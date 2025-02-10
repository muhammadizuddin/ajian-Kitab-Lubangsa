<?php
namespace Absensi\Controllers;

class AbsensiController {
    public function inputAbsensi($data) {
        if (!isset($_SESSION['absensi'])) {
            $_SESSION['absensi'] = [];
        }
        
        $_SESSION['absensi'][] = $data;
        return true;
    }

    public function getAbsensi() {
        return !empty($_SESSION['absensi']) ? $_SESSION['absensi'] : [];
    }

    public function hitungStatistik($dataAbsensi) {
        $statistik = [
            'total_santri' => [],
            'total_kitab' => [],
            'total_absensi' => count($dataAbsensi),
            'status_absensi' => [
                'Hadir' => 0,
                'Izin' => 0,
                'Sakit' => 0,
                'Alpa' => 0
            ]
        ];

        foreach ($dataAbsensi as $absen) {
            $statistik['total_santri'][$absen['nama']] = true;
            $statistik['total_kitab'][$absen['kitab']] = true;
            $statistik['status_absensi'][$absen['status']]++;
        }

        $statistik['total_santri'] = count($statistik['total_santri']);
        $statistik['total_kitab'] = count($statistik['total_kitab']);

        return $statistik;
    }
}
