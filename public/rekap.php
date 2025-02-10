<?php
session_start();

// Fungsi untuk mengumpulkan catatan kehadiran
function aggregateAttendance($attendanceData) {
    $aggregatedData = [];
    $totalSessionsByKitab = [];
    
    foreach ($attendanceData as $record) {
        $key = $record['nama'] . '_' . $record['kitab'];
        $kitab = $record['kitab'];
        
        // Track total sessions for each kitab
        if (!isset($totalSessionsByKitab[$kitab])) {
            $totalSessionsByKitab[$kitab] = 0;
        }
        $totalSessionsByKitab[$kitab]++;
        
        if (!isset($aggregatedData[$key])) {
            $aggregatedData[$key] = [
                'nama' => $record['nama'],
                'kitab' => $record['kitab'],
                'hadir' => 0,
                'tanggal' => []
            ];
        }
        
        if ($record['status'] == 'Hadir') {
            $aggregatedData[$key]['hadir']++;
            $aggregatedData[$key]['tanggal'][] = $record['tanggal'];
        }
    }
    
    // Calculate percentage for each student
    foreach ($aggregatedData as &$data) {
        $kitab = $data['kitab'];
        $totalSessions = $totalSessionsByKitab[$kitab];
        $data['persentase'] = round(($data['hadir'] / $totalSessions) * 100, 2);
    }
    
    return $aggregatedData;
}

// Proses penghapusan data
if (isset($_GET['hapus']) && isset($_SESSION['absensi'])) {
    $indexToDelete = $_GET['hapus'];
    if (isset($_SESSION['absensi'][$indexToDelete])) {
        unset($_SESSION['absensi'][$indexToDelete]);
        // Re-index array
        $_SESSION['absensi'] = array_values($_SESSION['absensi']);
    }
}

// Proses edit data
if (isset($_POST['edit_index']) && isset($_SESSION['absensi'])) {
    $indexToEdit = $_POST['edit_index'];
    if (isset($_SESSION['absensi'][$indexToEdit])) {
        $_SESSION['absensi'][$indexToEdit] = [
            'nama' => $_POST['nama'],
            'kitab' => $_POST['kitab'],
            'tanggal' => $_POST['tanggal'],
            'status' => $_POST['status']
        ];
    }
}

// Fungsi pencarian
function cariData($data, $keyword) {
    if (empty($keyword)) return $data;
    
    $hasilPencarian = [];
    $keyword = strtolower($keyword);
    
    foreach ($data as $index => $absen) {
        if (
            strpos(strtolower($absen['nama']), $keyword) !== false ||
            strpos(strtolower($absen['kitab']), $keyword) !== false ||
            strpos(strtolower($absen['tanggal']), $keyword) !== false ||
            strpos(strtolower($absen['status']), $keyword) !== false
        ) {
            $hasilPencarian[$index] = $absen;
        }
    }
    
    return $hasilPencarian;
}

// Ambil keyword pencarian
$keyword = $_GET['cari'] ?? '';
$dataAbsensi = !empty($_SESSION['absensi']) ? $_SESSION['absensi'] : [];
$hasilPencarian = cariData($dataAbsensi, $keyword);

// Kumpulkan kehadiran jika data ada
$aggregatedAttendance = !empty($hasilPencarian) ? aggregateAttendance($hasilPencarian) : [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Absensi Kajian Kitab</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/styles.css" rel="stylesheet">
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
                        <a class="nav-link" href="../absensi.php">Masukkan Absensi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="rekap.php">Rekap Absensi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../hukuman.php">Rekap Hukuman</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <h4>Rekap Absensi Kajian Kitab</h4>
            </div>
            <div class="card-body">
                <!-- Form Pencarian -->
                <form method="GET" action="rekap.php" class="mb-3">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Cari berdasarkan nama, kitab, tanggal, atau status" 
                               name="cari" value="<?= htmlspecialchars($keyword) ?>">
                        <button class="btn btn-primary" type="submit">Cari</button>
                        <?php if (!empty($keyword)): ?>
                            <a href="rekap.php" class="btn btn-secondary">Reset</a>
                        <?php endif; ?>
                    </div>
                </form>

                <?php if (!empty($hasilPencarian)): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Mahasiswa</th>
                                    <th>Kitab</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th>Persentase</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                foreach ($hasilPencarian as $index => $absen): 
                                ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($absen['nama']) ?></td>
                                    <td><?= htmlspecialchars($absen['kitab']) ?></td>
                                    <td><?= htmlspecialchars($absen['tanggal']) ?></td>
                                    <td><?= htmlspecialchars($absen['status']) ?></td>
                                    <td>
                                        <?php 
                                        $key = $absen['nama'] . '_' . $absen['kitab'];
                                        if (isset($aggregatedAttendance[$key])) {
                                            echo $aggregatedAttendance[$key]['persentase'] . '%';
                                        } else {
                                            echo '0%';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-warning edit-btn" data-index="<?= $index ?>" 
                                                data-nama="<?= htmlspecialchars($absen['nama']) ?>"
                                                data-kitab="<?= htmlspecialchars($absen['kitab']) ?>"
                                                data-tanggal="<?= htmlspecialchars($absen['tanggal']) ?>"
                                                data-status="<?= htmlspecialchars($absen['status']) ?>">
                                            Edit
                                        </button>
                                        <a href="rekap.php?hapus=<?= $index ?>&cari=<?= urlencode($keyword) ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Anda yakin ingin menghapus data ini?')">Hapus</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Modal Edit (sama seperti sebelumnya) -->
                    <div class="modal fade" id="editModal" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="POST" action="rekap.php?cari=<?= urlencode($keyword) ?>">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Data Absensi</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="edit_index" id="edit-index">
                                        <div class="mb-3">
                                            <label class="form-label">Nama Mahasiswa</label>
                                            <input type="text" class="form-control" name="nama" id="edit-nama" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Kitab</label>
                                            <select class="form-select" name="kitab" id="edit-kitab" required>
                                                <option value="Fathul Qorib">Fathul Qorib</option>
                                                <option value="Bulughul Maram">Bulughul Maram</option>
                                                <option value="Riyadhus Shalihin">Riyadhus Shalihin</option>
                                                <option value="Ta'lim Muta'alim">Ta'lim Muta'alim</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Tanggal</label>
                                            <input type="date" class="form-control" name="tanggal" id="edit-tanggal" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Status</label>
                                            <select class="form-select" name="status" id="edit-status" required>
                                                <option value="Hadir">Hadir</option>
                                                <option value="Izin">Izin</option>
                                                <option value="Sakit">Sakit</option>
                                                <option value="Alpa">Alpa</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($aggregatedAttendance)): ?>
                    <div class="card mt-4">
                        <div class="card-header">
                            <h4>Rekap Kehadiran per Kitab</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Mahasiswa</th>
                                            <th>Kitab</th>
                                            <th>Jumlah Hadir</th>
                                            <th>Tanggal Hadir</th>
                                            <th>Persentase Kehadiran</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $no = 1;
                                        foreach ($aggregatedAttendance as $absen): 
                                        ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= htmlspecialchars($absen['nama']) ?></td>
                                            <td><?= htmlspecialchars($absen['kitab']) ?></td>
                                            <td><?= $absen['hadir'] ?></td>
                                            <td><?= implode(', ', array_unique($absen['tanggal'])) ?></td>
                                            <td><?= $absen['persentase'] ?>%</td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="alert alert-info">
                        <?= empty($keyword) ? 'Belum ada data absensi yang tersimpan.' : 'Tidak ada data yang cocok dengan pencarian Anda.' ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Tambahkan event listener untuk tombol edit
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                // Ambil data dari atribut
                const index = this.getAttribute('data-index');
                const nama = this.getAttribute('data-nama');
                const kitab = this.getAttribute('data-kitab');
                const tanggal = this.getAttribute('data-tanggal');
                const status = this.getAttribute('data-status');

                // Isi modal dengan data
                document.getElementById('edit-index').value = index;
                document.getElementById('edit-nama').value = nama;
                document.getElementById('edit-kitab').value = kitab;
                document.getElementById('edit-tanggal').value = tanggal;
                document.getElementById('edit-status').value = status;

                // Tampilkan modal
                var editModal = new bootstrap.Modal(document.getElementById('editModal'));
                editModal.show();
            });
        });
    </script>
</body>
</html>
