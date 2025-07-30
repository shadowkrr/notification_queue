<?php
require_once './discord.php';

echo 'push notifications start...' . PHP_EOL;

try {
    if (!file_exists('./queues.json')) {
        echo 'queues.json not found, creating empty file...' . PHP_EOL;
        file_put_contents('./queues.json', json_encode([], JSON_UNESCAPED_UNICODE));
        exit(0);
    }

    $jsons = file_get_contents('./queues.json');
    
    if ($jsons === false) {
        echo 'Failed to read queues.json' . PHP_EOL;
        exit(1);
    }

    $queues = json_decode($jsons, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo 'Invalid JSON in queues.json: ' . json_last_error_msg() . PHP_EOL;
        exit(1);
    }

    if (empty($queues) || !is_array($queues)) {
        echo 'No alerts to send.' . PHP_EOL;
        exit(0);
    }

    echo 'Sending ' . count($queues) . ' alert(s)...' . PHP_EOL;

    foreach ($queues as $key => $queue) {
        try {
            if (!isset($queue['title']) || !isset($queue['text'])) {
                echo 'Invalid queue item at index ' . $key . ', skipping...' . PHP_EOL;
                continue;
            }

            $payload = [];
            $payload['content'] = "";
            $payload["title"] = $queue['title'];
            $payload["text"] = $queue['text'];
            $payload["create"] = isset($queue['create']) ? $queue['create'] : date('c');
            $payload["author"] = "alert";
            $payload['embeds'] = getEmbeds($payload);

            sendMessage($payload, ALERT_WEBHOOK);
            echo 'Sent alert: ' . $queue['title'] . PHP_EOL;
            
        } catch (Exception $e) {
            echo 'Error sending alert ' . $key . ': ' . $e->getMessage() . PHP_EOL;
        }
    }

    // Clear the queue after sending all notifications
    if (file_put_contents("./queues.json", json_encode([], JSON_UNESCAPED_UNICODE)) === false) {
        echo 'Warning: Failed to clear queues.json' . PHP_EOL;
    } else {
        echo 'Queue cleared successfully.' . PHP_EOL;
    }

} catch (Exception $e) {
    echo 'Fatal error: ' . $e->getMessage() . PHP_EOL;
    exit(1);
}

echo 'push notifications end...' . PHP_EOL;
?>