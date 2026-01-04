<?php
declare(strict_types=1);

require __DIR__ . '/includes/common.php';

$settings   = loadSettings();
$baseDir    = '/var/log/remote';
$maxLines   = $settings['max_lines'];

/* -----------------------------
   Severity handling
----------------------------- */
$allowedSev = ['emerg','alert','crit','error','warn','notice','info','debug'];

$severity = $_GET['sev'] ?? [];
$severity = is_array($severity) ? $severity : [$severity];
$filterSev = array_values(array_intersect($severity, $allowedSev));

/* -----------------------------
   Host handling
----------------------------- */
$hosts = getHosts();
array_unshift($hosts, 'ALL'); // add "ALL hosts" tab

$activeHost = $_GET['host'] ?? 'ALL';
if (!in_array($activeHost, $hosts, true)) {
    $activeHost = 'ALL';
}

/* -----------------------------
   Load logs
----------------------------- */
$logs = [];

if ($activeHost === 'ALL') {
    foreach (array_slice($hosts, 1) as $host) {
        foreach (getLogsForHost("$baseDir/$host", $maxLines, $filterSev) as $entry) {
            $entry['host'] = $host;
            $entry['time'] = substr($entry['raw'], 0, 15);
            $entry['message'] = $entry['raw'];
            $logs[] = $entry;
        }
    }
} else {
    foreach (getLogsForHost("$baseDir/$activeHost", $maxLines, $filterSev) as $entry) {
        $entry['host'] = $activeHost;
        $entry['time'] = substr($entry['raw'], 0, 15);
        $entry['message'] = $entry['raw'];
        $logs[] = $entry;
    }
}

usort($logs, fn($a, $b) => strtotime($b['time']) <=> strtotime($a['time']));

/* Severity colors for filter buttons and message cards */
$sevColors = [
    'emerg' => '#ef4444',
    'alert' => '#ef4444',
    'crit'  => '#ef4444',
    'error' => '#ef4444',
    'warn'  => '#facc15',
    'notice'=> '#fbbf24',
    'info'  => '#22c55e',
    'debug' => '#a78bfa',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BacoLOG Viewer</title>
<style>
:root {
    --bg-main:#0b1220;
    --bg-panel:#020617;
    --bg-panel-hover:#0f172a;
    --border:#1e293b;
    --text-main:#e5e7eb;
    --text-muted:#94a3b8;
    --tab-active:#2563eb;
}

/* Base */
body {
    margin:0;
    background:var(--bg-main);
    color:var(--text-main);
    font-family:system-ui,-apple-system,Segoe UI,Roboto,sans-serif;
}

/* Layout */
.container {
    display:flex;
    min-height:100vh;
}

/* Sidebar */
.sidebar {
    width:240px;
    flex-shrink:0;
    background:var(--bg-panel);
    border-right:1px solid var(--border);
    padding:14px;
    display:flex;
    flex-direction:column;
    gap:20px;
    position:sticky;
    top:0;
    height:100vh;
    overflow-y:auto;
}

/* Sidebar sections */
.sidebar h2 {
    margin:0 0 8px;
    font-size:16px;
    font-weight:600;
}

/* Severity filter buttons */
.sev-buttons {
    display:grid;
    grid-template-columns: 1fr 1fr; /* 2 columns */
    gap:6px;
    margin-bottom:6px;
}

.sev-button {
    padding:4px 6px; /* smaller padding */
    border-radius:6px;
    font-size:12px; /* smaller font */
    font-weight:600;
    text-align:center;
    cursor:pointer;
    color:#000;
    border:none;
    transition:0.15s;
    user-select:none;
}

.sev-button.selected {
    box-shadow:0 0 0 2px #fff inset;
    color:#fff;
}

/* Apply/Clear buttons */
.apply-clear {
    display:flex;
    gap:6px;
    flex-wrap:wrap;
}

.apply-clear button {
    flex:1; /* same width */
    padding:6px 0;
    border-radius:6px;
    border:1px solid var(--border);
    background:var(--bg-panel);
    color:var(--text-main);
    cursor:pointer;
    font-size:14px;
    transition:0.2s;
}

.apply-clear button:hover {
    border-color:var(--tab-active);
}

/* Host links */
.sidebar .hosts {
    display:flex;
    flex-direction:column;
    gap:6px;
}

.sidebar .host-tab {
    padding:6px 10px;
    border-radius:6px;
    border:1px solid var(--border);
    background:var(--bg-panel);
    color:var(--text-main);
    text-decoration:none;
    transition:0.2s;
    white-space:nowrap;
}

.sidebar .host-tab.active {
    background:var(--tab-active);
    border-color:var(--tab-active);
    color:#fff;
}

/* Main content */
.main {
    flex:1;
    padding:14px;
    overflow-x:auto;
}

.log {
    display:flex;
    flex-direction:column;
    gap:8px;
    font-family:monospace;
    font-size:14px; /* slightly bigger */
}

/* Individual log entry */
.entry {
    background:var(--bg-panel);
    border-left:4px solid #64748b; /* default, overridden below */
    border-radius:8px;
    padding:12px 14px;
    transition:0.15s;
}

.entry:hover {
    background:var(--bg-panel-hover);
    box-shadow:0 4px 12px rgba(0,0,0,.4);
}

/* Entry severity colors */
<?php foreach($sevColors as $sev => $color): ?>
.sev-<?= $sev ?> { border-left-color: <?= $color ?>; }
<?php endforeach; ?>

.entry-header {
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:6px;
    font-size:12px;
}

.entry-header .left {
    display:flex;
    gap:6px;
}

.entry-header .right {
    color:var(--text-muted);
}

.badge {
    padding:3px 8px;
    border-radius:6px;
    border:1px solid var(--border);
    background:var(--bg-panel);
    font-size:14px;
    display:inline-flex;
    align-items:center;
}

.badge.host { color:#93c5fd; }

.msg {
    white-space:pre-wrap;
    word-break:break-word;
    font-size:14px; /* slightly bigger */
}

/* Mobile adjustments */
@media(max-width:768px){
    .container { flex-direction:column; }
    .sidebar { width:100%; height:auto; position:relative; }
}
</style>
<script>
// Toggle selection on button click
function toggleSev(button) {
    button.classList.toggle('selected');
    const input = button.querySelector('input[type="checkbox"]');
    input.checked = !input.checked;
}
</script>
</head>
<body>

<?php include __DIR__ . '/includes/navbar.php'; ?>

<div class="container">
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Filter Severities</h2>
        <form method="get">
            <div class="sev-buttons">
                <?php foreach ($allowedSev as $s):
                    $selected = in_array($s,$filterSev) ? 'selected' : '';
                    $color = $sevColors[$s] ?? '#64748b';
                ?>
                    <div class="sev-button <?= $selected ?>" style="background:<?= $color ?>;" onclick="toggleSev(this)">
                        <input type="checkbox" name="sev[]" value="<?= $s ?>" style="display:none;" <?= $selected ? 'checked' : '' ?>>
                        <?= strtoupper($s) ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="apply-clear">
                <button type="submit">Apply</button>
                <button type="button" onclick="window.location='viewer.php'">Clear</button>
                <button type="button" onclick="window.location.reload()">Refresh</button>
            </div>
        </form>

        <h2>Hosts</h2>
        <div class="hosts">
            <?php foreach ($hosts as $host): ?>
                <a class="host-tab <?= $host === $activeHost ? 'active' : '' ?>"
                   href="?host=<?= urlencode($host) ?>&<?= http_build_query(['sev'=>$filterSev]) ?>">
                    <?= $host === 'ALL' ? 'ALL HOSTS' : htmlspecialchars($host) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Main logs -->
    <div class="main">
        <div class="log">
            <?php if (!$logs): ?>
                <p>No logs found.</p>   
            <?php else: ?>
                <?php foreach ($logs as $entry): ?>
                    <div class="entry sev-<?= $entry['sev'] ?>">
                        <div class="entry-header">
                            <div class="left">
                                <span class="badge"><?= strtoupper($entry['sev']) ?></span>
                                <span class="badge host"><?= htmlspecialchars($entry['host']) ?></span>
                            </div>
                            <div class="badge host"><?= htmlspecialchars($entry['time']) ?></div>
                        </div>
                        <div class="msg"><?= htmlspecialchars($entry['message']) ?></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
