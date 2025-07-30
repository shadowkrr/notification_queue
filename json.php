<?php
require "queue.php";

echo 'json check start...'.PHP_EOL;

$checkFiles = [
    'filename' => 'file.json'
];

foreach ($checkFiles as $key => $checkFile) {
    $checkPath = '/filepath/json/'. $checkFile;
    
    try {
        if (!file_exists($checkPath)) {
            $queue = [];
            $queue['title'] = $key . ' json failure';
            $queue['text'] = 'File does not exist: ' . $checkPath;
            $queue['create'] = date('c');
            addAlertQueue($queue);
            echo $key . ' file not found...' . PHP_EOL;
            continue;
        }

        $response = file_get_contents($checkPath);
        
        if ($response === false) {
            $queue = [];
            $queue['title'] = $key . ' json failure';
            $queue['text'] = 'Failed to read file: ' . $checkPath;
            $queue['create'] = date('c');
            addAlertQueue($queue);
            echo $key . ' file read error...' . PHP_EOL;
            continue;
        }

        $json = json_decode($response, true);
        $error = [];

        if (empty($response)) {
            $error['text'] = 'response is empty';
        } else if (json_last_error() !== JSON_ERROR_NONE) {
            $error['text'] = json_last_error_msg();
        } else if (!is_array($json) && !is_object($json)) {
            $error['text'] = 'json is not array or object, It may be damaged';
        } else if (is_array($json) && count($json) === 0) {
            $error['text'] = 'json array is empty';
        }

        if (count($error) === 0) {
            echo $key . ' json check success...' . PHP_EOL;
            continue;
        }

        $queue = [];
        $queue['title'] = $key . ' json failure';
        $queue['text'] = $error['text']. "\nFile: ". $checkPath;
        $queue['create'] = date('c');
        addAlertQueue($queue);
        echo $key . ' json check failed: ' . $error['text'] . PHP_EOL;
        
    } catch(Exception $e) {
        echo $key . ' error: ' . $e->getMessage(). PHP_EOL;
        $queue = [];
        $queue['title'] = $key . ' json failure';
        $queue['text'] = $key . " json failure\nmessage = " . $e->getMessage() . "\nFile: " . $checkPath;
        $queue['create'] = date('c');
        addAlertQueue($queue);
    }
}

echo 'json check end...' . PHP_EOL;
?>