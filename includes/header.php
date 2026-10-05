<?php
/**
 * Shared Header Component
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$pageTitle = $pageTitle ?? 'Online Voting System';
$basePath = (isset($inAdmin) && $inAdmin) ? '../' : './';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | CampusVote</title>
    <!-- Modern Styling -->
    <link rel="stylesheet" href="<?= $basePath ?>assets/css/style.css">
    <!-- Favicon (Inline SVG Data URI) -->
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🗳️</text></svg>">
</head>
<body>
