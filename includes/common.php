<?php
declare(strict_types=1);

define('LOG_BASE', '/var/log/remote');

function loadSettings(): array {
    return json_decode(
        file_get_contents(__DIR__.'/../settings.json'),
        true
    );
}

function getHosts(): array {
    return array_map('basename', glob(LOG_BASE.'/*', GLOB_ONLYDIR));
}

function detectSeverity(string $line): string {
    foreach (['emerg','alert','crit','error','warn','notice','info','debug'] as $s) {
        if (stripos($line, $s) !== false) return $s;
    }
    return 'info';
}

function getLogsForHost(
    string $hostDir,
    int $maxLines,
    array $filterSev = []
): array {
    $rows = [];

    foreach (glob($hostDir.'/*.log') as $file) {
        foreach (array_slice(file($file, FILE_IGNORE_NEW_LINES), -$maxLines) as $line) {
            $sev = detectSeverity($line);
            if ($filterSev && !in_array($sev, $filterSev, true)) continue;

            // extract syslog time if present (Jan  3 06:23:59)
            $time = '';
            if (preg_match('/^([A-Z][a-z]{2}\s+\d+\s[\d:]+)/', $line, $m)) {
                $time = $m[1];
            }

            $rows[] = [
                'time' => $time,          // ALWAYS defined (string)
                'raw'  => trim($line),    // FULL syslog line unchanged
                'sev'  => $sev
            ];
        }
    }

    return $rows;
}