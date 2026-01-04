<?php
// /api/health.php
declare(strict_types=1);
require_once __DIR__ . '/common.php';

header('Content-Type: application/json');

$uptime = @file_get_contents('/proc/uptime');
$uptimeSeconds = $uptime ? (int) explode(' ', $uptime)[0] : 0;

echo json_encode([
    'status'          => 'ok',
    'time'            => date('c'),
    'uptime_seconds'  => $uptimeSeconds,
    'disk_free_mb'    => round(disk_free_space('/') / 1024 / 1024),
    'disk_total_mb'   => round(disk_total_space('/') / 1024 / 1024)
], JSON_PRETTY_PRINT);
