<?php
class SessionHelper
{
    public static function start()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
    public static function isLoggedIn()
    {
        self::start();
        return isset($_SESSION['email']);
    }
    public static function isAdmin()
    {
        self::start();
        return isset($_SESSION['email']) && isset($_SESSION['role']) &&
            $_SESSION['role'] === 'admin';
    }
    public static function isUser()
    {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'user'; // Kiểm tra quyền user
    }
    public static function getRole()
    {
        self::start();
        return $_SESSION['role'] ?? 'guest';
    }
    // Thêm phương thức kiểm tra role cụ thể
    public static function hasRole($role)
    {
        self::start();
        return isset($_SESSION['role']) && $_SESSION['role'] === $role;
    }
}
