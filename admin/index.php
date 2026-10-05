<?php
/**
 * CampusVote - Administrator Control Center
 */
$inAdmin = true;
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdmin();

$db = getDBConnection();
$pageTitle = "Admin Control Center";

// Summary Metrics
$totalVoters = $db->query("SELECT COUNT(*) FROM users WHERE role = 'voter'")->fetchColumn();
$totalElections = $db->query("SELECT COUNT(*) FROM elections")->fetchColumn();
$totalPositions = $db->query("SELECT COUNT(*) FROM positions")->fetchColumn();
$totalCandidates = $db->query("SELECT COUNT(*) FROM candidates")->fetchColumn();
$totalVotesCast = $db->query("SELECT COUNT(*) FROM votes")->fetchColumn();
$totalBallots = $db->query("SELECT COUNT(*) FROM voter_ballots")->fetchColumn();

// Recent Audit Logs
$stmtAudit = $db->query("
    SELECT al.*, u.full_name, u.voter_id 
    FROM audit_logs al 
    LEFT JOIN users u ON al.user_id = u.id 
    ORDER BY al.created_at DESC LIMIT 10
");
$recentLogs = $stmtAudit->fetchAll();

// Active Elections
$stmtElections = $db->query("
    SELECT e.*, 
    (SELECT COUNT(*) FROM positions p WHERE p.election_id = e.id) as position_count,
    (SELECT COUNT(*) FROM voter_ballots vb WHERE vb.election_id = e.id) as ballot_count 
    FROM elections e 
    ORDER BY e.created_at DESC LIMIT 5
");
$recentElections = $stmtElections->fetchAll();

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main class="main-content">
    <div class="container">
        <!-- Admin Title Banner -->
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 2rem;">
            <div>
                <span class="badge badge-admin">Master Election Administrator</span>
                <h1 style="margin-top: 0.25rem;">Elections Oversight &amp; Audit Console</h1>
                <p style="margin-bottom: 0;">Monitor live voter participation, configure ballots, and review immutable audit trails.</p>
            </div>
            <div style="display: flex; gap: 0.75rem;">
                <a href="elections.php?action=create" class="btn btn-primary">+ Create Election</a>
                <a href="candidates.php?action=create" class="btn btn-outline">+ Add Candidate</a>
            </div>
        </div>

        <!-- Metric Stat Cards -->
        <div class="grid-4" style="margin-bottom: 2.5rem;">
            <div class="stat-box">
                <div class="stat-icon">👥</div>
                <div>
                    <div class="stat-number"><?= number_format($totalVoters) ?></div>
                    <div class="stat-title">Registered Voters</div>
                </div>
            </div>
            <div class="stat-box">
                <div class="stat-icon" style="background:#eff6ff; color:#2563eb;">🗳️</div>
                <div>
                    <div class="stat-number"><?= number_format($totalBallots) ?></div>
                    <div class="stat-title">Ballots Sealed</div>
                </div>
            </div>
            <div class="stat-box">
                <div class="stat-icon" style="background:#ecfdf5; color:#10b981;">🏛️</div>
                <div>
                    <div class="stat-number"><?= number_format($totalElections) ?></div>
                    <div class="stat-title">Total Elections</div>
                </div>
            </div>
            <div class="stat-box">
                <div class="stat-icon" style="background:#fffbeb; color:#f59e0b;">🎯</div>
                <div>
                    <div class="stat-number"><?= number_format($totalCandidates) ?></div>
                    <div class="stat-title">Nominated Candidates</div>
                </div>
            </div>
        </div>

        <!-- Elections Overview Table -->
        <div class="card" style="margin-bottom: 2.5rem;">
            <div class="card-header">
                <div>
                    <h3>Active &amp; Recent Elections</h3>
                    <p style="font-size:0.85rem; margin-bottom:0;">Live status and ballot volume tracking.</p>
                </div>
                <a href="elections.php" class="btn btn-outline btn-sm">Manage All Elections →</a>
            </div>
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Election Title</th>
                            <th>Status</th>
                            <th>Offices / Positions</th>
                            <th>Ballots Cast</th>
                            <th>Election Window</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentElections as $elec): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($elec['title']) ?></strong></td>
                                <td>
                                    <?php if ($elec['status'] === 'active'): ?>
                                        <span class="badge badge-active">Active</span>
                                    <?php elseif ($elec['status'] === 'upcoming'): ?>
                                        <span class="badge badge-upcoming">Upcoming</span>
                                    <?php else: ?>
                                        <span class="badge badge-completed">Completed</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $elec['position_count'] ?> positions</td>
                                <td><strong><?= number_format($elec['ballot_count']) ?></strong> ballots</td>
                                <td style="font-size:0.85rem;">
                                    <?= date('M d', strtotime($elec['start_date'])) ?> &rarr; <?= date('M d, Y', strtotime($elec['end_date'])) ?>
                                </td>
                                <td>
                                    <a href="../results.php?election_id=<?= $elec['id'] ?>" class="btn btn-outline btn-sm">Tally</a>
                                    <a href="elections.php?edit_id=<?= $elec['id'] ?>" class="btn btn-outline btn-sm">Configure</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Real-Time Audit Log Table -->
        <div class="card">
            <div class="card-header">
                <div>
                    <h3>Security Audit Log (Immutable Record)</h3>
                    <p style="font-size:0.85rem; margin-bottom:0;">Live stream of user registrations, logins, ballot sealing, and administrative actions.</p>
                </div>
                <span class="badge" style="background:#f1f5f9; color:var(--secondary);">Last 10 Events</span>
            </div>
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Timestamp</th>
                            <th>Action</th>
                            <th>Initiator</th>
                            <th>IP Address</th>
                            <th>Event Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentLogs as $log): ?>
                            <tr>
                                <td style="font-size:0.85rem; color:var(--text-muted);">
                                    <?= date('M d, H:i:s', strtotime($log['created_at'])) ?>
                                </td>
                                <td>
                                    <code style="font-weight:700; color:var(--primary);"><?= htmlspecialchars($log['action']) ?></code>
                                </td>
                                <td>
                                    <?= $log['voter_id'] ? htmlspecialchars($log['voter_id']) : 'System/Guest' ?>
                                </td>
                                <td><?= htmlspecialchars($log['ip_address']) ?></td>
                                <td style="font-size:0.9rem;"><?= htmlspecialchars($log['details']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
