<?php
declare(strict_types=1);

/* -----------------------------
   Settings handling (read-only)
----------------------------- */
define('SETTINGS_FILE', __DIR__ . '/settings.json');

function loadSettings(): array {
    if (!file_exists(SETTINGS_FILE)) {
        $default = [
            'auto_refresh'     => true,
            'refresh_interval' => 10,
            'max_lines'        => 2000,
            'dashboard_host'   => ''
        ];
        return $default;
    }

    $json = file_get_contents(SETTINGS_FILE);
    if ($json === false) throw new RuntimeException('Cannot read settings file');

    $settings = json_decode($json, true);
    if (!is_array($settings)) throw new RuntimeException('Invalid settings JSON');

    $defaults = [
        'auto_refresh'     => true,
        'refresh_interval' => 10,
        'max_lines'        => 2000,
        'dashboard_host'   => ''
    ];
    return array_merge($defaults, $settings);
}

/* -----------------------------
   Log helpers
----------------------------- */
function parseSyslog(string $line): array {
    if (preg_match('/^(\w+\s+\d+\s[\d:]+)\s(\S+)\s([^:]+):\s?(.*)$/', $line, $m)) {
        return [
            'raw_time' => $m[1],
            'program'  => $m[3],
            'message'  => $m[4],
            'ts'       => strtotime($m[1])
        ];
    }
    return ['raw_time'=>'','program'=>'','message'=>$line,'ts'=>0];
}

function detectSeverity(string $msg): string {
    foreach (['emerg','alert','crit','error','warn','notice','info','debug'] as $s) {
        if (stripos($msg, $s) !== false) return $s;
    }
    return 'info';
}

/* -----------------------------
   Host and log helpers
----------------------------- */
function getHosts(string $baseDir = '/var/log/remote'): array {
    $dirs = glob($baseDir . '/*', GLOB_ONLYDIR);
    return array_map('basename', $dirs);
}

function getLogsForHost(string $hostDir, int $maxLines = 2000, int $timeLimit = 0, array $filterSev = []): array {
    $rows = [];
    foreach (glob($hostDir . '/*') as $file) {
        if (!is_file($file) || !is_readable($file)) continue;
        $lines = array_slice(file($file, FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES), -$maxLines);
        foreach ($lines as $line) {
            $p = parseSyslog($line);
            if ($timeLimit && $p['ts'] && $p['ts'] < $timeLimit) continue;

            $sev = detectSeverity($p['message']);
            if ($filterSev && !in_array($sev,$filterSev)) continue;

            $rows[] = [
                'time'    => $p['raw_time'],
                'program' => $p['program'],
                'message' => $p['message'],
                'sev'     => $sev
            ];
        }
    }
    return $rows;
}

/* -----------------------------
   Dashboard helpers
----------------------------- */
function severityCounts(array $logs): array {
    $counts = [
        'emerg'  => 0,
        'alert'  => 0,
        'crit'   => 0,
        'error'  => 0,
        'warn'   => 0,
        'notice' => 0,
        'info'   => 0,
        'debug'  => 0,
        'hosts'  => count($logs)
    ];

    foreach ($logs as $hostLogs) {
        foreach ($hostLogs as $entry) {
            $sev = $entry['sev'] ?? 'info';
            if (isset($counts[$sev])) $counts[$sev]++;
            else $counts['info']++;
        }
    }

    return $counts;
}

function getDashboardHost(): string {
    $settings = loadSettings();
    return $settings['dashboard_host'] ?: gethostname();
}
?>