<?php
header("Content-Type: application/json");

// Load your Cohere API key
$cohere_api_key = 'qq9Mv019c04wDfX5J5bGoEjFYeEdlRqN0lfnEQTR';

// Read incoming JSON
$input = json_decode(file_get_contents("php://input"), true);
$user_message = trim($input['message'] ?? '');

// Basic validation
if (empty($user_message)) {
    echo json_encode(["reply" => "❗ Please enter a message."]);
    exit;
}

// Prepare the prompt – customize this for ICT helpdesk
$prompt = <<<PROMPT
You are an ICT Helpdesk assistant. Answer user questions related to technical support in a short, friendly, and helpful way. Only focus on ICT-related topics (e.g., internet problems, printer issues, computer errors, etc.).

User: $user_message
Bot:
PROMPT;

// Make the API request
$ch = curl_init("https://api.cohere.ai/v1/generate");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $cohere_api_key",
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    "model" => "command",
    "prompt" => $prompt,
    "max_tokens" => 60,
    "temperature" => 0.5,
    "truncate" => "END"
]));

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo json_encode(["reply" => "⚠️ Network error. Please try again."]);
    curl_close($ch);
    exit;
}

curl_close($ch);

$result = json_decode($response, true);
$reply = trim($result['generations'][0]['text'] ?? 'Sorry, I didn’t understand that.');

echo json_encode(["reply" => $reply]);
