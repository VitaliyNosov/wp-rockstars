<?php
/**
 * Test Telegram Bot Integration
 * 
 * This file tests if Telegram API is working correctly.
 * Open this file in browser: http://localhost:8081/wp-content/themes/rock-stars/test-telegram.php
 */

// Telegram bot credentials
$token   = '8100915185:AAGLXVCO8DjTm_cB2nx9BoayQzyUvqhZOR0';
$chat_id = '422713968';

// Test message
$test_message = "🧪 TEST MESSAGE\n\n";
$test_message .= "Время: " . date('Y-m-d H:i:s') . "\n";
$test_message .= "Это тестовое сообщение для проверки работы Telegram бота.\n\n";
$test_message .= "Если вы видите это сообщение, значит интеграция работает! ✅";

echo "<!DOCTYPE html>\n";
echo "<html>\n";
echo "<head>\n";
echo "    <meta charset='UTF-8'>\n";
echo "    <title>Telegram Bot Test</title>\n";
echo "    <style>\n";
echo "        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; background: #f5f5f5; }\n";
echo "        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }\n";
echo "        h1 { color: #333; border-bottom: 3px solid #667eea; padding-bottom: 10px; }\n";
echo "        .info { background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #2196f3; }\n";
echo "        .success { background: #e8f5e9; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #4caf50; }\n";
echo "        .error { background: #ffebee; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #f44336; }\n";
echo "        .code { background: #f5f5f5; padding: 10px; border-radius: 5px; font-family: monospace; overflow-x: auto; }\n";
echo "        pre { margin: 0; white-space: pre-wrap; word-wrap: break-word; }\n";
echo "    </style>\n";
echo "</head>\n";
echo "<body>\n";
echo "    <div class='container'>\n";
echo "        <h1>🤖 Telegram Bot Test</h1>\n";

echo "        <div class='info'>\n";
echo "            <strong>📋 Информация:</strong><br>\n";
echo "            Token: " . substr($token, 0, 20) . "...<br>\n";
echo "            Chat ID: $chat_id<br>\n";
echo "            Время теста: " . date('Y-m-d H:i:s') . "\n";
echo "        </div>\n";

echo "        <h2>Отправка тестового сообщения...</h2>\n";

// Send message to Telegram
$url = "https://api.telegram.org/bot{$token}/sendMessage";

$data = array(
    'chat_id' => $chat_id,
    'text' => $test_message,
    'parse_mode' => 'HTML'
);

// Using cURL for better error handling
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

// Parse response
$response_data = json_decode($response, true);

// Display results
if ($http_code == 200 && isset($response_data['ok']) && $response_data['ok'] === true) {
    echo "        <div class='success'>\n";
    echo "            <strong>✅ УСПЕХ!</strong><br>\n";
    echo "            Сообщение успешно отправлено в Telegram!<br>\n";
    echo "            Message ID: " . $response_data['result']['message_id'] . "\n";
    echo "        </div>\n";
    
    echo "        <h3>📱 Проверьте Telegram</h3>\n";
    echo "        <p>Откройте Telegram и проверьте, что сообщение пришло в чат.</p>\n";
} else {
    echo "        <div class='error'>\n";
    echo "            <strong>❌ ОШИБКА!</strong><br>\n";
    
    if ($curl_error) {
        echo "            cURL Error: $curl_error<br>\n";
    }
    
    echo "            HTTP Code: $http_code<br>\n";
    
    if (isset($response_data['description'])) {
        echo "            Telegram Error: " . $response_data['description'] . "<br>\n";
    }
    echo "        </div>\n";
}

// Show full response for debugging
echo "        <h3>🔍 Полный ответ от Telegram API:</h3>\n";
echo "        <div class='code'>\n";
echo "            <pre>" . htmlspecialchars(json_encode($response_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . "</pre>\n";
echo "        </div>\n";

// Test bot info
echo "        <h3>ℹ️ Информация о боте:</h3>\n";
$bot_info_url = "https://api.telegram.org/bot{$token}/getMe";
$bot_info = file_get_contents($bot_info_url);
$bot_data = json_decode($bot_info, true);

if (isset($bot_data['ok']) && $bot_data['ok'] === true) {
    echo "        <div class='success'>\n";
    echo "            <strong>Бот активен:</strong><br>\n";
    echo "            Имя: @" . $bot_data['result']['username'] . "<br>\n";
    echo "            ID: " . $bot_data['result']['id'] . "<br>\n";
    echo "            Имя бота: " . $bot_data['result']['first_name'] . "\n";
    echo "        </div>\n";
} else {
    echo "        <div class='error'>\n";
    echo "            <strong>Не удалось получить информацию о боте</strong>\n";
    echo "        </div>\n";
}

echo "        <div class='code'>\n";
echo "            <pre>" . htmlspecialchars(json_encode($bot_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . "</pre>\n";
echo "        </div>\n";

echo "    </div>\n";
echo "</body>\n";
echo "</html>\n";
