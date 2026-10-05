<?php
/**
 * CampusVote - Candidate Nominations & Positions Management
 */
$inAdmin = true;
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdmin();

$db = getDBConnection();
$message = '';
$error = '';

// Handle Candidate Deletion
if (isset($_GET['delete_candidate'])) {
    $cid = (int)$_GET['delete_candidate'];
    $stmtDel = $db->prepare("DELETE FROM candidates WHERE id = ?");
    $stmtDel->execute([$cid]);
    logAudit('CANDIDATE_DELETE', "Candidate ID $cid removed.");
    header("Location: candidates.php?msg=cand_deleted");
    exit;
}

// Handle Add New Position
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type']) && $_POST['action_type'] === 'add_position') {
    verifyCSRFToken($_POST['csrf_token'] ?? '');
    $electionId = (int)($_POST['election_id'] ?? 0);
    $posTitle = trim($_POST['position_title'] ?? '');

    if ($electionId && !empty($posTitle)) {
        $stmtP = $db->prepare("INSERT INTO positions (election_id, title) VALUES (?, ?)");
        $stmtP->execute([$electionId, $posTitle]);
        logAudit('POSITION_CREATE', "Added position '$posTitle' to election ID $electionId");
        $message = "Position '$posTitle' created successfully.";
    } else {
        $error = "Election and Position title are required.";
    }
}

// Handle Add Candidate
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type']) && $_POST['action_type'] === 'add_candidate') {
    verifyCSRFToken($_POST['csrf_token'] ?? '');
    $positionId = (int)($_POST['position_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $party = trim($_POST['party'] ?? 'Independent');
    $manifesto = trim($_POST['manifesto'] ?? '');

    if ($positionId && !empty($name)) {
        $stmtC = $db->prepare("INSERT INTO candidates (position_id, name, party, manifesto) VALUES (?, ?, ?, ?)");
        $stmtC->execute([$positionId, $name, $party, $manifesto]);
        logAudit('CANDIDATE_CREATE', "Added candidate '$name' for position ID $positionId");
        $message = "Candidate '$name' successfully nominated.";
    } else {
        $error = "Candidate name and position are required.";
    }
}

// Fetch all elections for dropdown
$elections = $db->query("SELECT id, title FROM elections ORDER BY id DESC")->fetchAll();

// Fetch all positions with their election titles
$positions = $db->query("
    SELECT p.*, e.title as election_title 
    FROM positions p 
    JOIN elections e ON p.election_id = e.id 
    ORDER BY e.id DESC, p.priority ASC, p.id ASC
")->fetchAll();

// Fetch all candidates
$candidates = $db->query("
    SELECT c.*, p.title as position_title, e.title as election_title,
    (SELECT COUNT(*) FROM votes v WHERE v.candidate_id = c.id) as vote_count
    FROM candidates c 
    JOIN positions p ON c.position_id = p.id 
    JOIN elections e ON p.election_id = e.id 
    ORDER BY e.id DESC, p.title ASC, c.name ASC
")->fetchAll();

$pageTitle = "Candidates & Positions";
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main class="main-content">
    <div class="container">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem;">
            <div>
                <h2>Positions &amp; Candidate Nominations</h2>
                <p style="margin-bottom:0;">Configure offices to be elected and register verified candidate profiles.</p>
            </div>
            <a href="index.php" class="btn btn-outline">&larr; Back to Dashboard</a>
        </div>

        <?php if (!empty($message) || (isset($_GET['msg']) && $_GET['msg'] === 'cand_deleted')): ?>
            <div class="alert alert-success"><?= !empty($message) ? htmlspecialchars($message) : 'Candidate removed successfully.' ?></div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="grid-2" style="margin-bottom: 2.5rem; align-items: flex-start;">
            <!-- Add New Position Card -->
            <div class="card">
                <h3>+ Add Election Office / Position</h3>
                <form action="candidates.php" method="POST" style="margin-top:1rem;">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                    <input type="hidden" name="action_type" value="add_position">

                    <div class="form-group">
                        <label class="form-label" for="election_id">Target Election</label>
                        <select name="election_id" id="election_id" class="form-control" required>
                            <option value="">-- Choose Election --</option>
                            <?php foreach ($elections as $el): ?>
                                <option value="<?= $el['id'] ?>"><?= htmlspecialchars($el['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="position_title">Position Title</label>
                        <input type="text" id="position_title" name="position_title" class="form-control" required placeholder="e.g. Student Council Treasurer">
                    </div>

                    <button type="submit" class="btn btn-outline" style="width:100%;">Create Position</button>
                </form>
            </div>

            <!-- Add Candidate Card -->
            <div class="card">
                <h3>+ Nominate Candidate</h3>
                <form action="candidates.php" method="POST" style="margin-top:1rem;">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                    <input type="hidden" name="action_type" value="add_candidate">

                    <div class="form-group">
                        <label class="form-label" for="position_id">Contested Office</label>
                        <select name="position_id" id="position_id" class="form-control" required>
                            <option value="">-- Select Position &amp; Election --</option>
                            <?php foreach ($positions as $p): ?>
                                <option value="<?= $p['id'] ?>">
                                    <?= htmlspecialchars($p['election_title']) ?> &rarr; <?= htmlspecialchars($p['title']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="grid-2" style="gap:1rem; margin-bottom:1rem;">
                        <div>
                            <label class="form-label" for="name">Candidate Name</label>
                            <input type="text" id="name" name="name" class="form-control" required placeholder="e.g. Sarah Jenkins">
                        </div>
                        <div>
                            <label class="form-label" for="party">Party / Slate</label>
                            <input type="text" id="party" name="party" class="form-control" placeholder="e.g. Campus Action">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="manifesto">Candidate Manifesto / Vision</label>
                        <textarea id="manifesto" name="manifesto" class="form-control" rows="2" placeholder="Brief statement of policy priorities..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width:100%;">Save Candidate</button>
                </form>
            </div>
        </div>

        <!-- Candidate Roster Table -->
        <div class="card">
            <div class="card-header">
                <h3>Candidate Roster</h3>
                <span class="badge" style="background:#f1f5f9; color:var(--secondary);"><?= count($candidates) ?> Nominated</span>
            </div>
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Candidate</th>
                            <th>Election</th>
                            <th>Contested Office</th>
                            <th>Party Affiliation</th>
                            <th>Votes Logged</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($candidates)): ?>
                            <tr><td colspan="6" style="text-align:center;">No candidates registered yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($candidates as $cand): ?>
                                <tr>
                                    <td>
                                        <div style="display:flex; align-items:center; gap:0.6rem;">
                                            <div class="candidate-avatar" style="width:36px; height:36px; font-size:1rem; margin-bottom:0;">
                                                <?= strtoupper(substr($cand['name'], 0, 1)) ?>
                                            </div>
                                            <strong><?= htmlspecialchars($cand['name']) ?></strong>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($cand['election_title']) ?></td>
                                    <td><span class="badge" style="background:#eff6ff; color:#2563eb;"><?= htmlspecialchars($cand['position_title']) ?></span></td>
                                    <td><?= htmlspecialchars($cand['party']) ?></td>
                                    <td><strong><?= number_format($cand['vote_count']) ?></strong></td>
                                    <td>
                                        <a href="candidates.php?delete_candidate=<?= $cand['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this candidate?')">Remove</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
