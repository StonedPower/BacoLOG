<?php
// /api/hosts.php
declare(strict_types=1);

require_once __DIR__ . '/common.php';

header('Content-Type: application/json');

$hosts = [];

foreach (listHosts() as $host) {
    $hosts[] = [
        'host' => $host,
        'ip'   => $host
    ];
}

echo json_encode([
    'total_hosts' => count($hosts),
    'hosts'       => $hosts
], JSON_PRETTY_PRINT);
