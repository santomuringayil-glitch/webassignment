<?php
/**
 * Shared Footer Component
 */
$basePath = (isset($inAdmin) && $inAdmin) ? '../' : './';
?>
    <footer class="footer">
        <div class="container">
            <p style="margin-bottom:0.4rem;">
                <strong>CampusVote</strong> — Transparent, Verifiable & Secure Online Voting System.
            </p>
            <p style="font-size:0.8rem; color:#94a3b8; margin-bottom:0;">
                Designed with PHP &amp; MySQL • SHA-256 Secret Ballot Cryptographic Receipts • &copy; <?= date('Y') ?> Student Election Commission.
            </p>
        </div>
    </footer>

    <!-- Client-side Scripts -->
    <script src="<?= $basePath ?>assets/js/script.js"></script>
</body>
</html>
