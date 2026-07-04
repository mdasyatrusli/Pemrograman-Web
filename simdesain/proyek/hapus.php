<?php
/**
 * =========================================================
 * FILE: proyek/hapus.php
 * FUNGSI: Memproses penghapusan data proyek beserta file
 *         gambar hasil desain yang terkait di server.
 * =========================================================
 */
require_once '../includes/auth.php';
require_once '../config/koneksi.php';
require_once '../includes/fungsi.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    set_flash('danger', 'Data proyek tidak ditemukan.');
    header("Location: index.php");
    exit;
}

$folder_upload = '../assets/uploads/proyek/';

// Ambil dulu nama file gambar sebelum datanya dihapus dari database
$stmt = $koneksi->prepare("SELECT gambar_hasil FROM proyek_desain WHERE id_proyek = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$hasil = $stmt->get_result();
$proyek = $hasil->fetch_assoc();

$hapus = $koneksi->prepare("DELETE FROM proyek_desain WHERE id_proyek = ?");
$hapus->bind_param("i", $id);

// proyek_desain direferensikan oleh pesanan (ON DELETE RESTRICT),
// jadi jika proyek ini masih dipesan, penghapusan akan ditolak database.
try {
    $berhasil = $hapus->execute();

    if ($berhasil) {
        // Hapus file gambar dari server HANYA setelah data berhasil dihapus dari database
        if ($proyek && !empty($proyek['gambar_hasil']) && file_exists($folder_upload . $proyek['gambar_hasil'])) {
            unlink($folder_upload . $proyek['gambar_hasil']);
        }
        set_flash('success', 'Proyek desain berhasil dihapus.');
    } elseif ($koneksi->errno === 1451) {
        set_flash('danger', 'Proyek tidak dapat dihapus karena masih memiliki pesanan terkait.');
    } else {
        set_flash('danger', 'Gagal menghapus proyek desain.');
    }
} catch (mysqli_sql_exception $e) {
    if ($e->getCode() === 1451) {
        set_flash('danger', 'Proyek tidak dapat dihapus karena masih memiliki pesanan terkait.');
    } else {
        set_flash('danger', 'Gagal menghapus proyek desain.');
    }
}

header("Location: index.php");
exit;
