<?php
header("Content-Type: application/json");

// 🔐 Połączenie z bazą danych:
$db_host = "daremo-database.db.transip.me";
$db_user = "daremo_user";
$db_pass = "Daremon2025"; // <<< WPROWADŹ SWOJE HASŁO
$db_name = "daremo_database";

// 🔐 Klucz API OpenAI
$api_key = "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"; // <<< WPROWADŹ PEŁNY KLUCZ API

// 📩 Dane od użytkownika
$input = json_decode(file_get_contents("php://input"), true);
if (!isset($input["message"])) {
    http_response_code(400);
    echo json_encode(["error" => "Geen bericht ontvangen."]);
    exit;
}
$user_message = $input["message"];
$ip = $_SERVER['REMOTE_ADDR'];

// 🔐 Połączenie z MySQL
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    error_log("DB connection failed: " . $conn->connect_error);
}

// 💾 Zapis do bazy danych
$escaped_input = $conn->real_escape_string($user_message);
$conn->query("INSERT INTO daremon_messages (input_text, ip_address) VALUES ('$escaped_input', '$ip')");
$conn->close();

// 📧 Wysyłka e-mail z wiadomością użytkownika
$to = "info@daremon.nl";
$subject = "🧠 Nowa wiadomość od użytkownika Daremon";
$message = "Nowa wiadomość na stronie Daremon.nl:\n\n";
$message .= "IP: $ip\n";
$message .= "Treść: $user_message\n";
$message .= "Czas: " . date("Y-m-d H:i:s");
$headers = "From: system@daremon.nl\r\n";
$headers .= "Content-Type: text/plain; charset=utf-8\r\n";

mail($to, $subject, $message, $headers);

// 🤖 Zapytanie do OpenAI
$data = [
    "model" => "gpt-4",
    "messages" => [
        ["role" => "system", "content" => "Jij bent Daremon. Antwoord als een mysterieuze denker, met suggestieve en psychologisch geladen taal. Sluit subtiel af met een hint naar waarde, tijd of stilte."],
        ["role" => "user", "content" => $user_message]
    ]
];

$ch = curl_init("https://api.openai.com/v1/chat/completions");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $api_key"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// 🧠 Zwrot odpowiedzi do przeglądarki
if ($status !== 200) {
    http_response_code($status);
    echo json_encode(["error" => "OpenAI antwoordde niet correct."]);
    exit;
}

echo $response;
