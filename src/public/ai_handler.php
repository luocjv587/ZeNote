<?php
session_start();
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$input = json_decode(file_get_contents('php://input'), true);
$prompt = $input['prompt'] ?? '';
$context = $input['context'] ?? '';

if (!$prompt) {
    http_response_code(400);
    echo json_encode(['error' => 'Prompt required']);
    exit;
}

// Get API Key
$stmt = $pdo->prepare("SELECT aliyun_api_key, aliyun_model_name FROM z_user WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$apiKey = $user['aliyun_api_key'] ?? '';
$modelName = $user['aliyun_model_name'] ?? 'qwen-plus';

if (!$apiKey) {
    http_response_code(400);
    echo json_encode(['error' => 'Aliyun API Key not configured. Please go to Settings to configure it.']);
    exit;
}

// Prepare Messages
$messages = [];
if ($context) {
    $messages[] = [
        'role' => 'system',
        'content' => "You are a helpful AI assistant. The user will provide a context text. Please perform the task based on the context."
    ];
    $messages[] = [
        'role' => 'user',
        'content' => "Context:\n" . $context . "\n\nTask: " . $prompt
    ];
} else {
    $messages[] = [
        'role' => 'user',
        'content' => $prompt
    ];
}

// Call Aliyun API (OpenAI Compatible)
$url = "https://dashscope.aliyuncs.com/compatible-mode/v1/chat/completions";
$data = [
    "model" => $modelName,
    "messages" => $messages
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $apiKey
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($response === false) {
    $error = curl_error($ch);
    curl_close($ch);
    http_response_code(500);
    echo json_encode(['error' => 'Curl error: ' . $error]);
    exit;
}

curl_close($ch);

$responseData = json_decode($response, true);

if ($httpCode >= 200 && $httpCode < 300) {
    $content = $responseData['choices'][0]['message']['content'] ?? '';
    echo json_encode(['content' => $content]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'AI API Error: ' . ($responseData['message'] ?? $responseData['error']['message'] ?? 'Unknown error'), 'details' => $responseData]);
}
?>