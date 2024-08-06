<?php
header('Content-Type: application/json');

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

// Подключение к базе данных MySQL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "visitors";

// Создаем подключение
$conn = new mysqli($servername, $username, $password, $dbname);
$ip = '127.0.0.1';

// Проверка соединения
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Connection failed: ' . $conn->connect_error]);
    exit();
}

// Записываем данные в базу данных
$stmt = $conn->prepare("INSERT INTO visitors_data (url, visiting_site, ip_user, time_stamp, scroll_percentage, history_click, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssss", $data['url'], $data['visiting_site'], $ip, $data['time_stamp'], $data['scroll_percentage'], $data['history_click'], $data['user_agent']);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Data recorded']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
