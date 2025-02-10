<?php
namespace Absensi\Models;

require_once __DIR__ . '/../../config/database/config.php';

class SantriModel {
    private $conn;

    public function __construct() {
        $this->conn = \DatabaseConfig::getConnection();
    }

    public function tambahSantri($nama, $nis, $kelas, $alamat) {
        $query = "INSERT INTO santri (nama, nis, kelas, alamat) VALUES (:nama, :nis, :kelas, :alamat)";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':nama', $nama);
        $stmt->bindParam(':nis', $nis);
        $stmt->bindParam(':kelas', $kelas);
        $stmt->bindParam(':alamat', $alamat);
        
        return $stmt->execute();
    }

    public function getDaftarSantri() {
        $query = "SELECT * FROM santri";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
