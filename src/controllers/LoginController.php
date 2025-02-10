<?php
namespace Absensi\Controllers;

use Absensi\Models\AdminModel;

class LoginController {
    private $adminModel;

    public function __construct() {
        $this->adminModel = new AdminModel();
    }

    public function prosesLogin($username, $password) {
        // Validasi input
        if (empty($username) || empty($password)) {
            return [
                'success' => false,
                'message' => 'Username dan password harus diisi'
            ];
        }

        // Coba login
        $admin = $this->adminModel->login($username, $password);

        if ($admin) {
            // Mulai session
            session_start();
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['username'] = $admin['username'];
            $_SESSION['nama'] = $admin['nama'];
            $_SESSION['logged_in'] = true;

            return [
                'success' => true,
                'message' => 'Login berhasil',
                'redirect' => 'public/index.php'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Username atau password salah'
            ];
        }
    }

    public function logout() {
        session_start();
        session_destroy();
        return [
            'success' => true,
            'message' => 'Logout berhasil',
            'redirect' => 'login.php'
        ];
    }

    public function cekLogin() {
        session_start();
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header('Location: login.php');
            exit();
        }
    }
}
