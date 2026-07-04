<?php
/**
 * =========================================================
 * FILE: kategori/hapus.php
 * FUNGSI: Memproses penghapusan kategori desain.
 *         HANYA bisa diakses oleh role 'admin'.
 * =========================================================
 */
require_once '../includes/auth.php';
require_once '../config/koneksi.php';
require_once '../includes/fungsi.php';

// Proteksi tambahan: tolak akses jika bukan admin, meskipun URL diakses langsung
hanya_admin();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    set_flash('danger', 'Data kategori tidak ditemukan.');
    header("Location: index.php");
    exit;
}

$stmt = $koneksi->prepare("DELETE FROM kategori_desain WHERE id_kategori = ?");
$stmt->bind_param("i", $id);

// Kategori direferensikan oleh tabel proyek_desain (ON DELETE RESTRICT),
// jadi jika kategori masih dipakai oleh proyek, MySQL akan menolak hapus.
// Dibungkus try-catch untuk kompatibilitas berbagai versi PHP (lihat catatan
// yang sama di pelanggan/hapus.php).
try {
    $berhasil = $stmt->execute();

    if ($berhasil) {
        set_flash('success', 'Kategori desain berhasil dihapus.');
    } elseif ($koneksi->errno === 1451) {
        set_flash('danger', 'Kategori tidak dapat dihapus karena masih digunakan oleh proyek desain.');
    } else {
        set_flash('danger', 'Gagal menghapus kategori desain.');
    }
} catch (mysqli_sql_exception $e) {
    if ($e->getCode() === 1451) {
        set_flash('danger', 'Kategori tidak dapat dihapus karena masih digunakan oleh proyek desain.');
    } else {
        set_flash('danger', 'Gagal menghapus kategori desain.');
    }
}

header("Location: index.php");
exit;
