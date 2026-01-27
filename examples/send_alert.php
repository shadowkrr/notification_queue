<?php
/**
 * Send Alert Example
 *
 * This example shows how to manually queue an alert
 * from the command line or another application.
 *
 * Usage:
 *   php send_alert.php "Alert Title" "Alert message here"
 *   php send_alert.php "Server Down" "Web server is not responding" ff0000
 */

require_once __DIR__ . '/../src/queue.php';

// Parse command line arguments
$title = $argv[1] ?? null;
$text = $argv[2] ?? null;
$color = $argv[3] ?? 'ff0000';

if (!$title || !$text) {
    echo "Usage: php send_alert.php <title> <text> [color]\n";
    echo "\n";
    echo "Arguments:\n";
    echo "  title  - Alert title (required)\n";
    echo "  text   - Alert message (required)\n";
    echo "  color  - Hex color code without # (optional, default: ff0000)\n";
    echo "\n";
    echo "Examples:\n";
    echo "  php send_alert.php \"Test Alert\" \"This is a test message\"\n";
    echo "  php send_alert.php \"Warning\" \"Disk space low\" ff6600\n";
    exit(1);
}

// Create alert payload
$alert = [
    'title' => $title,
    'text' => $text,
    'color' => ltrim($color, '#'),
    'create' => date('c')
];

// Queue the alert
if (addAlertQueue($alert)) {
    echo "Alert queued successfully!\n";
    echo "  Title: {$title}\n";
    echo "  Text: {$text}\n";
    echo "  Color: #{$color}\n";
    echo "\nRun 'php push.php' to send queued alerts.\n";
} else {
    echo "Failed to queue alert.\n";
    exit(1);
}
