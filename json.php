<?php
require "queue.php";

echo 'json check start...'.PHP_EOL;

$checkFiles = [
    'filename' => 'file.json'
];

foreach ($checkFiles as $key => $checkFile) {
    try {
        $context = stream_context_create(array(
            'http' => array('ignore_errors' => true)
        ));
        $response = file_get_contents('/filepath/json/'. $checkFile, false, $context);
        $json = json_decode($response, true);

        $error = [];

        if (empty($response)) {
            $error['text'] = 'response is empty';
        } else if (json_last_error() !== JSON_ERROR_NONE) {
            $error['text'] = json_last_error_msg();
        } else if (!is_array($json)) {
            $error['text'] = 'json is not array, It may be damaged';
        } else if (count($json) < 0) {
            $error['text'] = 'json count = 0';
        }

        if (count($error) === 0) continue;

        $queue = [];
        $queue['title'] = $key . ' json failure';
        $queue['text'] = $error['text']. "\n". $checkUrl;
        $queue['create'] = date('Y-m-d H:i:s');
        addAlertQueue($queue);
    } catch(Exception $e) {
        echo $e->getMessage(). PHP_EOL;
        $queue = [];
        $queue['title'] = $key . ' json failure';
        $queue['text'] = $key . " json failure\nmessage = " . $e->getMessage() . "\n" . $checkUrl;
        $queue['create'] = date('Y-m-d H:i:s');
        addAlertQueue($queue);
    }
}

echo 'json check end...' . PHP_EOL;