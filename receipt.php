<?php
/**
 * CampusVote - Official Cryptographic Ballot Receipt
 */
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

requireLogin();

$user = getCurrentUser();
$db = getDBConnection();

$electionId = filter_input(INPUT_GET, 'election_id', FILTER_VALIDATE_INT);

if (!$electionId) {
    header("Location: dashboard.php");
    exit;
}

// Fetch ballot record
$stmtBallot = $db->prepare("
    SELECT vb.*, e.title as election_title, e.start_date, e.end_date 
    FROM voter_ballots vb 
    JOIN elections e ON vb.election_id = e.id 
    WHERE vb.user_id = ? AND vb.election_id = ?
");
$stmtBallot->execute([$user['id'], $electionId]);
$ballot = $stmtBallot->fetch();

if (!$ballot) {
    $_SESSION['flash_error'] = "No ballot record found for this election.";
    header("Location: dashboard.php");
    exit;
}

$pageTitle = "Official Voting Receipt";
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<main class="main-content">
    <div class="container">
        <div class="receipt-card">
            <div style="font-size: 3rem; margin-bottom: 0.5rem;">🎉</div>
            <span class="badge badge-active" style="margin-bottom: 1rem;">Ballot Cryptographically Sealed</span>
            <h2 style="margin-bottom: 0.5rem;">Official Voting Verification Slip</h2>
            <p>Your electronic ballot has been recorded and authenticated in the election database.</p>

            <div style="text-align: left; background: #f8fafc; border: 1px solid var(--border); border-radius: 10px; padding: 1.25rem; margin: 1.5rem 0;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span style="color:var(--text-muted); font-size: 0.9rem;">Election:</span>
                    <strong><?= htmlspecialchars($ballot['election_title']) ?></strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span style="color:var(--text-muted); font-size: 0.9rem;">Voter Matriculation:</span>
                    <strong><?= htmlspecialchars($user['voter_id']) ?></strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span style="color:var(--text-muted); font-size: 0.9rem;">Date &amp; Time Recorded:</span>
                    <strong><?= date('F j, Y - g:i:s A T', strtotime($ballot['cast_at'])) ?></strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color:var(--text-muted); font-size: 0.9rem;">Integrity Protocol:</span>
                    <strong style="color:var(--primary);">SHA-256 Secret Ballot Proof</strong>
                </div>
            </div>

            <div style="text-align: left;">
                <label style="font-size: 0.85rem; font-weight: 700; color: var(--secondary); text-transform: uppercase;">
                    Verifiable Receipt Token
                </label>
                <div class="receipt-token">
                    <?= htmlspecialchars($ballot['receipt_token']) ?>
                </div>
                <small style="color: var(--text-muted); font-size: 0.8rem; display: block; margin-bottom: 1.5rem;">
                    * Notice: This cryptographic receipt serves as your mathematical proof of participation. Your specific candidate selections are decoupled from your identity to ensure complete secret ballot anonymity.
                </small>
            </div>

            <div style="display: flex; gap: 0.75rem; justify-content: center; flex-wrap: wrap;">
                <button onclick="window.print()" class="btn btn-outline">🖨️ Print / Save Receipt</button>
                <a href="dashboard.php" class="btn btn-primary">Return to Dashboard</a>
                <a href="results.php?election_id=<?= $electionId ?>" class="btn btn-outline">View Election Tally</a>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
