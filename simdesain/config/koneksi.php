<?php
/**
 * =========================================================
 * FILE: config/koneksi.php
 * FUNGSI: Membuka koneksi ke database MySQL menggunakan mysqli.
 *         File ini akan di-include (require) di hampir semua
 *         halaman yang membutuhkan akses database.
 * =========================================================
 */

// PHP 8.1+ membuat mysqli otomatis melempar exception saat query gagal.
// Kita matikan agar bisa menangani error (misal foreign key) dengan cara
// mengecek $koneksi->errno secara manual, dengan pesan yang ramah pengguna.
mysqli_report(MYSQLI_REPORT_OFF);

// --- Konfigurasi koneksi database ---
// Sesuaikan jika pengaturan XAMPP Anda berbeda (umumnya default seperti ini)
$db_host = "localhost";   // alamat server database
$db_user = "root";        // username default XAMPP
$db_pass = "";            // password default XAMPP (kosong)
$db_name = "db_simdesain"; // nama database yang sudah dibuat lewat db_simdesain.sql

// --- Membuat koneksi menggunakan mysqli (Object Oriented style) ---
$koneksi = new mysqli($db_host, $db_user, $db_pass, $db_name);

// --- Cek apakah koneksi berhasil ---
if ($koneksi->connect_error) {
    // Jika gagal konek, hentikan eksekusi & tampilkan pesan yang jelas
    // (di aplikasi production sebaiknya jangan tampilkan detail error ke user,
    //  tapi untuk pembelajaran ini membantu proses debugging)
    die("Koneksi ke database gagal: " . $koneksi->connect_error .
        "<br>Pastikan MySQL sudah berjalan di XAMPP dan database 'db_simdesain' sudah diimport.");
}

// --- Mengatur karakter set ke utf8mb4 agar mendukung semua karakter (termasuk emoji) ---
$koneksi->set_charset("utf8mb4");

/**
 * Catatan penting untuk pemula:
 * - Variabel $koneksi ini akan dipakai di semua file lain dengan cara:
 *   require_once '../config/koneksi.php';
 * - Semua query ke database WAJIB menggunakan prepared statement
 *   ($koneksi->prepare(...)) untuk mencegah SQL Injection.
 *   Contoh akan dibahas lengkap di tahap pembuatan modul CRUD.
 */
