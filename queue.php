<?php
function addAlertQueue($queue)
{
    $queues = file_get_contents("./queues.json");
    $queues = json_decode($queues, true);

    $new = true;
    if (0 <= count($queues)) {
        foreach ($queues as $key => $q) {
            if ($q['title'] === $queue['title']) {
                $new = false;
                break;
            }
        }
    }
    if ($new === true) $queues[] = $queue;

    file_put_contents("./queues.json", json_encode($queues, JSON_UNESCAPED_UNICODE));
}
?>