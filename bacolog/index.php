<?php
declare(strict_types=1);

// Fetch stats from the API
$statsJson = @file_get_contents("http://localhost/api/v1/stats");
if ($statsJson === false) {
    $stats = null;
} else {
    $stats = json_decode($statsJson, true);
}

// Fallback in case of errors
if (!is_array($stats)) {
    $stats = [
        'global_severities' => [
            'emerg'=>0,'alert'=>0,'crit'=>0,'error'=>0,'warn'=>0,'notice'=>0,'info'=>0,'debug'=>0
        ],
        'total_hosts' => 0,
        'hosts' => [],
        'per_host' => []
    ];
}

$globalSev = $stats['global_severities'];

// Load settings.json
$settingsFile = __DIR__ . '/settings.json';
$settings = [];
if (file_exists($settingsFile)) {
    $json = file_get_contents($settingsFile);
    $settings = json_decode($json, true) ?: [];
}

// Default logo if not set
$logoUrl = $settings['logo_url'] ?? 'assets/logo.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BacoLOG Dashboard</title>

<style>
:root {
    --bg-main:#0b1220;
    --bg-card:#020617;
    --bg-card-hover:#0f172a;
    --border:#1e293b;
    --text-main:#e5e7eb;
    --text-muted:#94a3b8;
}

* { box-sizing:border-box; }

body {
    margin:0;
    background:linear-gradient(180deg,#020617,#0b1220);
    color:var(--text-main);
    font-family:system-ui,-apple-system,Segoe UI,Roboto,sans-serif;
}

h1 {
    margin:24px 14px 10px;
    font-size:18px;
    font-weight:600;
    letter-spacing:.3px;
}

.section {
    padding-bottom:20px;
}

.cards {
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(160px,1fr));
    gap:14px;
    justify-content:center; /* center all cards horizontally */
    padding:0 14px;
}

.card {
    background:var(--bg-card);
    border:1px solid var(--border);
    border-radius:12px;
    padding:16px 14px;
    cursor:pointer;
    transition:transform .15s ease, box-shadow .15s ease, background .15s ease;
    position:relative;
    overflow:hidden;
}

.card::after {
    content:"";
    position:absolute;
    inset:0;
    background:linear-gradient(120deg,transparent,rgba(255,255,255,.04),transparent);
    opacity:0;
    transition:opacity .2s;
}

.card:hover {
    background:var(--bg-card-hover);
    transform:translateY(-2px);
    box-shadow:0 8px 24px rgba(0,0,0,.35);
}

.card:hover::after {
    opacity:1;
}

.card h2 {
    margin:0 0 6px;
    font-size:16px;
    font-weight:600;
}

.card p {
    margin:0;
    font-size:13px;
    color:var(--text-muted);
}

.sev-card h2 {
    font-size:15px;
    letter-spacing:.5px;
}

.sev-count {
    font-size:22px;
    font-weight:700;
    margin-top:6px;
}

.host-card h2 {
    font-size:14px;
    word-break:break-all;
}

/* Logo styling */
.logo-container {
    display:flex;
    justify-content:center;
    margin:20px 0;
}
.logo-container img {
    max-width:300px;
    width:30%;
    height:auto;
}

@media (max-width: 640px) {
    h1 { margin-top:18px; }
    .logo-container img { width:50%; }
}
</style>
</head>
<body>

<?php include __DIR__ . '/includes/navbar.php'; ?>



<!-- Logo -->
<div class="logo-container">
    <img src="<?= htmlspecialchars($logoUrl) ?>" alt="BacoLOG Logo">
</div>
<h1><center>Dashboard</center></h1>

<h1>Total Severities</h1>

<div class="section">
    <div class="cards">
    <?php foreach ($globalSev as $sev => $count):
        $color = match($sev) {
            'emerg','alert','crit','error' => '#ef4444',
            'warn' => '#facc15',
            'notice' => '#fbbf24',
            'info' => '#22c55e',
            'debug' => '#a78bfa',
            default => '#64748b'
        };
    ?>
        <div class="card sev-card"
             onclick="window.location='viewer.php?sev[]=<?= $sev ?>'"
             style="border-left:4px solid <?= $color ?>">

            <h2><?= strtoupper($sev) ?></h2>
            <div class="sev-count"><?= $count ?></div>
            <p>entries</p>
        </div>
    <?php endforeach; ?>
    </div>
</div>

<h1>Hosts · <?= $stats['total_hosts'] ?></h1>
<div class="section">
    <div class="cards">
    <?php foreach ($stats['hosts'] as $host): ?>
        <div class="card host-card"
             onclick="window.location='viewer.php?host=<?= urlencode($host) ?>'">
            <h2><?= htmlspecialchars($host) ?></h2>
        </div>
    <?php endforeach; ?>
    </div>
</div>

</body>
</html>
