<?php
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['message']) || trim($data['message']) === '') {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Geen geldig bericht."]);
    exit;
}

$message = trim($data['message']);
$timestamp = date("Y-m-d H:i:s");
$ip = $_SERVER['REMOTE_ADDR'] ?? 'onbekend';
$ua = $_SERVER['HTTP_USER_AGENT'] ?? 'onbekend';

$log = "[$timestamp][$ip][$ua] $message\n";
file_put_contents("meldingen.log", $log, FILE_APPEND);

echo json_encode(["success" => true]);
?>
