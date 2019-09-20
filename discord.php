<?php
define("ALERT_WEBHOOK", "https://discordapp.com/api/webhooks/*******/*********");

function sendMessage($payload = [], $webhook_url = '') {
    $ch = curl_init($webhook_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json; charser=UTF-8'
    ));
    $result = curl_exec($ch);
    curl_close($ch);
}

function getEmbeds($payload = []) {
    $payload['color'] = '000000';
    $payload["author_url"] = "https://avatars0.githubusercontent.com/u/10069690?s=460&v=4";
    return [
        [
            // Set the title for your embed
            "title" => $payload['title'],

            // The type of your embed, will ALWAYS be "rich"
            "type" => "rich",

            // A description for your embed
            "description" => $payload['text'],

            // The URL of where your title will be a link to
            "url" => $payload['link'],

            /* A timestamp to be displayed below the embed, IE for when an an article was posted
             * This must be formatted as ISO8601
             */
            "timestamp" => $payload['create'],

            // The integer color to be used on the left side of the embed
            "color" => isset($payload['color']) == true ? hexdec( $payload['color'] ) : hexdec( "000000" ),

            // Footer object
            "footer" => [
                "text" => $payload['footer_text'],
                "icon_url" => $payload['footer_icon_url']
            ],

            // Image object
            "image" => [
                "url" => $payload['src']
            ],

            // Author object
            "author" => [
                "name" => $payload['author'],
                "url" => $payload['author_url']
            ],
            "username" => $payload['author'],
            "avatar_url" => $payload['author_url'],
        ]
    ];
}