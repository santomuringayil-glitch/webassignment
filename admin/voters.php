<?php
/**
 * CampusVote - Voter Registry & Participation Roster
 */
$inAdmin = true;
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdmin();

$db = getDBConnection();
$message = '';

// Toggle status
if (isset($_GET['toggle_status'])) {
    $uid = (int)$_GET['toggle_status'];
    $stmtUser = $db->prepare("SELECT status, voter_id FROM users WHERE id = ? AND role != 'admin'");
    $stmtUser->execute([$uid]);
    $u = $stmtUser->fetch();
    if ($u) {
        $newStatus = ($u['status'] === 'active') ? 'inactive' : 'active';
        $stmtUp = $db->prepare("UPDATE users SET status = ? WHERE id = ?");
        $stmtUp->execute([$newStatus, $uid]);
        logAudit('VOTER_STATUS_CHANGE', "Voter {$u['voter_id']} status updated to $newStatus");
        $message = "Voter status updated to $newStatus.";
    }
}

// Fetch all registered voters
$voters = $db->query("
    SELECT u.*, 
    (SELECT COUNT(*) FROM voter_ballots vb WHERE vb.user_id = u.id) as ballots_cast 
    FROM users u 
    WHERE u.role = 'voter' 
    ORDER BY u.created_at DESC
")->fetchAll();

$pageTitle = "Voter Registry";
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main class="main-content">
    <div class="container">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem;">
            <div>
                <h2>Electorate Registry &amp; Voter Turnout</h2>
                <p style="margin-bottom:0;">Review registered student voters, matriculation numbers, and ballot participation.</p>
            </div>
            <a href="index.php" class="btn btn-outline">&larr; Back to Dashboard</a>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <div>
                    <h3>Voter Roll</h3>
                    <span style="font-size:0.85rem; color:var(--text-muted);">Real-time count: <?= count($voters) ?> registered voters</span>
                </div>
                <div style="max-width:300px; width:100%;">
                    <input type="text" id="tableSearch" class="form-control" placeholder="🔍 Search ID, Name, Email...">
                </div>
            </div>

            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Student / Voter ID</th>
                            <th>Full Name</th>
                            <th>Institutional Email</th>
                            <th>Ballots Cast</th>
                            <th>Status</th>
                            <th>Registered On</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($voters)): ?>
                            <tr><td colspan="7" style="text-align:center;">No student voters registered yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($voters as $v): ?>
                                <tr>
                                    <td><strong style="color:var(--primary);"><?= htmlspecialchars($v['voter_id']) ?></strong></td>
                                    <td><?= htmlspecialchars($v['full_name']) ?></td>
                                    <td><?= htmlspecialchars($v['email']) ?></td>
                                    <td>
                                        <?php if ($v['ballots_cast'] > 0): ?>
                                            <span class="badge badge-active"><?= $v['ballots_cast'] ?> cast</span>
                                        <?php else: ?>
                                            <span class="badge" style="background:#f1f5f9; color:var(--secondary);">0 cast</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($v['status'] === 'active'): ?>
                                            <span class="badge badge-active">Active</span>
                                        <?php else: ?>
                                            <span class="badge" style="background:#fee2e2; color:#b91c1c;">Suspended</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="font-size:0.85rem; color:var(--text-muted);"><?= date('M d, Y', strtotime($v['created_at'])) ?></td>
                                    <td>
                                        <a href="voters.php?toggle_status=<?= $v['id'] ?>" class="btn btn-outline btn-sm">
                                            <?= ($v['status'] === 'active') ? 'Suspend' : 'Activate' ?>
                                        </a>
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
