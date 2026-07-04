<?php
/**
 * =========================================================
 * FILE: pelanggan/tambah.php
 * FUNGSI: Menampilkan form tambah pelanggan & memproses
 *         penyimpanan data baru ke database.
 * =========================================================
 */
require_once '../includes/auth.php';
require_once '../config/koneksi.php';
require_once '../includes/fungsi.php';

$halaman_aktif = 'pelanggan';
$error = [];

// Menyimpan input lama supaya form tidak kosong lagi jika terjadi error validasi
$nama_pelanggan = $email = $no_telepon = $alamat = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_pelanggan = bersihkan_input($_POST['nama_pelanggan']);
    $email          = bersihkan_input($_POST['email']);
    $no_telepon     = bersihkan_input($_POST['no_telepon']);
    $alamat         = bersihkan_input($_POST['alamat']);

    // --- Validasi input ---
    if (empty($nama_pelanggan)) {
        $error[] = "Nama pelanggan wajib diisi.";
    }
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error[] = "Format email tidak valid.";
    }
    if (!empty($no_telepon) && !preg_match('/^[0-9+\-\s]+$/', $no_telepon)) {
        $error[] = "No. telepon hanya boleh berisi angka, spasi, tanda + atau -.";
    }

    // --- Simpan jika tidak ada error ---
    if (empty($error)) {
        $stmt = $koneksi->prepare(
            "INSERT INTO pelanggan (nama_pelanggan, email, no_telepon, alamat) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("ssss", $nama_pelanggan, $email, $no_telepon, $alamat);

        if ($stmt->execute()) {
            set_flash('success', 'Data pelanggan berhasil ditambahkan.');
            header("Location: index.php");
            exit;
        } else {
            $error[] = "Terjadi kesalahan saat menyimpan data.";
        }
        $stmt->close();
    }
}
?>
<?php require_once '../includes/header.php'; ?>

<div class="wrapper">
    <?php require_once '../includes/sidebar.php'; ?>

    <div class="content-area p-4">
        <h4 class="fw-semibold mb-3">Tambah Pelanggan</h4>

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
                        <label class="form-label">Nama Pelanggan <span class="text-danger">*</span></label>
                        <input type="text" name="nama_pelanggan" class="form-control"
                               value="<?= htmlspecialchars($nama_pelanggan) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control"
                               value="<?= htmlspecialchars($email) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No. Telepon</label>
                        <input type="text" name="no_telepon" class="form-control"
                               value="<?= htmlspecialchars($no_telepon) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="3"><?= htmlspecialchars($alamat) ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-dark">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                    <a href="index.php" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
