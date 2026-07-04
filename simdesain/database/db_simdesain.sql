-- =========================================================
-- DATABASE: db_simdesain
-- Sistem Informasi Manajemen Layanan Desain Grafis
-- Cara pakai: buka phpMyAdmin -> tab Import -> pilih file ini -> Go
-- Atau: buka tab SQL di phpMyAdmin -> paste seluruh isi file -> Go
-- =========================================================

-- Membuat database jika belum ada, lalu memakainya
CREATE DATABASE IF NOT EXISTS db_simdesain
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci;

USE db_simdesain;

-- =========================================================
-- TABEL: users
-- Menyimpan akun pengguna aplikasi (admin & staff)
-- =========================================================
CREATE TABLE users (
    id_user        INT AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap   VARCHAR(100)        NOT NULL,
    email          VARCHAR(100)        NOT NULL UNIQUE,
    password       VARCHAR(255)        NOT NULL, -- disimpan dalam bentuk hash (password_hash)
    role           ENUM('admin','staff') NOT NULL DEFAULT 'staff',
    foto_profil    VARCHAR(255)        DEFAULT NULL,
    created_at     TIMESTAMP           DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =========================================================
-- TABEL: pelanggan
-- Menyimpan data pelanggan yang memesan jasa desain
-- =========================================================
CREATE TABLE pelanggan (
    id_pelanggan     INT AUTO_INCREMENT PRIMARY KEY,
    nama_pelanggan   VARCHAR(100)  NOT NULL,
    email            VARCHAR(100)  DEFAULT NULL,
    no_telepon       VARCHAR(20)   DEFAULT NULL,
    alamat           TEXT          DEFAULT NULL,
    created_at       TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =========================================================
-- TABEL: kategori_desain
-- Menyimpan jenis-jenis layanan desain (Logo, Banner, Poster, dst)
-- =========================================================
CREATE TABLE kategori_desain (
    id_kategori    INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori  VARCHAR(100)  NOT NULL,
    deskripsi      TEXT          DEFAULT NULL,
    created_at     TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =========================================================
-- TABEL: proyek_desain
-- Menyimpan data proyek/hasil karya desain, terhubung ke kategori
-- =========================================================
CREATE TABLE proyek_desain (
    id_proyek       INT AUTO_INCREMENT PRIMARY KEY,
    id_kategori     INT           NOT NULL,
    nama_proyek     VARCHAR(150)  NOT NULL,
    deskripsi       TEXT          DEFAULT NULL,
    gambar_hasil    VARCHAR(255)  DEFAULT NULL, -- nama file gambar di assets/uploads/proyek/
    tanggal_dibuat  DATE          NOT NULL,
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_proyek_kategori
        FOREIGN KEY (id_kategori) REFERENCES kategori_desain(id_kategori)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- =========================================================
-- TABEL: pesanan
-- Menghubungkan pelanggan dengan proyek yang dipesan
-- =========================================================
CREATE TABLE pesanan (
    id_pesanan      INT AUTO_INCREMENT PRIMARY KEY,
    id_pelanggan    INT           NOT NULL,
    id_proyek       INT           NOT NULL,
    created_by      INT           DEFAULT NULL, -- id_user yang menginput pesanan
    tanggal_pesan   DATE          NOT NULL,
    status_pesanan  ENUM('baru','proses','selesai','batal') NOT NULL DEFAULT 'baru',
    catatan         TEXT          DEFAULT NULL,
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_pesanan_pelanggan
        FOREIGN KEY (id_pelanggan) REFERENCES pelanggan(id_pelanggan)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_pesanan_proyek
        FOREIGN KEY (id_proyek) REFERENCES proyek_desain(id_proyek)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_pesanan_user
        FOREIGN KEY (created_by) REFERENCES users(id_user)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- =========================================================
-- DATA AWAL (opsional, memudahkan testing)
-- Password untuk akun di bawah adalah: admin123
-- Hash ini dihasilkan dengan password_hash('admin123', PASSWORD_DEFAULT)
-- =========================================================
INSERT INTO users (nama_lengkap, email, password, role) VALUES
('Administrator', 'admin@simdesain.test', '$2b$12$NeM./EFm80BR2GsLJqEMT.PXtJqHuS4cGJsxyZsnUclS8cN.e55DW', 'admin');

INSERT INTO kategori_desain (nama_kategori, deskripsi) VALUES
('Logo', 'Desain identitas visual merek/bisnis'),
('Banner', 'Desain banner promosi cetak maupun digital'),
('Poster', 'Desain poster untuk acara atau kampanye'),
('Kartu Nama', 'Desain kartu nama profesional');
