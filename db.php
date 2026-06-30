<?php

require_once 'config.php';

try {
    // اتصال
    $pdo = new PDO("mysql:host=$mysql_server;dbname=$mysql_db;charset=utf8mb4", $mysql_username, $mysql_password);
    
    // مدیریت خطا
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    // پیام خطا
    die("خطا در اتصال به دیتابیس: " . $e->getMessage());
}
?>
