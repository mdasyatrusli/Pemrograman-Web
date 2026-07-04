-- =========================================================
-- FILE: database/dummy_data.sql (v3 - lebih aman)
-- FUNGSI: Data contoh (dummy) untuk keperluan testing aplikasi.
--
-- CATATAN PENTING:
-- File ini menggunakan SUBQUERY berdasarkan NAMA (bukan ID tetap)
-- untuk mengambil id_kategori, id_pelanggan, dan id_proyek. Ini
-- membuat file aman dijalankan kapan pun, walau AUTO_INCREMENT
-- di database Anda sudah tidak dimulai dari angka 1.
-- Juga aman dijalankan BERULANG KALI tanpa duplikat data.
--
-- Cara pakai: buka phpMyAdmin -> pilih database db_simdesain
--             -> tab SQL -> paste isi file ini -> Go
-- =========================================================

USE db_simdesain;

-- --- Data dummy: Pelanggan (skip jika email sudah ada) ---
INSERT INTO pelanggan (nama_pelanggan, email, no_telepon, alamat)
SELECT * FROM (SELECT 'Andi Saputra', 'andi.saputra@gmail.com', '081234567801', 'Jl. Malioboro No. 10, Yogyakarta') AS tmp
WHERE NOT EXISTS (SELECT 1 FROM pelanggan WHERE email = 'andi.saputra@gmail.com') LIMIT 1;

INSERT INTO pelanggan (nama_pelanggan, email, no_telepon, alamat)
SELECT * FROM (SELECT 'Sri Wahyuni', 'sri.wahyuni@gmail.com', '081234567802', 'Jl. Kaliurang KM 5, Sleman') AS tmp
WHERE NOT EXISTS (SELECT 1 FROM pelanggan WHERE email = 'sri.wahyuni@gmail.com') LIMIT 1;

INSERT INTO pelanggan (nama_pelanggan, email, no_telepon, alamat)
SELECT * FROM (SELECT 'Budi Hartono', 'budi.hartono@yahoo.com', '081234567803', 'Jl. Solo No. 22, Yogyakarta') AS tmp
WHERE NOT EXISTS (SELECT 1 FROM pelanggan WHERE email = 'budi.hartono@yahoo.com') LIMIT 1;

INSERT INTO pelanggan (nama_pelanggan, email, no_telepon, alamat)
SELECT * FROM (SELECT 'Dewi Lestari', 'dewi.lestari@gmail.com', '081234567804', 'Jl. Parangtritis No. 8, Bantul') AS tmp
WHERE NOT EXISTS (SELECT 1 FROM pelanggan WHERE email = 'dewi.lestari@gmail.com') LIMIT 1;

INSERT INTO pelanggan (nama_pelanggan, email, no_telepon, alamat)
SELECT * FROM (SELECT 'Rizky Pratama', 'rizky.pratama@gmail.com', '081234567805', 'Jl. Magelang No. 15, Yogyakarta') AS tmp
WHERE NOT EXISTS (SELECT 1 FROM pelanggan WHERE email = 'rizky.pratama@gmail.com') LIMIT 1;

INSERT INTO pelanggan (nama_pelanggan, email, no_telepon, alamat)
SELECT * FROM (SELECT 'Putri Amelia', 'putri.amelia@gmail.com', '081234567806', 'Jl. Godean No. 30, Sleman') AS tmp
WHERE NOT EXISTS (SELECT 1 FROM pelanggan WHERE email = 'putri.amelia@gmail.com') LIMIT 1;

INSERT INTO pelanggan (nama_pelanggan, email, no_telepon, alamat)
SELECT * FROM (SELECT 'Fajar Nugroho', 'fajar.nugroho@gmail.com', '081234567807', 'Jl. Wonosari No. 45, Gunungkidul') AS tmp
WHERE NOT EXISTS (SELECT 1 FROM pelanggan WHERE email = 'fajar.nugroho@gmail.com') LIMIT 1;

INSERT INTO pelanggan (nama_pelanggan, email, no_telepon, alamat)
SELECT * FROM (SELECT 'Indah Permata', 'indah.permata@gmail.com', '081234567808', 'Jl. Bantul No. 12, Yogyakarta') AS tmp
WHERE NOT EXISTS (SELECT 1 FROM pelanggan WHERE email = 'indah.permata@gmail.com') LIMIT 1;

-- --- Data dummy: Kategori Desain tambahan (skip jika nama sudah ada) ---
INSERT INTO kategori_desain (nama_kategori, deskripsi)
SELECT * FROM (SELECT 'Undangan', 'Desain undangan pernikahan, ulang tahun, dan acara lainnya') AS tmp
WHERE NOT EXISTS (SELECT 1 FROM kategori_desain WHERE nama_kategori = 'Undangan') LIMIT 1;

INSERT INTO kategori_desain (nama_kategori, deskripsi)
SELECT * FROM (SELECT 'Sertifikat', 'Desain sertifikat penghargaan atau pelatihan') AS tmp
WHERE NOT EXISTS (SELECT 1 FROM kategori_desain WHERE nama_kategori = 'Sertifikat') LIMIT 1;

INSERT INTO kategori_desain (nama_kategori, deskripsi)
SELECT * FROM (SELECT 'Kemasan Produk', 'Desain kemasan/packaging untuk produk UMKM') AS tmp
WHERE NOT EXISTS (SELECT 1 FROM kategori_desain WHERE nama_kategori = 'Kemasan Produk') LIMIT 1;

-- --- Data dummy: Proyek Desain ---
-- id_kategori diambil via SUBQUERY berdasarkan nama_kategori, BUKAN angka tetap,
-- supaya selalu cocok dengan ID asli di database Anda.
-- CATATAN: jika kategori "Logo", "Banner", "Poster", "Kartu Nama" tidak ada
-- (misal sudah diganti nama/dihapus), baris terkait di bawah akan gagal
-- karena id_kategori bernilai NULL -- itu wajar dan aman diabaikan.
INSERT INTO proyek_desain (id_kategori, nama_proyek, deskripsi, tanggal_dibuat)
SELECT * FROM (SELECT
    (SELECT id_kategori FROM kategori_desain WHERE nama_kategori = 'Logo' LIMIT 1),
    'Logo Kedai Kopi Senja', 'Logo minimalis untuk kedai kopi lokal', '2026-05-01'
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM proyek_desain WHERE nama_proyek = 'Logo Kedai Kopi Senja') LIMIT 1;

INSERT INTO proyek_desain (id_kategori, nama_proyek, deskripsi, tanggal_dibuat)
SELECT * FROM (SELECT
    (SELECT id_kategori FROM kategori_desain WHERE nama_kategori = 'Banner' LIMIT 1),
    'Banner Promo Ramadhan', 'Banner promosi diskon bulan Ramadhan', '2026-05-05'
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM proyek_desain WHERE nama_proyek = 'Banner Promo Ramadhan') LIMIT 1;

INSERT INTO proyek_desain (id_kategori, nama_proyek, deskripsi, tanggal_dibuat)
SELECT * FROM (SELECT
    (SELECT id_kategori FROM kategori_desain WHERE nama_kategori = 'Poster' LIMIT 1),
    'Poster Konser Musik Indie', 'Poster untuk acara konser musik indie lokal', '2026-05-10'
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM proyek_desain WHERE nama_proyek = 'Poster Konser Musik Indie') LIMIT 1;

INSERT INTO proyek_desain (id_kategori, nama_proyek, deskripsi, tanggal_dibuat)
SELECT * FROM (SELECT
    (SELECT id_kategori FROM kategori_desain WHERE nama_kategori = 'Kartu Nama' LIMIT 1),
    'Kartu Nama Fotografer', 'Kartu nama untuk jasa fotografer freelance', '2026-05-12'
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM proyek_desain WHERE nama_proyek = 'Kartu Nama Fotografer') LIMIT 1;

INSERT INTO proyek_desain (id_kategori, nama_proyek, deskripsi, tanggal_dibuat)
SELECT * FROM (SELECT
    (SELECT id_kategori FROM kategori_desain WHERE nama_kategori = 'Logo' LIMIT 1),
    'Logo Toko Baju Anak', 'Logo ceria untuk toko baju anak online', '2026-05-15'
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM proyek_desain WHERE nama_proyek = 'Logo Toko Baju Anak') LIMIT 1;

-- --- Data dummy: Pesanan ---
-- id_pelanggan & id_proyek diambil via SUBQUERY berdasarkan email/nama_proyek,
-- BUKAN angka tetap, supaya selalu cocok dengan ID asli di database Anda.
INSERT INTO pesanan (id_pelanggan, id_proyek, tanggal_pesan, status_pesanan, catatan)
SELECT * FROM (SELECT
    (SELECT id_pelanggan FROM pelanggan WHERE email = 'andi.saputra@gmail.com' LIMIT 1),
    (SELECT id_proyek FROM proyek_desain WHERE nama_proyek = 'Logo Kedai Kopi Senja' LIMIT 1),
    '2026-05-02', 'selesai', 'Revisi warna logo sudah disetujui pelanggan'
) AS tmp
WHERE NOT EXISTS (
    SELECT 1 FROM pesanan
    WHERE id_pelanggan = (SELECT id_pelanggan FROM pelanggan WHERE email = 'andi.saputra@gmail.com' LIMIT 1)
      AND id_proyek = (SELECT id_proyek FROM proyek_desain WHERE nama_proyek = 'Logo Kedai Kopi Senja' LIMIT 1)
) LIMIT 1;

INSERT INTO pesanan (id_pelanggan, id_proyek, tanggal_pesan, status_pesanan, catatan)
SELECT * FROM (SELECT
    (SELECT id_pelanggan FROM pelanggan WHERE email = 'sri.wahyuni@gmail.com' LIMIT 1),
    (SELECT id_proyek FROM proyek_desain WHERE nama_proyek = 'Banner Promo Ramadhan' LIMIT 1),
    '2026-05-06', 'proses', 'Sedang dalam tahap desain awal'
) AS tmp
WHERE NOT EXISTS (
    SELECT 1 FROM pesanan
    WHERE id_pelanggan = (SELECT id_pelanggan FROM pelanggan WHERE email = 'sri.wahyuni@gmail.com' LIMIT 1)
      AND id_proyek = (SELECT id_proyek FROM proyek_desain WHERE nama_proyek = 'Banner Promo Ramadhan' LIMIT 1)
) LIMIT 1;

INSERT INTO pesanan (id_pelanggan, id_proyek, tanggal_pesan, status_pesanan, catatan)
SELECT * FROM (SELECT
    (SELECT id_pelanggan FROM pelanggan WHERE email = 'budi.hartono@yahoo.com' LIMIT 1),
    (SELECT id_proyek FROM proyek_desain WHERE nama_proyek = 'Poster Konser Musik Indie' LIMIT 1),
    '2026-05-11', 'baru', 'Menunggu detail lineup band dari pelanggan'
) AS tmp
WHERE NOT EXISTS (
    SELECT 1 FROM pesanan
    WHERE id_pelanggan = (SELECT id_pelanggan FROM pelanggan WHERE email = 'budi.hartono@yahoo.com' LIMIT 1)
      AND id_proyek = (SELECT id_proyek FROM proyek_desain WHERE nama_proyek = 'Poster Konser Musik Indie' LIMIT 1)
) LIMIT 1;

INSERT INTO pesanan (id_pelanggan, id_proyek, tanggal_pesan, status_pesanan, catatan)
SELECT * FROM (SELECT
    (SELECT id_pelanggan FROM pelanggan WHERE email = 'dewi.lestari@gmail.com' LIMIT 1),
    (SELECT id_proyek FROM proyek_desain WHERE nama_proyek = 'Kartu Nama Fotografer' LIMIT 1),
    '2026-05-13', 'selesai', NULL
) AS tmp
WHERE NOT EXISTS (
    SELECT 1 FROM pesanan
    WHERE id_pelanggan = (SELECT id_pelanggan FROM pelanggan WHERE email = 'dewi.lestari@gmail.com' LIMIT 1)
      AND id_proyek = (SELECT id_proyek FROM proyek_desain WHERE nama_proyek = 'Kartu Nama Fotografer' LIMIT 1)
) LIMIT 1;

INSERT INTO pesanan (id_pelanggan, id_proyek, tanggal_pesan, status_pesanan, catatan)
SELECT * FROM (SELECT
    (SELECT id_pelanggan FROM pelanggan WHERE email = 'rizky.pratama@gmail.com' LIMIT 1),
    (SELECT id_proyek FROM proyek_desain WHERE nama_proyek = 'Logo Toko Baju Anak' LIMIT 1),
    '2026-05-16', 'batal', 'Pelanggan membatalkan pesanan'
) AS tmp
WHERE NOT EXISTS (
    SELECT 1 FROM pesanan
    WHERE id_pelanggan = (SELECT id_pelanggan FROM pelanggan WHERE email = 'rizky.pratama@gmail.com' LIMIT 1)
      AND id_proyek = (SELECT id_proyek FROM proyek_desain WHERE nama_proyek = 'Logo Toko Baju Anak' LIMIT 1)
) LIMIT 1;
