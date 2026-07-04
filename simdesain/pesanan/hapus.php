<?php
/**
 * =========================================================
 * FILE: pesanan/hapus.php
 * FUNGSI: Memproses penghapusan data pesanan.
 *         Tabel pesanan tidak direferensikan tabel manapun,
 *         jadi tidak perlu penanganan foreign key khusus.
 * =========================================================
 */
require_once '../includes/auth.php';
require_once '../config/koneksi.php';
require_once '../includes/fungsi.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    set_flash('danger', 'Data pesanan tidak ditemukan.');
    header("Location: index.php");
    exit;
}

$stmt = $koneksi->prepare("DELETE FROM pesanan WHERE id_pesanan = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    set_flash('success', 'Pesanan berhasil dihapus.');
} else {
    set_flash('danger', 'Gagal menghapus pesanan.');
}

header("Location: index.php");
exit;
