<?php
if (!defined('BASE_URL')) define('BASE_URL', '');

require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/models/AccountModel.php';
require_once __DIR__ . '/../app/models/CartModel.php';
require_once __DIR__ . '/ExcelDataHelper.php';

function createTestDatabase(): PDO
{
    $pdo = new PDO('sqlite::memory:');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

    $pdo->exec("CREATE TABLE account (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        email TEXT NOT NULL UNIQUE, full_name TEXT NOT NULL, password TEXT NOT NULL,
        address TEXT, phone_number TEXT, birth_date TEXT,
        role TEXT NOT NULL DEFAULT 'user',
        is_active INTEGER NOT NULL DEFAULT 1, is_verified INTEGER NOT NULL DEFAULT 0,
        verification_token TEXT, token_expiry TEXT, reset_token TEXT,
        reset_token_expiry TEXT, image TEXT, last_login TEXT,
        created_at TEXT NOT NULL DEFAULT (datetime('now'))
    )");

    $pdo->exec("CREATE TABLE cart (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        account_id INTEGER NOT NULL, product_id INTEGER NOT NULL,
        quantity INTEGER NOT NULL DEFAULT 1, toggle INTEGER NOT NULL DEFAULT 1,
        UNIQUE(account_id, product_id)
    )");

    $pdo->exec("CREATE TABLE product (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL, price REAL NOT NULL,
        quantity INTEGER NOT NULL DEFAULT 0, image TEXT, category_id INTEGER
    )");

    $pdo->exec("CREATE TABLE orders (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        account_id INTEGER NOT NULL,
        total_amount REAL NOT NULL,
        transaction_id TEXT,
        payment_method TEXT NOT NULL DEFAULT 'cash',
        status TEXT NOT NULL DEFAULT 'pending',
        address TEXT,
        phone_number TEXT,
        shipping_fee REAL DEFAULT 0,
        promotion_id INTEGER,
        discount_amount REAL DEFAULT 0,
        created_at TEXT DEFAULT (datetime('now'))
    )");

    $pdo->exec("CREATE TABLE order_details (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        order_id INTEGER NOT NULL,
        product_id INTEGER NOT NULL,
        quantity INTEGER NOT NULL,
        price REAL NOT NULL
    )");

    return $pdo;
}

class TestAccountModel extends AccountModel
{
    private $conn;

    public function __construct($db)
    {
        parent::__construct($db);
        $this->conn = $db;
    }

    public function updateLastLogin($email): bool
    {
        $stmt = $this->conn->prepare("UPDATE account SET last_login = datetime('now') WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function save($email, $fullName, $password, $address, $phoneNumber,
                         $birthDate, $otp, $expiry, $role = 'user'): bool
    {
        if ($this->getAccountByEmail($email)) return false;

        $stmt = $this->conn->prepare("
            INSERT INTO account (email, full_name, password, address, phone_number,
                birth_date, role, is_active, is_verified, verification_token, token_expiry, created_at)
            VALUES (:email, :full_name, :password, :address, :phone_number,
                :birth_date, :role, 1, 0, :otp, :expiry, datetime('now'))
        ");
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt->bindParam(':email',        $email);
        $stmt->bindParam(':full_name',    $fullName);
        $stmt->bindParam(':password',     $hash);
        $stmt->bindParam(':address',      $address);
        $stmt->bindParam(':phone_number', $phoneNumber);
        $stmt->bindParam(':birth_date',   $birthDate);
        $stmt->bindParam(':role',         $role);
        $stmt->bindParam(':otp',          $otp);
        $stmt->bindParam(':expiry',       $expiry);
        return $stmt->execute();
    }
}
