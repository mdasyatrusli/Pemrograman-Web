<?php
/**
 * =========================================================
 * FILE: pesanan/edit.php
 * FUNGSI: Menampilkan form edit pesanan dengan data lama,
 *         dan memproses pembaruannya (termasuk ubah status).
 * =========================================================
 */
require_once '../includes/auth.php';
require_once '../config/koneksi.php';
require_once '../includes/fungsi.php';

$halaman_aktif = 'pesanan';
$error = [];

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    set_flash('danger', 'Data pesanan tidak ditemukan.');
    header("Location: index.php");
    exit;
}

$stmt = $koneksi->prepare("SELECT * FROM pesanan WHERE id_pesanan = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$hasil = $stmt->get_result();

if ($hasil->num_rows === 0) {
    set_flash('danger', 'Data pesanan tidak ditemukan.');
    header("Location: index.php");
    exit;
}

$pesanan = $hasil->fetch_assoc();
$id_pelanggan   = $pesanan['id_pelanggan'];
$id_proyek      = $pesanan['id_proyek'];
$tanggal_pesan  = $pesanan['tanggal_pesan'];
$status_pesanan = $pesanan['status_pesanan'];
$catatan        = $pesanan['catatan'];

$daftar_pelanggan = $koneksi->query("SELECT * FROM pelanggan ORDER BY nama_pelanggan ASC");
$daftar_proyek    = $koneksi->query("SELECT * FROM proyek_desain ORDER BY nama_proyek ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pelanggan   = (int) $_POST['id_pelanggan'];
    $id_proyek      = (int) $_POST['id_proyek'];
    $tanggal_pesan  = bersihkan_input($_POST['tanggal_pesan']);
    $status_pesanan = bersihkan_input($_POST['status_pesanan']);
    $catatan        = bersihkan_input($_POST['catatan']);

    if ($id_pelanggan <= 0) {
        $error[] = "Pelanggan wajib dipilih.";
    }
    if ($id_proyek <= 0) {
        $error[] = "Proyek wajib dipilih.";
    }
    if (empty($tanggal_pesan)) {
        $error[] = "Tanggal pesan wajib diisi.";
    }
    if (!in_array($status_pesanan, ['baru', 'proses', 'selesai', 'batal'])) {
        $error[] = "Status pesanan tidak valid.";
    }

    if (empty($error)) {
        $update = $koneksi->prepare(
            "UPDATE pesanan
             SET id_pelanggan = ?, id_proyek = ?, tanggal_pesan = ?, status_pesanan = ?, catatan = ?
             WHERE id_pesanan = ?"
        );
        $update->bind_param("iisssi", $id_pelanggan, $id_proyek, $tanggal_pesan, $status_pesanan, $catatan, $id);

        if ($update->execute()) {
            set_flash('success', 'Pesanan berhasil diperbarui.');
            header("Location: index.php");
            exit;
        } else {
            $error[] = "Terjadi kesalahan saat memperbarui data.";
        }
    }
}
?>
<?php require_once '../includes/header.php'; ?>

<div class="wrapper">
    <?php require_once '../includes/sidebar.php'; ?>

    <div class="content-area p-4">
        <h4 class="fw-semibold mb-3">Edit Pesanan</h4>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0 ps-3">
                    <?php foreach ($error as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="edit.php?id=<?= $id ?>">
                    <div class="mb-3">
                        <label class="form-label">Pelanggan <span class="text-danger">*</span></label>
                        <select name="id_pelanggan" class="form-select" required>
                            <?php while ($pl = $daftar_pelanggan->fetch_assoc()): ?>
                                <option value="<?= $pl['id_pelanggan'] ?>" <?= ($id_pelanggan == $pl['id_pelanggan']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($pl['nama_pelanggan']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Proyek Desain <span class="text-danger">*</span></label>
                        <select name="id_proyek" class="form-select" required>
                            <?php while ($pr = $daftar_proyek->fetch_assoc()): ?>
                                <option value="<?= $pr['id_proyek'] ?>" <?= ($id_proyek == $pr['id_proyek']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($pr['nama_proyek']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Pesan <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_pesan" class="form-control"
                               value="<?= htmlspecialchars($tanggal_pesan) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status Pesanan <span class="text-danger">*</span></label>
                        <select name="status_pesanan" class="form-select" required>
                            <?php foreach (['baru', 'proses', 'selesai', 'batal'] as $opsi): ?>
                                <option value="<?= $opsi ?>" <?= ($status_pesanan === $opsi) ? 'selected' : '' ?>>
                                    <?= ucfirst($opsi) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="catatan" class="form-control" rows="3"><?= htmlspecialchars($catatan) ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-dark">
                        <i class="bi bi-save"></i> Perbarui
                    </button>
                    <a href="index.php" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
