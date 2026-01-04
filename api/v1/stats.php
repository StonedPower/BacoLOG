<?php
// /api/stats.php
declare(strict_types=1);

require_once __DIR__ . '/common.php';

header('Content-Type: application/json');

$globalSeverities = emptySeverityCounters();
$hostsData = [];

foreach (listHosts() as $host) {
    $hostSeverities = emptySeverityCounters();
    $lastLog = null;

    foreach (listLogFiles($host) as $file) {
        $lines = @file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (!$lines) continue;

        foreach ($lines as $line) {
            $sev = parseSeverity($line);
            if ($sev) {
                $hostSeverities[$sev]++;
                $globalSeverities[$sev]++;
            }
        }

        $candidate = end($lines);
        if ($candidate) {
            $lastLog = $candidate;
        }
    }

    $hostsData[$host] = [
        'total_severities' => array_sum($hostSeverities),
        'severities'       => $hostSeverities,
        'last_log'         => $lastLog
    ];
}

echo json_encode([
    'generated_at'       => date('c'),
    'uptime_seconds'     => time() - @filemtime('/proc/1/stat'),
    'total_hosts'        => count($hostsData),
    'hosts'              => array_keys($hostsData),
    'global_severities'  => $globalSeverities,
    'per_host'           => $hostsData
], JSON_PRETTY_PRINT);
