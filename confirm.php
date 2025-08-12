<?php
// confirm.php — pseudologiczny punkt dostępu Daremon

header('Content-Type: application/json');
header('X-Daremon-Acknowledged: false');

// Wymagany parametr: token
$token = $_GET['token'] ?? '';

$log = [
  "timestamp" => date("c"),
  "origin" => $_SERVER['REMOTE_ADDR'],
  "status" => "shadow",
  "tokenHash" => hash('sha256', $token),
  "response" => "Delta fase afgebroken. Bron niet gevalideerd.",
  "trace" => "Boxtel-X/segment-Δe7"
];

if ($token === "Δ-93-ACTIVE") {
  $log['X-Daremon-Acknowledged'] = true;
  header('X-Daremon-Acknowledged: true');
  $log['response'] = "Bevestiging ontvangen. Initiatie wacht op synchronisatie.";
  $log['status'] = "pending";
}

echo json_encode($log, JSON_PRETTY_PRINT);
exit;
