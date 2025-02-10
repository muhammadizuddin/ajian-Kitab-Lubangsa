<?php
session_start();

// Daftar kitab dengan deskripsi
$daftarKitab = [
    'Fathul Qorib' => [
        'deskripsi' => 'Kitab fikih madzhab Syafi\'i yang membahas hukum-hukum ibadah dan muamalah.',
        'icon' => '📖'
    ],
    'Bulughul Maram' => [
        'deskripsi' => 'Kitab hadits yang berisi kumpulan hadits-hadits hukum dari berbagai kitab hadits.',
        'icon' => '📚'
    ],
    'Riyadhus Shalihin' => [
        'deskripsi' => 'Kitab hadits yang fokus pada akhlak dan pembinaan spiritual muslim.',
        'icon' => '🕯️'
    ],
    'Ta\'lim Muta\'alim' => [
        'deskripsi' => 'Kitab yang membahas etika dan adab menuntut ilmu dalam tradisi pesantren.',
        'icon' => '🌟'
    ]
];

// Proses pemilihan kitab
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['kitab'])) {
    $kitabTerpilih = $_POST['kitab'];
    
    $nama = $_POST['nama'] ?? '';
    $tanggal = $_POST['tanggal'] ?? '';
    $status = $_POST['status'] ?? '';
    
    // Simpan data sementara di session
    if (!isset($_SESSION['absensi'])) {
        $_SESSION['absensi'] = [];
    }
    
    $_SESSION['absensi'][] = [
        'nama' => $nama,
        'kitab' => $kitabTerpilih,
        'tanggal' => $tanggal,
        'status' => $status
    ];
    
    $_SESSION['message'] = "Absensi untuk $kitabTerpilih berhasil disimpan!";
    header("Location: absensi.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masukkan Absensi Kajian Kitab</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/styles.css" rel="stylesheet">
    <style>
        .kitab-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .kitab-card:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        .kitab-card.selected {
            border: 3px solid #3498db;
            background-color: rgba(52,152,219,0.1);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="../index.php">Absensi Kajian Kitab</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="absensi.php">Masukkan Absensi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../rekap.php">Rekap Absensi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../hukuman.php">Rekap Hukuman</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success">
                <?php 
                echo $_SESSION['message'];
                unset($_SESSION['message']);
                ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h4>Pilih Kitab untuk Absensi</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($daftarKitab as $namaKitab => $detailKitab): ?>
                        <div class="col-md-3 mb-4">
                            <div class="card kitab-card" data-kitab="<?= htmlspecialchars($namaKitab) ?>">
                                <div class="card-body text-center">
                                    <h3 class="card-title"><?= $detailKitab['icon'] ?> <?= htmlspecialchars($namaKitab) ?></h3>
                                    <p class="card-text"><?= htmlspecialchars($detailKitab['deskripsi']) ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Form Absensi (Tersembunyi sampai kitab dipilih) -->
                <form id="absensiForm" method="POST" action="" style="display:none;">
                    <input type="hidden" name="kitab" id="selectedKitab">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nama" class="form-label">Nama Mahasiswa</label>
                            <input type="text" class="form-control" id="nama" name="nama" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tanggal" class="form-label">Tanggal</label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="status" class="form-label">Status Kehadiran</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="">Pilih Status</option>
                                <option value="Hadir">Hadir</option>
                                <option value="Izin">Izin</option>
                                <option value="Sakit">Sakit</option>
                                <option value="Alpa">Alpa</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">Simpan Absensi</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Tambahkan event listener untuk kartu kitab
        document.querySelectorAll('.kitab-card').forEach(card => {
            card.addEventListener('click', function() {
                // Hapus kelas 'selected' dari semua kartu
                document.querySelectorAll('.kitab-card').forEach(c => c.classList.remove('selected'));
                
                // Tambahkan kelas 'selected' ke kartu yang diklik
                this.classList.add('selected');
                
                // Isi input tersembunyi dengan nama kitab
                const kitab = this.getAttribute('data-kitab');
                document.getElementById('selectedKitab').value = kitab;
                
                // Tampilkan form absensi
                document.getElementById('absensiForm').style.display = 'block';
            });
        });
    </script>
</body>
</html>
