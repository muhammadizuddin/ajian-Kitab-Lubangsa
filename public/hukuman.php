<?php
session_start();

// Fungsi untuk menghitung jumlah alpa per santri
function hitungAlpa($dataAbsensi) {
    $rekapAlpa = [];
    
    foreach ($dataAbsensi as $absen) {
        $nama = $absen['nama'];
        $kitab = $absen['kitab'];
        $key = $nama . '_' . $kitab;
        
        if (!isset($rekapAlpa[$key])) {
            $rekapAlpa[$key] = [
                'nama' => $nama,
                'kitab' => $kitab,
                'total_hadir' => 0,
                'total_sesi' => 1,  // Mulai dari 1 sesi
                'total_alpa' => 0,
                'persentase_kehadiran' => 0,
                'hukuman' => 0
            ];
        } else {
            // Tambah total sesi jika sudah ada entri sebelumnya
            $rekapAlpa[$key]['total_sesi']++;
        }
        
        if ($absen['status'] == 'Hadir') {
            $rekapAlpa[$key]['total_hadir']++;
        }
        
        if ($absen['status'] == 'Alpa') {
            $rekapAlpa[$key]['total_alpa']++;
        }
    }
    
    // Hitung persentase kehadiran dan hukuman
    foreach ($rekapAlpa as &$santri) {
        $santri['persentase_kehadiran'] = round(($santri['total_hadir'] / $santri['total_sesi']) * 100, 2);
        $santri['hukuman'] = $santri['total_alpa'];
    }
    
    return $rekapAlpa;
}

// Ambil data absensi dari session
$dataAbsensi = !empty($_SESSION['absensi']) ? $_SESSION['absensi'] : [];
$rekapHukuman = hitungAlpa($dataAbsensi);

// Sorting berdasarkan total alpa (descending)
usort($rekapHukuman, function($a, $b) {
    return $b['total_alpa'] - $a['total_alpa'];
});
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Hukuman Santri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/styles.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Absensi Kajian Kitab</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="absensi.php">Masukkan Absensi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="rekap.php">Rekap Absensi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="hukuman.php">Rekap Hukuman</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <h4>Rekap Hukuman Santri</h4>
            </div>
            <div class="card-body">
                <?php if (!empty($rekapHukuman)): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Santri</th>
                                    <th>Kitab</th>
                                    <th>Total Hadir</th>
                                    <th>Total Sesi</th>
                                    <th>Persentase Kehadiran</th>
                                    <th>Total Alpa</th>
                                    <th>Hukuman Mengaji</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                foreach ($rekapHukuman as $santri): 
                                ?>
                                <tr <?= $santri['total_alpa'] > 0 ? 'class="table-warning"' : '' ?>>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($santri['nama']) ?></td>
                                    <td><?= htmlspecialchars($santri['kitab']) ?></td>
                                    <td><?= $santri['total_hadir'] ?></td>
                                    <td><?= $santri['total_sesi'] ?></td>
                                    <td><?= $santri['persentase_kehadiran'] ?>%</td>
                                    <td><?= $santri['total_alpa'] ?></td>
                                    <td>
                                        <?php if ($santri['hukuman'] > 0): ?>
                                            <?= $santri['hukuman'] ?> Jam Mengaji
                                        <?php else: ?>
                                            Tidak Ada Hukuman
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        Belum ada data absensi yang tersimpan.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
