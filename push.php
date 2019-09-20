<?php
require_once './discord.php';

$jsons = file_get_contents('./queues.json');
$jsons = json_decode($jsons, true);

foreach ($jsons as $key => $json) {
    $payload = [];
    $payload['content'] = "";
    $payload["title"] = $json['title'];
    $payload["text"] = $json['text'];
    $payload["create"] = $json['create'];
    $payload["author"] = "alert";
    $payload['embeds'] = getEmbeds($payload);

    sendMessage($payload, ALERT_WEBHOOK);
}
file_put_contents("./queues.json", json_encode([], JSON_UNESCAPED_UNICODE));

?>