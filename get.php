<?php
require "queue.php";

echo 'get check start...'.PHP_EOL;

$checkUrls = [
    'file' => 'https://checkdomain.com/json/file.json'
];

foreach ($checkUrls as $key => $checkUrl) {
    try {
        $context = stream_context_create(array(
            'http' => array(
                'ignore_errors' => true,
                'timeout' => 30,
                'user_agent' => 'PHP Health Check Monitor/1.0'
            )
        ));
        
        $response = file_get_contents($checkUrl, false, $context);
        
        if ($response === false) {
            $queue = [];
            $queue['title'] = $key . ' get failure';
            $queue['text'] = $key . " get failure\nUnable to fetch URL\n" . $checkUrl;
            $queue['create'] = date('c');
            addAlertQueue($queue);
            continue;
        }

        if (!isset($http_response_header) || empty($http_response_header)) {
            $queue = [];
            $queue['title'] = $key . ' get failure';
            $queue['text'] = $key . " get failure\nNo HTTP response headers\n" . $checkUrl;
            $queue['create'] = date('c');
            addAlertQueue($queue);
            continue;
        }

        preg_match('/HTTP\/1\.[0|1|x] ([0-9]{3})/', $http_response_header[0], $matches);
        $status_code = isset($matches[1]) ? $matches[1] : 'unknown';

        switch ($status_code) {
            case '200':
                echo $key . ' check success (200)...' . PHP_EOL;
                break;
            default:
                $queue = [];
                $queue['title'] = $key . ' get failure';
                $queue['text'] = $key . " get failure\nstatus_code = ". $status_code. "\n". $checkUrl;
                $queue['create'] = date('c');
                addAlertQueue($queue);
                echo $key . ' check failed (' . $status_code . ')...' . PHP_EOL;
                break;
        }
    } catch(Exception $e) {
        $queue = [];
        $queue['title'] = $key . ' get failure';
        $queue['text'] = $key . " get failure\nmessage = " . $e->getMessage() . "\n" . $checkUrl;
        $queue['create'] = date('c');
        addAlertQueue($queue);
        echo $key . ' check error: ' . $e->getMessage() . PHP_EOL;
    }
}

echo 'get check end...' . PHP_EOL;
?>
