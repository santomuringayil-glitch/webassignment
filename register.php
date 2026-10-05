<?php
/**
 * CampusVote - Student Voter Registration
 */
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = "Voter Registration";
$error = '';

if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $voterId   = strtoupper(trim($_POST['voter_id'] ?? ''));
    $fullName  = trim($_POST['full_name'] ?? '');
    $email     = strtolower(trim($_POST['email'] ?? ''));
    $password  = $_POST['password'] ?? '';
    $passConfirm = $_POST['password_confirm'] ?? '';
    $csrfToken = $_POST['csrf_token'] ?? '';

    if (verifyCSRFToken($csrfToken)) {
        if (empty($voterId) || empty($fullName) || empty($email) || empty($password)) {
            $error = "All fields are required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Please provide a valid institutional email address.";
        } elseif (strlen($password) < 6) {
            $error = "Password must be at least 6 characters long.";
        } elseif ($password !== $passConfirm) {
            $error = "Passwords do not match.";
        } else {
            $db = getDBConnection();

            // Check if voter_id or email already exists
            $checkStmt = $db->prepare("SELECT id, email, voter_id FROM users WHERE voter_id = ? OR email = ?");
            $checkStmt->execute([$voterId, $email]);
            $existing = $checkStmt->fetch();

            if ($existing) {
                if ($existing['voter_id'] === $voterId) {
                    $error = "A voter with this Student ID ($voterId) is already registered.";
                } else {
                    $error = "This email ($email) is already associated with an account.";
                }
            } else {
                // Hash password securely
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

                $insertStmt = $db->prepare("INSERT INTO users (voter_id, full_name, email, password, role, status) VALUES (?, ?, ?, ?, 'voter', 'active')");
                $insertStmt->execute([$voterId, $fullName, $email, $hashedPassword]);

                logAudit('VOTER_REGISTER', "New voter registered: $voterId ($email)");

                $_SESSION['flash_success'] = "Registration successful! You may now sign in to view active elections and vote.";
                header("Location: login.php");
                exit;
            }
        }
    }
}

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<main class="main-content">
    <div class="container">
        <div class="auth-box" style="max-width: 520px;">
            <div class="auth-header">
                <span class="brand-icon" style="margin: 0 auto 1rem; width:48px; height:48px; font-size:1.5rem;">📝</span>
                <h2>Voter Registration</h2>
                <p>Register with your university credentials to participate in transparent elections.</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form action="register.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

                <div class="form-group">
                    <label class="form-label" for="voter_id">Student / Voter ID</label>
                    <input type="text" id="voter_id" name="voter_id" class="form-control" required placeholder="e.g. STU202688" value="<?= htmlspecialchars($_POST['voter_id'] ?? '') ?>">
                    <small style="color:var(--text-muted); font-size:0.8rem;">Must match your university matriculation number.</small>
                </div>

                <div class="form-group">
                    <label class="form-label" for="full_name">Full Legal Name</label>
                    <input type="text" id="full_name" name="full_name" class="form-control" required placeholder="e.g. Jane Doe" value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Campus Email</label>
                    <input type="email" id="email" name="email" class="form-control" required placeholder="e.g. jane.doe@campus.edu" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>

                <div class="grid-2" style="gap:1rem; margin-bottom:1.25rem;">
                    <div>
                        <label class="form-label" for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control" required placeholder="Min 6 characters">
                    </div>
                    <div>
                        <label class="form-label" for="password_confirm">Confirm Password</label>
                        <input type="password" id="password_confirm" name="password_confirm" class="form-control" required placeholder="Repeat password">
                    </div>
                </div>

                <div style="margin-top: 1.5rem;">
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Create Voter Account</button>
                </div>
            </form>

            <div style="margin-top: 1.75rem; text-align: center; border-top: 1px solid var(--border); padding-top: 1.25rem;">
                <p style="font-size: 0.9rem; margin-bottom: 0;">Already registered? <a href="login.php"><strong>Sign In here</strong></a></p>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
