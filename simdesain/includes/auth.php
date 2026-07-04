<?php
/**
 * =========================================================
 * FILE: includes/auth.php
 * FUNGSI: Proteksi halaman. Wajib di-require PALING ATAS
 *         (sebelum HTML apapun dicetak) di setiap halaman
 *         yang hanya boleh diakses oleh pengguna yang sudah login.
 *
 * CARA PAKAI di halaman lain, contoh dashboard.php:
 *   require_once 'includes/auth.php';
 * =========================================================
 */

// session_start() wajib dipanggil sebelum mengakses $_SESSION.
// Kita cek dulu supaya tidak error jika sudah dipanggil sebelumnya.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Pastikan fungsi.php ikut dimuat karena hanya_admin() di bawah memakai set_flash()
require_once __DIR__ . '/fungsi.php';

// Jika session id_user tidak ada, artinya pengguna belum login.
// Maka paksa redirect ke halaman login dan hentikan eksekusi script.
if (!isset($_SESSION['id_user'])) {
    header("Location: /simdesain/auth/login.php");
    exit; // WAJIB exit setelah header redirect agar kode di bawahnya tidak ikut jalan
}

/**
 * Fungsi tambahan untuk membatasi akses khusus admin.
 * Dipanggil di halaman yang hanya boleh diakses admin,
 * misal proses hapus kategori_desain.
 */
function hanya_admin() {
    if ($_SESSION['role'] !== 'admin') {
        set_flash('danger', 'Anda tidak memiliki akses untuk melakukan aksi ini.');
        header("Location: /simdesain/dashboard.php");
        exit;
    }
}
