<?php
/**
 * Authentication and Security Helper Functions
 */

require_once __DIR__ . '/../config/db.php';

/**
 * Check if a user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if the logged in user is an Admin
 */
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Enforce voter login
 */
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['flash_error'] = "Please log in to access this page.";
        header("Location: login.php");
        exit;
    }
}

/**
 * Enforce admin login
 */
function requireAdmin() {
    if (!isAdmin()) {
        $_SESSION['flash_error'] = "Unauthorized access. Administrator privileges required.";
        header("Location: ../login.php");
        exit;
    }
}

/**
 * Get current logged in user data
 */
function getCurrentUser() {
    if (!isLoggedIn()) return null;
    $db = getDBConnection();
    $stmt = $db->prepare("SELECT id, voter_id, full_name, email, role, status, created_at FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

/**
 * Generate CSRF Token
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF Token
 */
function verifyCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], (string)$token)) {
        die("Security validation failed (Invalid CSRF Token). Please refresh the page and try again.");
    }
    return true;
}

/**
 * Record an audit log entry
 */
function logAudit($action, $details = '') {
    try {
        $db = getDBConnection();
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $stmt = $db->prepare("INSERT INTO audit_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
        $stmt->execute([$userId, $action, $details, $ip]);
    } catch (Exception $e) {
        // Silently continue if audit logging encounters an issue
    }
}

/**
 * Check if a user has already cast a ballot in an election
 */
function hasUserVoted($userId, $electionId) {
    $db = getDBConnection();
    $stmt = $db->prepare("SELECT id, receipt_token, cast_at FROM voter_ballots WHERE user_id = ? AND election_id = ?");
    $stmt->execute([$userId, $electionId]);
    return $stmt->fetch();
}

/**
 * Generate voter hash for ballot anonymity
 */
function generateVoterHash($voterId, $electionId) {
    return hash('sha256', $voterId . '_' . $electionId . '_' . VOTE_SALT);
}
