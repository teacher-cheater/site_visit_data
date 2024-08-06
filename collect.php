<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Получаем данные из POST-запроса
$raw_data = file_get_contents('php://input');
$data = json_decode($raw_data, true);

if (!$data) {
    echo json_encode([
        'status' => 'error',
        'message' => 'No data received or JSON invalid',
        'raw_data' => $raw_data,
        'json_error' => json_last_error_msg()
    ]);
    exit();
}

function getIp()
{
    $keys = [
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'REMOTE_ADDR'
    ];
    foreach ($keys as $key) {
        if (!empty($_SERVER[$key])) {
            $ip = trim(end(explode(',', $_SERVER[$key])));
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
    }
    return $_SERVER['REMOTE_ADDR'];
}

$ip = getIp();

// Подключение к базе данных MySQL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "visitors";

$conn = new mysqli($servername, $username, $password, $dbname);

// Проверка соединения
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Connection failed: ' . $conn->connect_error]);
    exit();
}

// Записываем данные в базу данных
$stmt = $conn->prepare("INSERT INTO visitors_data (url, time_on_page, ip_user, time_stamp, scroll_percentage, history_click, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssss", $data['url'], $data['time_on_page'], $ip, $data['time_stamp'], $data['scroll_percentage'], $data['history_click'], $data['user_agent']);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Data recorded']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
