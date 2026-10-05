<?php
/**
 * Shared Navbar Component
 */
$isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
$isAdmin = $isLoggedIn && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
$base = (isset($inAdmin) && $inAdmin) ? '../' : './';
$adminBase = (isset($inAdmin) && $inAdmin) ? './' : 'admin/';
?>
<nav class="navbar">
    <div class="container nav-container">
        <a href="<?= $base ?>index.php" class="brand-logo">
            <span class="brand-icon">✓</span>
            <span>Campus<strong style="color:var(--primary);">Vote</strong></span>
        </a>

        <ul class="nav-links">
            <li><a href="<?= $base ?>index.php">Home</a></li>
            <li><a href="<?= $base ?>results.php">Live Results</a></li>

            <?php if ($isLoggedIn): ?>
                <?php if ($isAdmin): ?>
                    <li><a href="<?= $adminBase ?>index.php" class="badge badge-admin" style="text-decoration:none; padding: 0.35rem 0.75rem;">Admin Panel</a></li>
                    <li><a href="<?= $adminBase ?>elections.php">Manage Elections</a></li>
                    <li><a href="<?= $adminBase ?>candidates.php">Candidates</a></li>
                    <li><a href="<?= $adminBase ?>voters.php">Voters</a></li>
                <?php else: ?>
                    <li><a href="<?= $base ?>dashboard.php">My Ballots</a></li>
                <?php endif; ?>
                
                <li style="display:flex; align-items:center; gap:0.5rem; margin-left:0.5rem;">
                    <span style="font-size:0.85rem; color:var(--text-muted); font-weight:600;">
                        👤 <?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?>
                    </span>
                    <a href="<?= $base ?>logout.php" class="btn btn-outline btn-sm">Logout</a>
                </li>
            <?php else: ?>
                <li><a href="<?= $base ?>login.php" class="btn btn-outline btn-sm">Log In</a></li>
                <li><a href="<?= $base ?>register.php" class="btn btn-primary btn-sm">Register to Vote</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
