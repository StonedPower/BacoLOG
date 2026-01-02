<?php
declare(strict_types=1);
require __DIR__ . '/includes/common.php';

$baseDir = '/var/log/remote';
$hosts = getHosts($baseDir);

// Load logs per host
$logs = [];
foreach ($hosts as $host) {
    $logs[$host] = getLogsForHost($baseDir.'/'.$host);
}

// Get severity counts
$counts = severityCounts($logs);
$dashboardLabel = getDashboardHost();

$allSeverities = ['emerg','alert','crit','error','warn','notice','info','debug'];
$colors = [
    'emerg'=>'#ef4444',
    'alert'=>'#f87171',
    'crit'=>'#f87171',
    'error'=>'#ef4444',
    'warn'=>'#facc15',
    'notice'=>'#fbbf24',
    'info'=>'#22c55e',
    'debug'=>'#a78bfa'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Syslog Dashboard</title>
<style>
body { margin:0; font-family:system-ui,sans-serif; background:#0b1220; color:#e5e7eb; }
.container { max-width:900px; margin:auto; padding:20px; }
h1 { margin-bottom:20px; }
.stats { display:flex; flex-wrap:wrap; gap:12px; }
.stat { flex:1 1 120px; background:#1e293b; padding:16px; border-radius:12px; text-align:center; min-width:100px; }
.stat h2 { margin:0; font-size:22px; }
.stat span { font-size:14px; color:#94a3b8; }
@media(max-width:768px){ .stats { flex-direction:row; flex-wrap:wrap; gap:8px; } .stat { flex:1 1 45%; } }
</style>
</head>
<body>
<?php include __DIR__ . '/includes/navbar.php'; ?>

<div class="container">
    <h1>Dashboard - <?= htmlspecialchars($dashboardLabel) ?></h1>

    <div class="stats">
        <div class="stat" style="border-top:4px solid #2563eb;">
            <h2><?= $counts['hosts'] ?></h2>
            <span>Hosts</span>
        </div>
        <?php foreach($allSeverities as $sev): ?>
        <div class="stat" style="border-top:4px solid <?= $colors[$sev] ?>;">
            <h2><?= $counts[$sev] ?></h2>
            <span><?= strtoupper($sev) ?></span>
        </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>