<?php
require "queue.php";

echo 'get check start...'.PHP_EOL;

$checkUrls = [
    'file' => 'https://checkdomain.com/json/file.json'
];

foreach ($checkUrls as $key => $checkUrl) {
    try {
        $context = stream_context_create(array(
            'http' => array('ignore_errors' => true)
        ));
        $response = file_get_contents($checkUrl, false, $context);

        preg_match('/HTTP\/1\.[0|1|x] ([0-9]{3})/', $http_response_header[0], $matches);
        $status_code = $matches[1];

        switch ($status_code) {
            case '200':
                break;
            default:
                $queue = [];
                $queue['title'] = $key . ' get failure';
                $queue['text'] = $key . " get failure\nstatus_code = ". $status_code. "\n". $checkUrl;
                $queue['create'] = date('Y-m-d H:i:s');
                addAlertQueue($queue);
                break;
        }
    } catch(Exception $e) {
        $queue = [];
        $queue['title'] = $key . ' get failure';
        $queue['text'] = $key . " get failure\nmessage = " . $e->getMessage() . "\n" . $checkUrl;
        $queue['create'] = date('Y-m-d H:i:s');
        addAlertQueue($queue);
    }
}

echo 'get check end...' . PHP_EOL;
?>
