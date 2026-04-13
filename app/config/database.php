<?php
// Tự động detect môi trường:
// - Render/production: BASE_URL = ''
// - Local Laragon:     BASE_URL = '/webbanhang'
$_detected_base = '';
if (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
    $_detected_base = '/webbanhang';
}
define('BASE_URL', getenv('BASE_URL') !== false ? getenv('BASE_URL') : $_detected_base);

class Database
{
    // Sử dụng getenv() để lấy giá trị từ cấu hình của Render
    private $host;
    private $db_name;
    private $username;
    private $password;
    public $conn;

    public function __construct() {
        // Lấy từ biến môi trường, nếu không có thì mới dùng giá trị mặc định (cho local)
        $this->host = getenv('DB_HOST') ?: "localhost";
        $this->db_name = getenv('DB_DATABASE') ?: "my_store";
        $this->username = getenv('DB_USERNAME') ?: "root";
        $this->password = getenv('DB_PASSWORD') ?: "";
    }

    public function getConnection()
    {
        $this->conn = null;
        try {
            // Thêm port nếu cần thiết (Aiven thường dùng port khác 3306)
            $port = getenv('DB_PORT') ?: "3306";
            $dsn = "mysql:host=" . $this->host . ";port=" . $port . ";dbname=" . $this->db_name;
            // Aiven yêu cầu SSL
            $options = [
                PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
                PDO::ATTR_PERSISTENT => true,
            ];
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            $this->conn->exec("set names utf8");
        } catch (PDOException $exception) {
            // LƯU Ý: Xóa hoặc comment dòng throw new Exception này khi deploy 
            // để thấy lỗi thật sự (ví dụ lỗi sai pass hay sai host)
            error_log("Connection error: " . $exception->getMessage());
            die("Lỗi kết nối CSDL: " . $exception->getMessage()); 
        }
        return $this->conn;
    }
}