<?php
$data = json_decode(file_get_contents('php://input'), true);
$message = trim($data['message'] ?? '');

if ($message) {
  $timestamp = date("Y-m-d H:i:s");
  file_put_contents("meldingen.log", "[$timestamp] $message\n", FILE_APPEND);
  echo json_encode(['success' => true]);
} else {
  echo json_encode(['success' => false]);
}
$userAgent = $_SERVER['HTTP_USER_AGENT'];
$ip = $_SERVER['REMOTE_ADDR'];
$timestamp = date("Y-m-d H:i:s");

file_put_contents("meldingen.log",
    "[$timestamp][$ip][$userAgent] $message\n", FILE_APPEND);
