<?php
/**
 * CampusVote - Public Landing Page
 */
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = "Democratic, Secure & Transparent Campus Voting";

$db = getDBConnection();

// Fetch election counts and metrics
$totalVoters = $db->query("SELECT COUNT(*) FROM users WHERE role = 'voter'")->fetchColumn();
$activeElections = $db->query("SELECT COUNT(*) FROM elections WHERE status = 'active'")->fetchColumn();
$totalBallotsCast = $db->query("SELECT COUNT(*) FROM voter_ballots")->fetchColumn();

// Fetch active elections
$stmt = $db->query("SELECT * FROM elections WHERE status = 'active' ORDER BY end_date ASC");
$activeList = $stmt->fetchAll();

// Fetch upcoming elections
$stmtUpcoming = $db->query("SELECT * FROM elections WHERE status = 'upcoming' ORDER BY start_date ASC LIMIT 3");
$upcomingList = $stmtUpcoming->fetchAll();

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<main class="main-content">
    <div class="container">
        <!-- Hero Section -->
        <div style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; padding: 3.5rem 2.5rem; border-radius: 16px; margin-bottom: 2.5rem; box-shadow: var(--shadow-lg);">
            <div style="max-width: 720px;">
                <span class="badge badge-active" style="background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid #059669; margin-bottom: 1rem;">
                    ● E-VOTING SYSTEM ACTIVE
                </span>
                <h1 style="color: white; font-size: 2.75rem; margin-bottom: 1rem; font-weight: 800; letter-spacing: -0.02em;">
                    Empowering Every Voice With Secure &amp; Transparent Elections
                </h1>
                <p style="color: #cbd5e1; font-size: 1.15rem; margin-bottom: 2rem;">
                    A decentralized, tamper-proof electronic voting platform engineered for student union elections, class representatives, and university community polls.
                </p>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <?php if (isLoggedIn()): ?>
                        <a href="dashboard.php" class="btn btn-primary btn-lg">Go to My Voting Dashboard →</a>
                        <a href="results.php" class="btn btn-outline btn-lg" style="color: white; border-color: #475569; background: transparent;">View Live Results</a>
                    <?php else: ?>
                        <a href="register.php" class="btn btn-primary btn-lg">Register as Voter</a>
                        <a href="login.php" class="btn btn-outline btn-lg" style="color: white; border-color: #475569; background: transparent;">Sign In to Cast Vote</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Metrics Strip -->
        <div class="grid-3" style="margin-bottom: 3rem;">
            <div class="stat-box">
                <div class="stat-icon">👥</div>
                <div>
                    <div class="stat-number"><?= number_format($totalVoters) ?></div>
                    <div class="stat-title">Registered Eligible Voters</div>
                </div>
            </div>
            <div class="stat-box">
                <div class="stat-icon" style="background:#ecfdf5; color:#10b981;">🗳️</div>
                <div>
                    <div class="stat-number"><?= number_format($totalBallotsCast) ?></div>
                    <div class="stat-title">Verified Ballots Cast</div>
                </div>
            </div>
            <div class="stat-box">
                <div class="stat-icon" style="background:#fffbeb; color:#f59e0b;">🏛️</div>
                <div>
                    <div class="stat-number"><?= number_format($activeElections) ?></div>
                    <div class="stat-title">Active Live Elections</div>
                </div>
            </div>
        </div>

        <!-- Active Elections Section -->
        <div style="margin-bottom: 3rem;">
            <div class="card-header" style="border:none; padding-bottom:0;">
                <div>
                    <h2>Active Elections Open for Voting</h2>
                    <p>Select an ongoing election below to review candidate manifestos and cast your ballot.</p>
                </div>
            </div>

            <?php if (empty($activeList)): ?>
                <div class="card" style="text-align: center; padding: 3rem;">
                    <p style="font-size: 1.1rem; color: var(--text-muted); margin-bottom: 1rem;">There are currently no active polls or elections open right now.</p>
                    <a href="results.php" class="btn btn-outline">Check Past Election Results</a>
                </div>
            <?php else: ?>
                <div class="grid-2">
                    <?php foreach ($activeList as $election): ?>
                        <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
                            <div>
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
                                    <span class="badge badge-active">● Active Now</span>
                                    <span style="font-size: 0.8rem; color: var(--text-muted);">
                                        Ends: <?= date('M d, Y H:i', strtotime($election['end_date'])) ?>
                                    </span>
                                </div>
                                <h3 style="margin-bottom: 0.75rem;"><?= htmlspecialchars($election['title']) ?></h3>
                                <p style="font-size: 0.95rem; margin-bottom: 1.5rem;"><?= htmlspecialchars($election['description']) ?></p>
                            </div>
                            <div style="display: flex; gap: 0.75rem; border-top: 1px solid var(--border); padding-top: 1rem;">
                                <a href="vote.php?election_id=<?= $election['id'] ?>" class="btn btn-primary" style="flex:1;">Cast Ballot</a>
                                <a href="results.php?election_id=<?= $election['id'] ?>" class="btn btn-outline">Live Tally</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- System Architecture & Security Highlights -->
        <div class="card" style="background: #f8fafc; border: 1px solid #cbd5e1; margin-bottom: 3rem;">
            <h3 style="margin-bottom: 1.5rem; text-align: center;">Core Security &amp; Transparency Architecture</h3>
            <div class="grid-3">
                <div style="background: white; padding: 1.5rem; border-radius: 12px; border: 1px solid var(--border);">
                    <div style="font-size: 2rem; margin-bottom: 0.5rem;">🔒</div>
                    <h4>Secret Ballot Anonymity</h4>
                    <p style="font-size: 0.9rem;">Voter identities are separated from candidate selections using one-way SHA-256 cryptographic hashes, guaranteeing complete privacy.</p>
                </div>
                <div style="background: white; padding: 1.5rem; border-radius: 12px; border: 1px solid var(--border);">
                    <div style="font-size: 2rem; margin-bottom: 0.5rem;">📜</div>
                    <h4>Verifiable Audit Receipts</h4>
                    <p style="font-size: 0.9rem;">Every voter receives an authentic cryptographic receipt hash upon submission to independently audit that their vote was recorded without exposing their choice.</p>
                </div>
                <div style="background: white; padding: 1.5rem; border-radius: 12px; border: 1px solid var(--border);">
                    <div style="font-size: 2rem; margin-bottom: 0.5rem;">🛡️</div>
                    <h4>Strict Single-Vote Integrity</h4>
                    <p style="font-size: 0.9rem;">Database-level unique constraints and prepared statements prevent SQL injection and guarantee each registered student votes at most once per position.</p>
                </div>
            </div>
        </div>

    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
