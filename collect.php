<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// ip адреса
class IpManager
{
    public static function getIp()
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
}

// работа с базой данных
class Database
{
    private $conn;

    public function __construct($servername, $username, $password, $dbname)
    {
        $this->conn = new mysqli($servername, $username, $password, $dbname);

        if ($this->conn->connect_error) {
            throw new Exception('Connection failed: ' . $this->conn->connect_error);
        }
    }

    public function insertVisitorData($url, $time_on_page, $ip_user, $time_stamp, $scroll_percentage, $history_click, $user_agent)
    {
        $stmt = $this->conn->prepare("INSERT INTO visitors_data (url, time_on_page, ip_user, time_stamp, scroll_percentage, history_click, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $this->conn->error);
        }

        $stmt->bind_param("sssssss", $url, $time_on_page, $ip_user, $time_stamp, $scroll_percentage, $history_click, $user_agent);

        if (!$stmt->execute()) {
            throw new Exception('Execute failed: ' . $stmt->error);
        }

        $stmt->close();
    }

    public function __destruct()
    {
        $this->conn->close();
    }
}

// обработка запросов
class RequestHandler
{
    private $data;

    public function __construct()
    {
        $raw_data = file_get_contents('php://input');
        $this->data = json_decode($raw_data, true);

        if (!$this->data) {
            echo json_encode([
                'status' => 'error',
                'message' => 'No data received or JSON invalid',
                'raw_data' => $raw_data,
                'json_error' => json_last_error_msg()
            ]);
            exit();
        }
    }

    public function handleRequest()
    {
        $ip = IpManager::getIp();
        $db = new Database("localhost", "root", "", "visitors");

        try {
            $db->insertVisitorData(
                $this->data['url'],
                $this->data['time_on_page'],
                $ip,
                $this->data['time_stamp'],
                $this->data['scroll_percentage'],
                $this->data['history_click'],
                $this->data['user_agent']
            );

            echo json_encode(['status' => 'success', 'message' => 'Data recorded']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
}

$requestHandler = new RequestHandler();
$requestHandler->handleRequest();
