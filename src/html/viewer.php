<?php
declare(strict_types=1);
require __DIR__ . '/includes/common.php';

$settings   = loadSettings();
$baseDir    = '/var/log/remote';
$maxLines   = $settings['max_lines'];
$severity   = $_GET['sev'] ?? [];
$activeHost = $_GET['host'] ?? '';

$severity = is_array($severity) ? $severity : [$severity];
$allowedSev = ['emerg','alert','crit','error','warn','notice','info','debug'];
$severity = array_values(array_intersect($severity, $allowedSev));

$hosts = getHosts($baseDir);
if (!$activeHost && !empty($hosts)) $activeHost = $hosts[0];

$logs = [];
foreach ($hosts as $host) {
    $logs[$host] = array_reverse(getLogsForHost($baseDir.'/'.$host, $maxLines, 0, $severity));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Syslog Viewer</title>
<style>
body { margin:0; background:#0b1220; color:#e5e7eb; font-family:system-ui,sans-serif; }
a { color:#93c5fd; text-decoration:none; }
.viewer-bar { padding:10px 14px; border-bottom:1px solid #1e293b; background:#020617; display:flex; flex-wrap:wrap; gap:8px; align-items:center; }
.badge { padding:6px 10px; border-radius:8px; font-size:12px; cursor:pointer; border:1px solid #1e293b; background:#020617; color:#e5e7eb; text-decoration:none; }
.badge.active { background:#2563eb; color:white; border-color:#2563eb; }
.tabs { display:flex; gap:6px; padding:8px 14px; border-bottom:1px solid #1e293b; overflow-x:auto; }
.tab { padding:6px 12px; background:#020617; border-radius:8px; border:1px solid #1e293b; white-space:nowrap; }
.tab.active { background:#2563eb; }
.log { padding:8px 14px; font-family:monospace; font-size:13px; }
.entry { display:flex; gap:10px; padding:8px; margin-bottom:6px; border-left:4px solid #64748b; background:#020617; border-radius:6px; }
.entry .meta { min-width:140px; color:#94a3b8; }
.entry .sev { font-size:11px; padding:2px 6px; border-radius:6px; margin-right:6px; border:1px solid #1e293b; }
.entry .msg { word-break:break-word; }
.sev-emerg,.sev-alert,.sev-crit,.sev-error { border-left-color:#ef4444; }
.sev-warn { border-left-color:#facc15; }
.sev-notice { border-left-color:#fbbf24; }
.sev-info { border-left-color:#22c55e; }
.sev-debug { border-left-color:#a78bfa; }
@media(max-width:768px){ .entry { flex-direction:column; } .entry .meta { min-width:auto; } }
</style>
</head>
<body>
<?php include __DIR__ . '/includes/navbar.php'; ?>

<div class="viewer-bar">
<?php
$allSev = ['emerg','alert','crit','error','warn','notice','info','debug'];
$allActive = empty($severity) || (count(array_diff($allSev,$severity))===0);
?>
<a class="badge <?= $allActive ? 'active' : '' ?>" href="?host=<?= urlencode($activeHost) ?>">ALL</a>

<?php foreach ($allSev as $s): ?>
<a class="badge <?= in_array($s,$severity) ? 'active':'' ?>" href="?host=<?= urlencode($activeHost) ?>&sev[]=<?= $s ?>"><?= strtoupper($s) ?></a>
<?php endforeach; ?>
</div>

<div class="tabs">
<?php foreach ($hosts as $host): ?>
<a class="tab <?= $activeHost===$host?'active':'' ?>" href="?host=<?= urlencode($host) ?><?php foreach($severity as $s) echo "&sev[]=$s"; ?>"><?= htmlspecialchars($host) ?></a>
<?php endforeach; ?>
</div>

<div class="log">
<?php if (!isset($logs[$activeHost]) || empty($logs[$activeHost])): ?>
<p>No logs available.</p>
<?php else: ?>
<?php foreach ($logs[$activeHost] as $entry): ?>
<div class="entry sev-<?= $entry['sev'] ?>">
    <div class="meta">
        <span class="sev"><?= strtoupper($entry['sev']) ?></span>
        <?= htmlspecialchars($entry['program']) ?>
    </div>
    <div class="msg"><?= htmlspecialchars($entry['message']) ?></div>
</div>
<?php endforeach; ?>
<?php endif; ?>
</div>

</body>
</html>