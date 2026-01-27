<?php
/**
 * Custom Monitor Example
 *
 * This example shows how to create a custom monitoring script
 * that checks a specific condition and queues an alert.
 */

require_once __DIR__ . '/../src/queue.php';

// Example: Monitor disk usage
function checkDiskUsage($threshold = 80) {
    $diskTotal = disk_total_space('/');
    $diskFree = disk_free_space('/');
    $diskUsed = $diskTotal - $diskFree;
    $usagePercent = ($diskUsed / $diskTotal) * 100;

    return [
        'usage' => round($usagePercent, 2),
        'exceeded' => $usagePercent > $threshold
    ];
}

// Example: Monitor memory usage (Linux only)
function checkMemoryUsage($threshold = 80) {
    if (PHP_OS !== 'Linux') {
        return ['usage' => 0, 'exceeded' => false, 'error' => 'Linux only'];
    }

    $memInfo = file_get_contents('/proc/meminfo');
    preg_match('/MemTotal:\s+(\d+)/', $memInfo, $totalMatch);
    preg_match('/MemAvailable:\s+(\d+)/', $memInfo, $availMatch);

    if (empty($totalMatch) || empty($availMatch)) {
        return ['usage' => 0, 'exceeded' => false, 'error' => 'Failed to parse meminfo'];
    }

    $total = (int)$totalMatch[1];
    $available = (int)$availMatch[1];
    $usagePercent = (($total - $available) / $total) * 100;

    return [
        'usage' => round($usagePercent, 2),
        'exceeded' => $usagePercent > $threshold
    ];
}

// Run checks
echo "Running custom monitors...\n";

// Check disk usage
$disk = checkDiskUsage(80);
echo "Disk usage: {$disk['usage']}%\n";

if ($disk['exceeded']) {
    addAlertQueue([
        'title' => 'Disk Usage Alert',
        'text' => "Disk usage is at {$disk['usage']}%, exceeding 80% threshold.",
        'color' => 'ff6600'
    ]);
    echo "Alert queued: High disk usage\n";
}

// Check memory usage
$memory = checkMemoryUsage(80);
if (!isset($memory['error'])) {
    echo "Memory usage: {$memory['usage']}%\n";

    if ($memory['exceeded']) {
        addAlertQueue([
            'title' => 'Memory Usage Alert',
            'text' => "Memory usage is at {$memory['usage']}%, exceeding 80% threshold.",
            'color' => 'ff0000'
        ]);
        echo "Alert queued: High memory usage\n";
    }
}

echo "Custom monitor check complete.\n";
