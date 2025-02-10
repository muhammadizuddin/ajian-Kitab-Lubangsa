<?php
namespace Absensi\Models;

require_once __DIR__ . '/../../config/database/config.php';

class AdminModel {
    private $conn;

    public function __construct() {
        $this->conn = \DatabaseConfig::getConnection();
    }

    public function login($username, $password) {
        try {
            $query = "SELECT * FROM admin WHERE username = :username";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            
            $admin = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if ($admin && password_verify($password, $admin['password'])) {
                // Update last login
                $updateQuery = "UPDATE admin SET last_login = NOW() WHERE id = :id";
                $updateStmt = $this->conn->prepare($updateQuery);
                $updateStmt->bindParam(':id', $admin['id']);
                $updateStmt->execute();
                
                return $admin;
            }
            
            return false;
        } catch(\PDOException $e) {
            // Log error
            error_log("Login error: " . $e->getMessage());
            return false;
        }
    }

    public function updatePassword($username, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $query = "UPDATE admin SET password = :password WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':username', $username);
        
        return $stmt->execute();
    }
}
