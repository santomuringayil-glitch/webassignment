<?php
/**
 * CampusVote - Election Management
 */
$inAdmin = true;
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdmin();

$db = getDBConnection();
$message = '';
$error = '';
$editElection = null;

// Handle Delete
if (isset($_GET['delete_id'])) {
    $deleteId = (int)$_GET['delete_id'];
    $stmtDel = $db->prepare("DELETE FROM elections WHERE id = ?");
    $stmtDel->execute([$deleteId]);
    logAudit('ELECTION_DELETE', "Election ID $deleteId deleted by admin.");
    header("Location: elections.php?msg=deleted");
    exit;
}

// Handle Form Submission (Create or Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf_token'] ?? '';
    verifyCSRFToken($csrf);

    $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $startDate = $_POST['start_date'] ?? '';
    $endDate = $_POST['end_date'] ?? '';
    $status = $_POST['status'] ?? 'upcoming';

    if (empty($title) || empty($startDate) || empty($endDate)) {
        $error = "Title, start date, and end date are required.";
    } else {
        if ($id) {
            // Update
            $stmt = $db->prepare("UPDATE elections SET title = ?, description = ?, start_date = ?, end_date = ?, status = ? WHERE id = ?");
            $stmt->execute([$title, $description, $startDate, $endDate, $status, $id]);
            logAudit('ELECTION_UPDATE', "Election ID $id updated.");
            $message = "Election updated successfully.";
        } else {
            // Insert
            $stmt = $db->prepare("INSERT INTO elections (title, description, start_date, end_date, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$title, $description, $startDate, $endDate, $status]);
            $newId = $db->lastInsertId();
            logAudit('ELECTION_CREATE', "New election created: '$title' (ID: $newId)");
            $message = "New election created successfully.";
        }
    }
}

// Check for Edit
if (isset($_GET['edit_id'])) {
    $editId = (int)$_GET['edit_id'];
    $stmtE = $db->prepare("SELECT * FROM elections WHERE id = ?");
    $stmtE->execute([$editId]);
    $editElection = $stmtE->fetch();
}

// Fetch all elections
$elections = $db->query("
    SELECT e.*, 
    (SELECT COUNT(*) FROM positions p WHERE p.election_id = e.id) as pos_count,
    (SELECT COUNT(*) FROM voter_ballots vb WHERE vb.election_id = e.id) as ballot_count 
    FROM elections e 
    ORDER BY e.created_at DESC
")->fetchAll();

$pageTitle = "Manage Elections";
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main class="main-content">
    <div class="container">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem;">
            <div>
                <h2>Elections &amp; Polls Configuration</h2>
                <p style="margin-bottom:0;">Configure scheduled campus elections, voting windows, and status flags.</p>
            </div>
            <a href="index.php" class="btn btn-outline">&larr; Back to Dashboard</a>
        </div>

        <?php if (!empty($message) || (isset($_GET['msg']) && $_GET['msg'] === 'deleted')): ?>
            <div class="alert alert-success"><?= !empty($message) ? htmlspecialchars($message) : 'Election deleted successfully.' ?></div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Create / Edit Form Card -->
        <div class="card" style="margin-bottom: 2.5rem;">
            <h3><?= $editElection ? 'Edit Election: ' . htmlspecialchars($editElection['title']) : 'Create New Campus Election' ?></h3>
            <form action="elections.php" method="POST" style="margin-top:1.25rem;">
                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                <?php if ($editElection): ?>
                    <input type="hidden" name="id" value="<?= $editElection['id'] ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label class="form-label" for="title">Election Title</label>
                    <input type="text" id="title" name="title" class="form-control" required placeholder="e.g. 2026 Student Union Executive Committee Election" value="<?= htmlspecialchars($editElection['title'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label class="form-label" for="description">Description &amp; Scope</label>
                    <textarea id="description" name="description" class="form-control" rows="3" placeholder="Provide background information, rules, and eligibility context..."><?= htmlspecialchars($editElection['description'] ?? '') ?></textarea>
                </div>

                <div class="grid-3">
                    <div class="form-group">
                        <label class="form-label" for="start_date">Start Date &amp; Time</label>
                        <input type="datetime-local" id="start_date" name="start_date" class="form-control" required value="<?= $editElection ? date('Y-m-d\TH:i', strtotime($editElection['start_date'])) : date('Y-m-d\TH:i') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="end_date">End Date &amp; Time</label>
                        <input type="datetime-local" id="end_date" name="end_date" class="form-control" required value="<?= $editElection ? date('Y-m-d\TH:i', strtotime($editElection['end_date'])) : date('Y-m-d\TH:i', strtotime('+7 days')) ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="status">Election Status</label>
                        <select id="status" name="status" class="form-control">
                            <option value="upcoming" <?= ($editElection && $editElection['status'] === 'upcoming') ? 'selected' : '' ?>>Upcoming (Scheduled)</option>
                            <option value="active" <?= ($editElection && $editElection['status'] === 'active') ? 'selected' : '' ?>>Active (Accepting Votes)</option>
                            <option value="completed" <?= ($editElection && $editElection['status'] === 'completed') ? 'selected' : '' ?>>Completed (Closed)</option>
                        </select>
                    </div>
                </div>

                <div style="display:flex; gap:0.75rem;">
                    <button type="submit" class="btn btn-primary"><?= $editElection ? 'Save Changes' : 'Create Election' ?></button>
                    <?php if ($editElection): ?>
                        <a href="elections.php" class="btn btn-outline">Cancel Edit</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Elections Table -->
        <div class="card">
            <div class="card-header">
                <h3>All Configured Elections</h3>
                <span class="badge" style="background:#f1f5f9; color:var(--secondary);"><?= count($elections) ?> Total</span>
            </div>
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Positions</th>
                            <th>Ballots</th>
                            <th>Duration Window</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($elections as $el): ?>
                            <tr>
                                <td>#<?= $el['id'] ?></td>
                                <td><strong><?= htmlspecialchars($el['title']) ?></strong></td>
                                <td>
                                    <?php if ($el['status'] === 'active'): ?>
                                        <span class="badge badge-active">Active</span>
                                    <?php elseif ($el['status'] === 'upcoming'): ?>
                                        <span class="badge badge-upcoming">Upcoming</span>
                                    <?php else: ?>
                                        <span class="badge badge-completed">Completed</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($el['pos_count'] == 0): ?>
                                        <span class="badge" style="background:#fee2e2; color:#b91c1c; border:1px solid #fca5a5;">0 offices (Needs setup)</span>
                                    <?php else: ?>
                                        <span class="badge" style="background:#f1f5f9; color:var(--secondary);"><?= $el['pos_count'] ?> offices</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $el['ballot_count'] ?> votes</td>
                                <td style="font-size:0.85rem;">
                                    <?= date('M d, y', strtotime($el['start_date'])) ?> to <?= date('M d, y', strtotime($el['end_date'])) ?>
                                </td>
                                <td style="display:flex; gap:0.4rem; flex-wrap:wrap;">
                                    <a href="candidates.php?election_id=<?= $el['id'] ?>" class="btn btn-primary btn-sm">+ Candidates</a>
                                    <a href="elections.php?edit_id=<?= $el['id'] ?>" class="btn btn-outline btn-sm">Edit</a>
                                    <a href="elections.php?delete_id=<?= $el['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Warning: Deleting this election will remove associated positions, candidates, and cast votes. Continue?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
