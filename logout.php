<?php
/**
 * CampusVote - User Logout
 */
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn()) {
    logAudit('LOGOUT', "User {$_SESSION['voter_id']} logged out.");
}

// Clear all session variables
$_SESSION = [];

// Destroy session cookie if exists
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy session
session_destroy();

// Redirect to login with notice
session_start();
$_SESSION['flash_success'] = "You have been securely signed out.";
header("Location: login.php");
exit;
