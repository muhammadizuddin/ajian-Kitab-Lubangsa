<?php
class DatabaseConfig {
    private static $host = 'localhost';
    private static $username = 'root';
    private static $password = '';
    private static $database = 'absensi_kajian_kitab';

    public static function getConnection() {
        try {
            $conn = new PDO("mysql:host=" . self::$host . ";dbname=" . self::$database, self::$username, self::$password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch(PDOException $e) {
            die("Koneksi database gagal: " . $e->getMessage());
        }
    }
}
