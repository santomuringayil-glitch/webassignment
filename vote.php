<?php
/**
 * CampusVote - Official Secret Ballot Voting Engine
 */
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

requireLogin();

$user = getCurrentUser();
$db = getDBConnection();

$electionId = filter_input(INPUT_GET, 'election_id', FILTER_VALIDATE_INT) ?? filter_input(INPUT_POST, 'election_id', FILTER_VALIDATE_INT);

if (!$electionId) {
    $_SESSION['flash_error'] = "Invalid election specified.";
    header("Location: dashboard.php");
    exit;
}

// Fetch election details
$stmtElection = $db->prepare("SELECT * FROM elections WHERE id = ?");
$stmtElection->execute([$electionId]);
$election = $stmtElection->fetch();

if (!$election) {
    $_SESSION['flash_error'] = "Election not found.";
    header("Location: dashboard.php");
    exit;
}

if ($election['status'] !== 'active') {
    $_SESSION['flash_error'] = "This election is currently not open for voting.";
    header("Location: dashboard.php");
    exit;
}

// Check if voter already cast ballot
$ballot = hasUserVoted($user['id'], $electionId);
if ($ballot) {
    $_SESSION['flash_error'] = "You have already cast your ballot for this election.";
    header("Location: receipt.php?election_id=" . $electionId);
    exit;
}

// Fetch all positions for this election
$stmtPositions = $db->prepare("SELECT * FROM positions WHERE election_id = ? ORDER BY priority ASC, id ASC");
$stmtPositions->execute([$electionId]);
$positions = $stmtPositions->fetchAll();

// Fetch candidates per position
$positionsWithCandidates = [];
foreach ($positions as $pos) {
    $stmtC = $db->prepare("SELECT * FROM candidates WHERE position_id = ? ORDER BY id ASC");
    $stmtC->execute([$pos['id']]);
    $pos['candidates'] = $stmtC->fetchAll();
    $positionsWithCandidates[] = $pos;
}

// Process Ballot Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    verifyCSRFToken($csrfToken);

    // Double check again inside request
    if (hasUserVoted($user['id'], $electionId)) {
        $_SESSION['flash_error'] = "Duplicate vote detected. You cannot vote twice.";
        header("Location: receipt.php?election_id=" . $electionId);
        exit;
    }

    $selections = $_POST['votes'] ?? []; // Array mapping position_id => candidate_id

    try {
        $db->beginTransaction();

        $voterHash = generateVoterHash($user['voter_id'], $electionId);

        // Record votes for selected positions
        $stmtInsertVote = $db->prepare("
            INSERT INTO votes (election_id, position_id, candidate_id, voter_hash) 
            VALUES (?, ?, ?, ?)
        ");

        $stmtVerifyCandidate = $db->prepare("
            SELECT id FROM candidates WHERE id = ? AND position_id = ?
        ");

        foreach ($positionsWithCandidates as $pos) {
            $posId = $pos['id'];
            if (isset($selections[$posId]) && !empty($selections[$posId])) {
                $candidateId = (int)$selections[$posId];
                
                // Verify candidate belongs to position
                $stmtVerifyCandidate->execute([$candidateId, $posId]);
                if ($stmtVerifyCandidate->fetch()) {
                    $stmtInsertVote->execute([$electionId, $posId, $candidateId, $voterHash]);
                }
            }
        }

        // Generate verifiable receipt token (SHA-256)
        $receiptToken = hash('sha256', $user['id'] . '_' . $electionId . '_' . microtime(true) . '_' . bin2hex(random_bytes(16)));

        // Record ballot completion
        $stmtInsertBallot = $db->prepare("
            INSERT INTO voter_ballots (user_id, election_id, receipt_token) 
            VALUES (?, ?, ?)
        ");
        $stmtInsertBallot->execute([$user['id'], $electionId, $receiptToken]);

        // Audit Log
        logAudit('BALLOT_CAST', "Voter ID {$user['voter_id']} successfully cast ballot in Election #{$electionId}");

        $db->commit();

        $_SESSION['flash_success'] = "Congratulations! Your ballot has been cryptographically sealed and recorded.";
        header("Location: receipt.php?election_id=" . $electionId);
        exit;

    } catch (Exception $e) {
        $db->rollBack();
        $error = "Voting failed: " . $e->getMessage();
    }
}

$pageTitle = "Official Ballot - " . $election['title'];
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<main class="main-content">
    <div class="container" style="max-width: 900px;">
        <!-- Election Header -->
        <div class="card" style="border-left: 5px solid var(--primary); margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                <span class="badge badge-active">Official Digital Ballot</span>
                <span style="font-size: 0.85rem; color: var(--text-muted);">
                    Closes: <?= date('M d, Y h:i A', strtotime($election['end_date'])) ?>
                </span>
            </div>
            <h1 style="font-size: 1.85rem; margin-bottom: 0.5rem;"><?= htmlspecialchars($election['title']) ?></h1>
            <p style="margin-bottom: 0.75rem;"><?= htmlspecialchars($election['description']) ?></p>
            <div style="background: var(--light); padding: 0.75rem 1rem; border-radius: 8px; font-size: 0.85rem; color: var(--secondary);">
                ℹ️ <strong>Instructions:</strong> Select your preferred candidate for each office below. Your choices remain strictly confidential and protected by one-way voter hashing.
            </div>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Ballot Form -->
        <form id="ballotForm" action="vote.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            <input type="hidden" name="election_id" value="<?= $election['id'] ?>">

            <?php foreach ($positionsWithCandidates as $index => $pos): ?>
                <div class="position-card">
                    <div class="position-title">
                        <div>
                            <span style="font-size: 0.8rem; font-weight: 700; color: var(--primary); text-transform: uppercase;">
                                Office <?= $index + 1 ?> of <?= count($positionsWithCandidates) ?>
                            </span>
                            <h3 style="margin-top: 0.2rem;"><?= htmlspecialchars($pos['title']) ?></h3>
                        </div>
                        <span class="badge" style="background:#f1f5f9; color:var(--secondary);">
                            Vote for <?= $pos['max_votes'] ?>
                        </span>
                    </div>

                    <?php if (empty($pos['candidates'])): ?>
                        <p style="font-style: italic; color: var(--text-muted);">No candidates nominated for this position.</p>
                    <?php else: ?>
                        <div class="candidate-options">
                            <?php foreach ($pos['candidates'] as $candidate): ?>
                                <label class="candidate-label">
                                    <input 
                                        type="radio" 
                                        name="votes[<?= $pos['id'] ?>]" 
                                        value="<?= $candidate['id'] ?>" 
                                        class="candidate-radio"
                                    >
                                    <div class="candidate-card-inner">
                                        <div class="candidate-avatar">
                                            <?= strtoupper(substr($candidate['name'], 0, 1)) ?>
                                        </div>
                                        <div class="candidate-name"><?= htmlspecialchars($candidate['name']) ?></div>
                                        <div class="candidate-party"><?= htmlspecialchars($candidate['party']) ?></div>
                                        <div class="candidate-manifesto">
                                            "<?= htmlspecialchars($candidate['manifesto']) ?>"
                                        </div>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <!-- Ballot Submission Action Strip -->
            <div class="card" style="text-align: center; padding: 2rem; background: #fafafa;">
                <h3 style="margin-bottom: 0.5rem;">Ready to Cast Your Ballot?</h3>
                <p style="margin-bottom: 1.5rem;">Verify your selections above carefully before confirming submission.</p>
                <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                    <a href="dashboard.php" class="btn btn-outline btn-lg">Save &amp; Return Later</a>
                    <button type="submit" class="btn btn-primary btn-lg" style="background: #10b981; border-color: #059669;">
                        Submit &amp; Seal My Vote 🗳️
                    </button>
                </div>
            </div>
        </form>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
