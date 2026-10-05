<?php
/**
 * CampusVote - Election Results & Real-Time Analytics
 */
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

$db = getDBConnection();

// Fetch all elections for selector
$allElections = $db->query("SELECT id, title, status FROM elections ORDER BY id DESC")->fetchAll();

$selectedElectionId = filter_input(INPUT_GET, 'election_id', FILTER_VALIDATE_INT);
if (!$selectedElectionId && !empty($allElections)) {
    // Default to first active, or first available
    $selectedElectionId = $allElections[0]['id'];
    foreach ($allElections as $e) {
        if ($e['status'] === 'active') {
            $selectedElectionId = $e['id'];
            break;
        }
    }
}

// Fetch selected election
$election = null;
$positionsData = [];
$totalVotersCount = $db->query("SELECT COUNT(*) FROM users WHERE role = 'voter'")->fetchColumn();
$ballotsCastCount = 0;

if ($selectedElectionId) {
    $stmtE = $db->prepare("SELECT * FROM elections WHERE id = ?");
    $stmtE->execute([$selectedElectionId]);
    $election = $stmtE->fetch();

    if ($election) {
        // Total ballots cast in this election
        $stmtB = $db->prepare("SELECT COUNT(*) FROM voter_ballots WHERE election_id = ?");
        $stmtB->execute([$selectedElectionId]);
        $ballotsCastCount = $stmtB->fetchColumn();

        // Fetch positions
        $stmtP = $db->prepare("SELECT * FROM positions WHERE election_id = ? ORDER BY priority ASC, id ASC");
        $stmtP->execute([$selectedElectionId]);
        $positions = $stmtP->fetchAll();

        foreach ($positions as $pos) {
            // Count total votes cast for this position
            $stmtTot = $db->prepare("SELECT COUNT(*) FROM votes WHERE position_id = ?");
            $stmtTot->execute([$pos['id']]);
            $posTotalVotes = $stmtTot->fetchColumn();

            // Fetch candidates with vote counts
            $stmtC = $db->prepare("
                SELECT c.*, COUNT(v.id) as vote_count 
                FROM candidates c 
                LEFT JOIN votes v ON c.id = v.candidate_id 
                WHERE c.position_id = ? 
                GROUP BY c.id 
                ORDER BY vote_count DESC, c.name ASC
            ");
            $stmtC->execute([$pos['id']]);
            $candidates = $stmtC->fetchAll();

            $pos['total_votes'] = $posTotalVotes;
            $pos['candidates'] = $candidates;
            $positionsData[] = $pos;
        }
    }
}

$turnoutPercent = ($totalVotersCount > 0) ? round(($ballotsCastCount / $totalVotersCount) * 100, 1) : 0;

$pageTitle = "Election Results & Analytics";
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<main class="main-content">
    <div class="container">
        <!-- Header & Election Dropdown Filter -->
        <div style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1rem; margin-bottom: 2rem;">
            <div>
                <h2>Election Results &amp; Live Tally</h2>
                <p style="margin-bottom: 0;">Verified cryptographic vote tabulation and candidate analytics.</p>
            </div>
            <div>
                <form action="results.php" method="GET" style="display: flex; gap: 0.5rem; align-items: center;">
                    <label for="election_id" style="font-weight: 600; font-size: 0.9rem;">Select Election:</label>
                    <select name="election_id" id="election_id" class="form-control" style="width: auto;" onchange="this.form.submit()">
                        <?php foreach ($allElections as $el): ?>
                            <option value="<?= $el['id'] ?>" <?= ($el['id'] == $selectedElectionId) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($el['title']) ?> (<?= ucfirst($el['status']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
        </div>

        <?php if ($election): ?>
            <!-- Election Summary Stats -->
            <div class="grid-3" style="margin-bottom: 2.5rem;">
                <div class="stat-box">
                    <div class="stat-icon">🗳️</div>
                    <div>
                        <div class="stat-number"><?= number_format($ballotsCastCount) ?></div>
                        <div class="stat-title">Ballots Submitted</div>
                    </div>
                </div>
                <div class="stat-box">
                    <div class="stat-icon" style="background:#eff6ff; color:#2563eb;">📊</div>
                    <div>
                        <div class="stat-number"><?= $turnoutPercent ?>%</div>
                        <div class="stat-title">Voter Turnout Rate</div>
                    </div>
                </div>
                <div class="stat-box">
                    <div class="stat-icon" style="background:#ecfdf5; color:#10b981;">⚡</div>
                    <div>
                        <div class="stat-number" style="font-size:1.4rem;">
                            <?= ($election['status'] === 'active') ? 'Live Tallying' : ucfirst($election['status']) ?>
                        </div>
                        <div class="stat-title">Status</div>
                    </div>
                </div>
            </div>

            <!-- Position Results Breakdown -->
            <?php foreach ($positionsData as $pos): ?>
                <div class="card" style="margin-bottom: 2rem;">
                    <div class="card-header">
                        <div>
                            <h3 style="margin-bottom: 0.2rem;"><?= htmlspecialchars($pos['title']) ?></h3>
                            <span style="font-size: 0.85rem; color: var(--text-muted);">
                                Total Valid Votes Cast: <strong><?= number_format($pos['total_votes']) ?></strong>
                            </span>
                        </div>
                        <span class="badge badge-active"><?= count($pos['candidates']) ?> Candidates</span>
                    </div>

                    <?php if (empty($pos['candidates'])): ?>
                        <p style="color: var(--text-muted);">No candidates were listed for this position.</p>
                    <?php else: ?>
                        <div>
                            <?php 
                            $maxVotes = 0;
                            if (!empty($pos['candidates'])) {
                                $maxVotes = $pos['candidates'][0]['vote_count'];
                            }

                            foreach ($pos['candidates'] as $rank => $c): 
                                $percent = ($pos['total_votes'] > 0) ? round(($c['vote_count'] / $pos['total_votes']) * 100, 1) : 0;
                                $isLeading = ($pos['total_votes'] > 0 && $c['vote_count'] === $maxVotes && $maxVotes > 0);
                            ?>
                                <div class="results-candidate-row">
                                    <div class="results-info">
                                        <div style="display:flex; align-items:center;">
                                            <strong style="color:var(--dark); margin-right:0.5rem; font-size:1.05rem;">
                                                <?= htmlspecialchars($c['name']) ?>
                                            </strong>
                                            <span style="font-size:0.85rem; color:var(--text-muted);">(<?= htmlspecialchars($c['party']) ?>)</span>
                                            <?php if ($isLeading): ?>
                                                <span class="winner-badge">🏆 <?= ($election['status'] === 'completed') ? 'Winner' : 'Leading' ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <span style="font-weight:700; color:var(--primary);"><?= number_format($c['vote_count']) ?> votes</span>
                                            <span style="color:var(--text-muted); font-size:0.85rem; margin-left:0.4rem;">(<?= $percent ?>%)</span>
                                        </div>
                                    </div>
                                    
                                    <div class="progress-track">
                                        <div class="progress-fill" style="width: <?= $percent ?>%; <?= $isLeading ? 'background: linear-gradient(90deg, #10b981, #059669);' : '' ?>"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

        <?php else: ?>
            <div class="card" style="text-align: center; padding: 3rem;">
                <p>No election selected or found.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
