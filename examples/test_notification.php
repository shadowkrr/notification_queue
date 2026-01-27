<?php
/**
 * Test Notification Example
 *
 * This example sends a test notification directly to Discord and Slack
 * without using the queue system. Useful for testing webhook configuration.
 *
 * Usage:
 *   php test_notification.php         # Test both Discord and Slack
 *   php test_notification.php discord # Test Discord only
 *   php test_notification.php slack   # Test Slack only
 */

require_once __DIR__ . '/../src/discord.php';
require_once __DIR__ . '/../src/slack.php';

$target = $argv[1] ?? 'all';

$testPayload = [
    'title' => 'Test Notification',
    'text' => 'This is a test message from Notification Queue System.',
    'color' => '00ff00',
    'create' => date('c'),
    'footer_text' => 'Test completed at ' . date('Y-m-d H:i:s')
];

echo "Testing notification system...\n\n";

// Test Discord
if ($target === 'all' || $target === 'discord') {
    echo "Discord:\n";
    if (empty(DISCORD_WEBHOOK)) {
        echo "  [SKIP] DISCORD_WEBHOOK environment variable not set\n";
    } else {
        $payload = [
            'content' => '',
            'embeds' => getEmbeds($testPayload)
        ];
        sendMessage($payload, DISCORD_WEBHOOK);
        echo "  [SENT] Check your Discord channel\n";
    }
    echo "\n";
}

// Test Slack
if ($target === 'all' || $target === 'slack') {
    echo "Slack:\n";
    if (empty(SLACK_WEBHOOK)) {
        echo "  [SKIP] SLACK_WEBHOOK environment variable not set\n";
    } else {
        $slackPayload = getSlackPayload($testPayload);
        if (sendSlackMessage($slackPayload, SLACK_WEBHOOK)) {
            echo "  [SENT] Check your Slack channel\n";
        } else {
            echo "  [FAIL] Failed to send to Slack\n";
        }
    }
    echo "\n";
}

echo "Test complete.\n";
