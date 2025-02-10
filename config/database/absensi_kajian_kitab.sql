-- Buat database
CREATE DATABASE IF NOT EXISTS absensi_kajian_kitab;
USE absensi_kajian_kitab;

-- Tabel Santri
CREATE TABLE santri (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    nis VARCHAR(20) UNIQUE NOT NULL,
    kelas VARCHAR(50) NOT NULL,
    alamat TEXT
);

-- Tabel Kitab
CREATE TABLE kitab (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kitab VARCHAR(100) NOT NULL,
    deskripsi TEXT
);

-- Tabel Absensi
CREATE TABLE absensi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    santri_id INT NOT NULL,
    kitab_id INT NOT NULL,
    tanggal DATE NOT NULL,
    status ENUM('Hadir', 'Izin', 'Sakit', 'Alpa') NOT NULL,
    FOREIGN KEY (santri_id) REFERENCES santri(id),
    FOREIGN KEY (kitab_id) REFERENCES kitab(id)
);

-- Tabel Hukuman
CREATE TABLE hukuman (
    id INT AUTO_INCREMENT PRIMARY KEY,
    santri_id INT NOT NULL,
    kitab_id INT NOT NULL,
    total_alpa INT NOT NULL,
    jam_hukuman INT NOT NULL,
    tanggal_hukuman DATE,
    FOREIGN KEY (santri_id) REFERENCES santri(id),
    FOREIGN KEY (kitab_id) REFERENCES kitab(id)
);

-- Tabel Admin
CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    last_login DATETIME
);

-- Tabel User
CREATE TABLE user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    last_login DATETIME
);

-- Insert data contoh Kitab
INSERT INTO kitab (nama_kitab, deskripsi) VALUES 
('Fathul Qorib', 'Kitab fiqh klasik'),
('Bulughul Maram', 'Kitab hadits'),
('Riyadhus Shalihin', 'Kitab akhlak dan adab'),
('Ta\'lim Muta\'alim', 'Kitab etika belajar');

-- Tambah admin default
INSERT INTO admin (username, password, nama, email) VALUES 
('izud', '$2y$10$Ry1Ld5Ld5Ld5Ld5Ld5Ld5OuJQlZbYXZBjXZBjXZBjXZBjX', 'Izud Admin', 'izud@annuqayah.com');

-- Prosedur untuk menghitung hukuman
DELIMITER //
CREATE PROCEDURE HitungHukuman()
BEGIN
    -- Hapus data hukuman lama
    TRUNCATE TABLE hukuman;
    
    -- Hitung hukuman berdasarkan total alpa
    INSERT INTO hukuman (santri_id, kitab_id, total_alpa, jam_hukuman, tanggal_hukuman)
    SELECT 
        santri_id, 
        kitab_id, 
        COUNT(*) as total_alpa, 
        COUNT(*) as jam_hukuman, 
        CURDATE() as tanggal_hukuman
    FROM absensi
    WHERE status = 'Alpa'
    GROUP BY santri_id, kitab_id
    HAVING total_alpa > 0;
END //
DELIMITER ;

-- Tambahkan trigger untuk otomatis menghitung hukuman
DELIMITER //
CREATE TRIGGER after_absensi_insert AFTER INSERT ON absensi
FOR EACH ROW
BEGIN
    CALL HitungHukuman();
END //
DELIMITER ;
