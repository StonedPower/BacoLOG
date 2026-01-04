<?php
// /api/common.php
declare(strict_types=1);

define('LOG_BASE', '/var/log/remote');

function listHosts(): array {
    if (!is_dir(LOG_BASE)) return [];
    return array_values(array_filter(scandir(LOG_BASE), function ($d) {
        return $d !== '.' && $d !== '..' && is_dir(LOG_BASE . '/' . $d);
    }));
}

function listLogFiles(string $host): array {
    $path = LOG_BASE . '/' . $host;
    if (!is_dir($path)) return [];
    return glob($path . '/*.log') ?: [];
}

function parseSeverity(string $line): ?string {
    foreach (['emerg','alert','crit','error','warn','notice','info','debug'] as $s) {
        if (stripos($line, $s) !== false) return $s;
    }
    return null;
}

function emptySeverityCounters(): array {
    return [
        'emerg'  => 0,
        'alert'  => 0,
        'crit'   => 0,
        'error'  => 0,
        'warn'   => 0,
        'notice' => 0,
        'info'   => 0,
        'debug'  => 0
    ];
}
