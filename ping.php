<?php
require "queue.php";

define('PING_OPTION', [
    'Linux' => '-c',    // linux
    'Darwin' => '-c',   // macOS
    'WINNT' => '-n',    // windows
]);

echo 'ping check start...'.PHP_EOL;

ping('172.0.0.1'); // target server ip

echo 'ping check end...' . PHP_EOL;

function ping($host, $count = 1)
{
    $option = PING_OPTION[PHP_OS];
    exec("ping {$option} {$count} {$host}", $output, $result);

    if ($result) {
        echo $host. ' ping failure...', PHP_EOL;
        $output = implode(PHP_EOL, $output);
        $queue = [];
        $queue['title'] = $host. ' ping failure';
        $queue['text'] = $output;
        $queue['create'] = date('Y-m-d H:i:s');
        addAlertQueue($queue);
    } else {
        echo $host. ' ping success...', PHP_EOL;
    }
}
?>
