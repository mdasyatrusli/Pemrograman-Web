<?php
/**
 * =========================================================
 * FILE: pelanggan/hapus.php
 * FUNGSI: Memproses penghapusan data pelanggan.
 *         Dipanggil setelah pengguna menekan "Ya, Hapus" pada modal
 *         konfirmasi di index.php.
 * =========================================================
 */
require_once '../includes/auth.php';
require_once '../config/koneksi.php';
require_once '../includes/fungsi.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    set_flash('danger', 'Data pelanggan tidak ditemukan.');
    header("Location: index.php");
    exit;
}

$stmt = $koneksi->prepare("DELETE FROM pelanggan WHERE id_pelanggan = ?");
$stmt->bind_param("i", $id);

// Tabel pelanggan direferensikan oleh tabel pesanan (ON DELETE RESTRICT),
// jadi jika pelanggan masih punya pesanan, MySQL akan menolak proses hapus.
// Dibungkus try-catch karena beberapa versi PHP (8.1+) membuat mysqli
// melempar exception saat query gagal, alih-alih sekadar return false.
try {
    $berhasil = $stmt->execute();

    if ($berhasil) {
        set_flash('success', 'Data pelanggan berhasil dihapus.');
    } elseif ($koneksi->errno === 1451) {
        // Error code 1451 = tidak bisa hapus karena masih dipakai di tabel lain (foreign key)
        set_flash('danger', 'Data tidak dapat dihapus karena pelanggan ini masih memiliki pesanan terkait.');
    } else {
        set_flash('danger', 'Gagal menghapus data pelanggan.');
    }
} catch (mysqli_sql_exception $e) {
    // Error code 1451 = tidak bisa hapus karena masih dipakai di tabel lain (foreign key)
    if ($e->getCode() === 1451) {
        set_flash('danger', 'Data tidak dapat dihapus karena pelanggan ini masih memiliki pesanan terkait.');
    } else {
        set_flash('danger', 'Gagal menghapus data pelanggan.');
    }
}

// CATATAN: sengaja TIDAK memanggil $stmt->close() secara manual di sini.
// Jika terjadi exception di atas, mysqli terkadang sudah menutup statement
// secara internal, sehingga close() manual justru memicu error "already closed".
// PHP akan otomatis membersihkan statement ini saat script selesai.
header("Location: index.php");
exit;
