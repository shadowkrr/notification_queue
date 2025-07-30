<?php
require "queue.php";

define('PING_OPTION', [
    'Linux' => '-c',    // linux
    'Darwin' => '-c',   // macOS
    'WINNT' => '-n',    // windows
]);

// Configure hosts to monitor - update these IPs/domains as needed
$monitorHosts = [
    'local_server' => '127.0.0.1',
    'google_dns' => '8.8.8.8',
    // Add more hosts as needed
    // 'example_server' => '192.168.1.100'
];

echo 'ping check start...'.PHP_EOL;

foreach ($monitorHosts as $name => $host) {
    ping($host, $name);
}

echo 'ping check end...' . PHP_EOL;

function ping($host, $name = null, $count = 1)
{
    // Validate host format for security
    if (!filter_var($host, FILTER_VALIDATE_IP) && !filter_var($host, FILTER_VALIDATE_DOMAIN)) {
        echo 'Invalid host format: ' . $host . PHP_EOL;
        return false;
    }
    
    $displayName = $name ?: $host;
    
    if (!isset(PING_OPTION[PHP_OS])) {
        echo 'Unsupported OS: ' . PHP_OS . PHP_EOL;
        return false;
    }
    
    $option = PING_OPTION[PHP_OS];
    $command = sprintf("ping %s %d %s", $option, (int)$count, escapeshellarg($host));
    
    exec($command, $output, $result);

    if ($result !== 0) {
        echo $displayName . ' (' . $host . ') ping failure...' . PHP_EOL;
        $outputText = implode(PHP_EOL, $output);
        $queue = [];
        $queue['title'] = $displayName . ' ping failure';
        $queue['text'] = "Host: " . $host . "\nResult:\n" . $outputText;
        $queue['create'] = date('c');
        addAlertQueue($queue);
        return false;
    } else {
        echo $displayName . ' (' . $host . ') ping success...' . PHP_EOL;
        return true;
    }
}
?>
