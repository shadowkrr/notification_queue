<?php
require_once __DIR__ . '/env.php';

define("SLACK_WEBHOOK", getenv("SLACK_WEBHOOK") ?: "");

/**
 * Send a message to Slack via Incoming Webhook
 *
 * @param array $payload Message payload
 * @param string $webhook_url Slack Webhook URL
 * @return bool Success status
 */
function sendSlackMessage($payload = [], $webhook_url = '') {
    if (empty($webhook_url)) {
        return false;
    }

    $ch = curl_init($webhook_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json; charset=UTF-8'
    ));

    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return $httpCode === 200;
}

/**
 * Build Slack attachment from alert payload
 *
 * @param array $payload Alert data
 * @return array Slack message with attachments
 */
function getSlackPayload($payload = []) {
    $color = isset($payload['color']) ? '#' . ltrim($payload['color'], '#') : '#ff0000';

    $attachment = [
        'color' => $color,
        'title' => $payload['title'] ?? 'Alert',
        'text' => $payload['text'] ?? '',
        'ts' => isset($payload['create']) ? strtotime($payload['create']) : time(),
    ];

    // Add title link if available
    if (!empty($payload['link'])) {
        $attachment['title_link'] = $payload['link'];
    }

    // Add footer if available
    if (!empty($payload['footer_text'])) {
        $attachment['footer'] = $payload['footer_text'];
    }

    // Add footer icon if available
    if (!empty($payload['footer_icon_url'])) {
        $attachment['footer_icon'] = $payload['footer_icon_url'];
    }

    // Add image if available
    if (!empty($payload['src'])) {
        $attachment['image_url'] = $payload['src'];
    }

    // Add author if available
    if (!empty($payload['author'])) {
        $attachment['author_name'] = $payload['author'];
        if (!empty($payload['author_url'])) {
            $attachment['author_icon'] = $payload['author_url'];
        }
    }

    return [
        'attachments' => [$attachment]
    ];
}
