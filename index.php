<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
date_default_timezone_set("Asia/Dhaka");


$requestMethod = strtoupper($_SERVER['REQUEST_METHOD']);
if ($requestMethod != "POST") {
    header($_SERVER["SERVER_PROTOCOL"] . " 204 method not allowed", true, 204);
    echo json_encode(['error' => 'Method not allowed']);

    exit;
} elseif (!isset($_SERVER['PHP_AUTH_USER'])) {
    header("WWW-Authenticate: Basic realm=\"Private Area\"");
    header("HTTP/1.0 401 Unauthorized");
    echo json_encode(['error' => 'Sorry, you need proper credentials']);

    exit;
} else {
    if ($_SERVER['USER'] == "johndeo" && $_SERVER['PASS'] == "123") {
        header("HTTP/1.1 200 OK");
        header('Content-Type: application/json');
    } else {
        header("WWW-Authenticate: Basic realm=\"Private Area\"");
        header("HTTP/1.0 401 Unauthorized");
        echo json_encode(['error' => 'Wrong username and password']);

        exit;
    }
}

$input = file_get_contents('php://input');
$webhook = json_decode($input, true);

$app_id = "demochatting_app";
$sender = $webhook['webhook']['info']['sender'];
$receiver = $webhook['webhook']['info']['receiver'];
$app = $webhook['webhook']['info']['app_id'];
$message_type = $webhook['webhook']['message']['type'];
$message = $webhook['webhook']['message']['message'];
$media = $webhook['webhook']['message']['media'];
$date = $webhook['webhook']['date']['date'];
$date_time = $webhook['webhook']['date']['date_time'];
$timestamp = $webhook['webhook']['date']['timestamp'];

if (isset($message) || isset($media)) {
    if ($app_id != "demochatting_app") {
        header("404 Unknown Sender", true, 400);
        echo json_encode(['error' => 'Unknown Sender.']);

        exit;
    }
} else {
    header("400 Wrong Input", true, 400);
    echo json_encode(['error' => 'wrong input']);

    exit;
}

if (isset($message_type)) {
    if ($message_type == "text") {
        $response =  [
            'info' => [
                'receiver' => $receiver,
                'sender' => $sender
            ],
            'message' => [
                'message_type' => $message_type,
                'message' => $message
            ],
            'date' => [
                'date' => $date_time
            ]
        ];
    } else {
        $response =  [
            'info' => [
                'receiver' => $receiver,
                'sender' => $sender
            ],
            'message' => [
                'message_type' => $message_type,
                'media' => $media
            ],
            'date' => [
                'date' => $date_time
            ]
        ];
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($response));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $response = curl_exec($ch);
    curl_close($ch);

    echo $response;
} else {
    header("400 Wrong Input", true, 400);
    echo json_encode(['error' => 'wrong input']);

    exit;
}
