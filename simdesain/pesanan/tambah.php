<?php
/**
 * =========================================================
 * FILE: pesanan/tambah.php
 * FUNGSI: Menampilkan form tambah pesanan (dropdown pelanggan
 *         & proyek) dan memproses penyimpanannya.
 * =========================================================
 */
require_once '../includes/auth.php';
require_once '../config/koneksi.php';
require_once '../includes/fungsi.php';

$halaman_aktif = 'pesanan';
$error = [];

$id_pelanggan = $id_proyek = '';
$tanggal_pesan = date('Y-m-d'); // default: hari ini
$status_pesanan = 'baru';
$catatan = '';

// Ambil daftar pelanggan & proyek untuk dropdown
$daftar_pelanggan = $koneksi->query("SELECT * FROM pelanggan ORDER BY nama_pelanggan ASC");
$daftar_proyek    = $koneksi->query("SELECT * FROM proyek_desain ORDER BY nama_proyek ASC");

// Cek dulu apakah data pelanggan & proyek sudah ada (pesanan butuh keduanya)
$ada_pelanggan = $daftar_pelanggan->num_rows > 0;
$ada_proyek    = $koneksi->query("SELECT COUNT(*) AS jumlah FROM proyek_desain")->fetch_assoc()['jumlah'] > 0;
$daftar_proyek->data_seek(0); // reset pointer setelah query count di atas

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
        // created_by diambil otomatis dari session yang sedang login
        $created_by = $_SESSION['id_user'];

        $stmt = $koneksi->prepare(
            "INSERT INTO pesanan (id_pelanggan, id_proyek, created_by, tanggal_pesan, status_pesanan, catatan)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("iiisss", $id_pelanggan, $id_proyek, $created_by, $tanggal_pesan, $status_pesanan, $catatan);

        if ($stmt->execute()) {
            set_flash('success', 'Pesanan berhasil ditambahkan.');
            header("Location: index.php");
            exit;
        } else {
            $error[] = "Terjadi kesalahan saat menyimpan data.";
        }
    }
}
?>
<?php require_once '../includes/header.php'; ?>

<div class="wrapper">
    <?php require_once '../includes/sidebar.php'; ?>

    <div class="content-area p-4">
        <h4 class="fw-semibold mb-3">Tambah Pesanan</h4>

        <?php if (!$ada_pelanggan || !$ada_proyek): ?>
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle"></i>
                Data pesanan membutuhkan minimal 1 <strong>Pelanggan</strong> dan 1 <strong>Proyek Desain</strong>.
                Silakan tambahkan data tersebut terlebih dahulu di menu masing-masing.
            </div>
        <?php endif; ?>

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
                <form method="POST" action="tambah.php">
                    <div class="mb-3">
                        <label class="form-label">Pelanggan <span class="text-danger">*</span></label>
                        <select name="id_pelanggan" class="form-select" required>
                            <option value="">-- Pilih Pelanggan --</option>
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
                            <option value="">-- Pilih Proyek --</option>
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

                    <button type="submit" class="btn btn-dark" <?= (!$ada_pelanggan || !$ada_proyek) ? 'disabled' : '' ?>>
                        <i class="bi bi-save"></i> Simpan
                    </button>
                    <a href="index.php" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
