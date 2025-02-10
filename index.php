<?php
session_start();

// Fungsi untuk menghitung statistik
function hitungStatistik($dataAbsensi) {
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
        // Hitung total santri unik
        $statistik['total_santri'][$absen['nama']] = true;
        
        // Hitung total kitab unik
        $statistik['total_kitab'][$absen['kitab']] = true;
        
        // Hitung status absensi
        $statistik['status_absensi'][$absen['status']]++;
    }

    $statistik['total_santri'] = count($statistik['total_santri']);
    $statistik['total_kitab'] = count($statistik['total_kitab']);

    return $statistik;
}

// Ambil data absensi dari session
$dataAbsensi = !empty($_SESSION['absensi']) ? $_SESSION['absensi'] : [];
$statistik = hitungStatistik($dataAbsensi);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Rekap Absensi Kajian Kitab</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
    <style>
        .dashboard-card {
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .dashboard-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .bg-gradient-primary {
            background: linear-gradient(to right, #4e73df 0%, #224abe 100%);
        }
        .bg-gradient-success {
            background: linear-gradient(to right, #1cc88a 0%, #13855f 100%);
        }
        .bg-gradient-warning {
            background: linear-gradient(to right, #f6c23e 0%, #dda20a 100%);
        }
        .bg-gradient-danger {
            background: linear-gradient(to right, #e74a3b 0%, #c0392b 100%);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-book-reader"></i> Absensi Kajian Kitab
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            <i class="fas fa-home"></i> Beranda
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="absensi.php">
                            <i class="fas fa-clipboard-list"></i> Input Absensi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="rekap.php">
                            <i class="fas fa-chart-bar"></i> Rekap Absensi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="hukuman.php">
                            <i class="fas fa-exclamation-triangle"></i> Rekap Hukuman
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="alert alert-primary" role="alert">
                    <h4 class="alert-heading">
                        <i class="fas fa-mosque"></i> Sistem Rekap Absensi Kajian Kitab
                    </h4>
                    <p>Selamat datang di sistem manajemen absensi kajian kitab PPA. Lubangsa. Pantau kehadiran dan perkembangan santri dengan mudah.</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card dashboard-card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Santri</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?= $statistik['total_santri'] ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card dashboard-card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Total Kitab</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?= $statistik['total_kitab'] ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-book fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card dashboard-card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Total Absensi</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?= $statistik['total_absensi'] ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card dashboard-card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    Total Alpa</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?= $statistik['status_absensi']['Alpa'] ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-chart-pie"></i> Status Absensi
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-pie pt-4 pb-2">
                            <canvas id="statusAbsensiChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-info-circle"></i> Informasi Sistem
                        </h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Hadir
                                <span class="badge bg-primary rounded-pill">
                                    <?= $statistik['status_absensi']['Hadir'] ?>
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Izin
                                <span class="badge bg-success rounded-pill">
                                    <?= $statistik['status_absensi']['Izin'] ?>
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Sakit
                                <span class="badge bg-warning rounded-pill">
                                    <?= $statistik['status_absensi']['Sakit'] ?>
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Alpa
                                <span class="badge bg-danger rounded-pill">
                                    <?= $statistik['status_absensi']['Alpa'] ?>
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Grafik Status Absensi
        const ctx = document.getElementById('statusAbsensiChart').getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Hadir', 'Izin', 'Sakit', 'Alpa'],
                datasets: [{
                    data: [
                        <?= $statistik['status_absensi']['Hadir'] ?>,
                        <?= $statistik['status_absensi']['Izin'] ?>,
                        <?= $statistik['status_absensi']['Sakit'] ?>,
                        <?= $statistik['status_absensi']['Alpa'] ?>
                    ],
                    backgroundColor: [
                        '#1cc88a', // Hadir (Hijau)
                        '#f6c23e', // Izin (Kuning)
                        '#36b9cc', // Sakit (Biru)
                        '#e74a3b'  // Alpa (Merah)
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    </script>
</body>
</html>
