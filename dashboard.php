<?php
/**
 * CampusVote - Student Voter Dashboard
 */
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

requireLogin();

$user = getCurrentUser();
$db = getDBConnection();

$pageTitle = "Voter Dashboard";

// Fetch active elections
$stmtActive = $db->query("SELECT * FROM elections WHERE status = 'active' ORDER BY end_date ASC");
$activeElections = $stmtActive->fetchAll();

// Fetch completed/past elections
$stmtPast = $db->query("SELECT * FROM elections WHERE status = 'completed' ORDER BY end_date DESC LIMIT 5");
$pastElections = $stmtPast->fetchAll();

// Fetch voter's submitted receipts
$stmtReceipts = $db->prepare("
    SELECT vb.*, e.title as election_title 
    FROM voter_ballots vb 
    JOIN elections e ON vb.election_id = e.id 
    WHERE vb.user_id = ? 
    ORDER BY vb.cast_at DESC
");
$stmtReceipts->execute([$user['id']]);
$userReceipts = $stmtReceipts->fetchAll();

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<main class="main-content">
    <div class="container">
        <!-- Voter Profile Banner -->
        <div class="card" style="background: linear-gradient(to right, #ffffff, #f1f5f9); border-left: 5px solid var(--primary); margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <span style="font-size: 0.85rem; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 0.05em;">
                        Authenticated Student Voter Portal
                    </span>
                    <h2 style="margin-top: 0.25rem;">Welcome, <?= htmlspecialchars($user['full_name']) ?></h2>
                    <p style="margin-bottom: 0;">
                        Student ID: <strong><?= htmlspecialchars($user['voter_id']) ?></strong> &bull; 
                        Email: <strong><?= htmlspecialchars($user['email']) ?></strong> &bull;
                        Status: <span class="badge badge-active" style="display:inline-flex;">Verified Active</span>
                    </p>
                </div>
                <div>
                    <a href="results.php" class="btn btn-outline">View Global Results</a>
                </div>
            </div>
        </div>

        <?php if (!empty($_SESSION['flash_success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_SESSION['flash_success']) ?></div>
            <?php unset($_SESSION['flash_success']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['flash_error'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['flash_error']) ?></div>
            <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>

        <!-- Active Elections List -->
        <div style="margin-bottom: 3rem;">
            <h3 style="margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                <span>🗳️</span> Active Elections Available
            </h3>

            <?php if (empty($activeElections)): ?>
                <div class="card" style="text-align: center; padding: 2.5rem;">
                    <p>There are currently no open elections. Please check back when new polls are scheduled.</p>
                </div>
            <?php else: ?>
                <div class="grid-2">
                    <?php foreach ($activeElections as $election): 
                        $hasVoted = hasUserVoted($user['id'], $election['id']);
                    ?>
                        <div class="card" style="display:flex; flex-direction:column; justify-content:space-between; border-top: 4px solid <?= $hasVoted ? 'var(--success)' : 'var(--primary)' ?>;">
                            <div>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                                    <?php if ($hasVoted): ?>
                                        <span class="badge badge-active" style="background:#ecfdf5; color:#059669; border-color:#6ee7b7;">✓ Ballot Cast</span>
                                    <?php else: ?>
                                        <span class="badge badge-upcoming" style="background:#eff6ff; color:#2563eb; border-color:#93c5fd;">● Pending Vote</span>
                                    <?php endif; ?>

                                    <span style="font-size: 0.8rem; color: var(--text-muted);">
                                        Deadline: <?= date('M d, Y h:i A', strtotime($election['end_date'])) ?>
                                    </span>
                                </div>
                                <h3 style="margin-bottom: 0.5rem;"><?= htmlspecialchars($election['title']) ?></h3>
                                <p style="font-size: 0.95rem; margin-bottom: 1.5rem;"><?= htmlspecialchars($election['description']) ?></p>
                            </div>

                            <div style="border-top: 1px solid var(--border); padding-top: 1rem; display: flex; gap: 0.75rem;">
                                <?php if ($hasVoted): ?>
                                    <a href="receipt.php?election_id=<?= $election['id'] ?>" class="btn btn-success" style="flex:1;">
                                        View Ballot Receipt
                                    </a>
                                    <a href="results.php?election_id=<?= $election['id'] ?>" class="btn btn-outline">
                                        Live Results
                                    </a>
                                <?php else: ?>
                                    <a href="vote.php?election_id=<?= $election['id'] ?>" class="btn btn-primary" style="flex:1;">
                                        Proceed to Ballot →
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Verification History / Receipts -->
        <div>
            <h3 style="margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                <span>📜</span> My Verified Voting Receipts
            </h3>

            <?php if (empty($userReceipts)): ?>
                <div class="card" style="padding: 1.75rem; text-align: center; color: var(--text-muted);">
                    You have not cast any ballots yet.
                </div>
            <?php else: ?>
                <div class="card" style="padding: 0; overflow: hidden;">
                    <div class="table-responsive">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>Election</th>
                                    <th>Submission Timestamp</th>
                                    <th>Cryptographic Receipt Hash (SHA-256)</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($userReceipts as $rcpt): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($rcpt['election_title']) ?></strong></td>
                                        <td><?= date('M d, Y h:i:s A', strtotime($rcpt['cast_at'])) ?></td>
                                        <td><code style="background:#f1f5f9; padding:0.2rem 0.4rem; border-radius:4px; font-size:0.85rem;"><?= htmlspecialchars(substr($rcpt['receipt_token'], 0, 16)) ?>...<?= htmlspecialchars(substr($rcpt['receipt_token'], -8)) ?></code></td>
                                        <td>
                                            <a href="receipt.php?election_id=<?= $rcpt['election_id'] ?>" class="btn btn-outline btn-sm">Full Receipt</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
