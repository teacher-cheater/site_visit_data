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
                $ipList = explode(',', $_SERVER[$key]);
                $ip = trim(end($ipList));
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

    public function getVisitorData()
    {
        $result = $this->conn->query("SELECT * FROM visitors_data");
        if (!$result) {
            throw new Exception('Query failed: ' . $this->conn->error);
        }

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        return $data;
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
    private $method;
    private $raw_data;

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->raw_data = file_get_contents('php://input');
        $this->data = json_decode($this->raw_data, true);
    }

    public function handleRequest()
    {
        $db = new Database("localhost", "root", "", "visitors");

        if ($this->method == 'POST') {
            if (!$this->data) {
                $this->respondWithError('No data received or JSON invalid', $this->raw_data, json_last_error_msg());
            }

            $ip = IpManager::getIp();

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

                $this->respondWithSuccess('Data recorded');
            } catch (Exception $e) {
                $this->respondWithError('Error: ' . $e->getMessage());
            }
        } elseif ($this->method == 'GET') {
            try {
                $data = $db->getVisitorData();
                echo json_encode(['status' => 'success', 'data' => $data]);
            } catch (Exception $e) {
                $this->respondWithError('Error: ' . $e->getMessage());
            }
        }
    }

    private function respondWithSuccess($message)
    {
        echo json_encode(['status' => 'success', 'message' => $message]);
        exit();
    }

    private function respondWithError($message, $raw_data = '', $json_error = '')
    {
        echo json_encode([
            'status' => 'error',
            'message' => $message,
            'raw_data' => $raw_data,
            'json_error' => $json_error
        ]);
        exit();
    }
}

$requestHandler = new RequestHandler();
$requestHandler->handleRequest();
