<?php
/**
 * =========================================================
 * FILE: kategori/tambah.php
 * FUNGSI: Menampilkan form tambah kategori & memproses
 *         penyimpanan data baru ke database.
 * =========================================================
 */
require_once '../includes/auth.php';
require_once '../config/koneksi.php';
require_once '../includes/fungsi.php';

$halaman_aktif = 'kategori';
$error = [];
$nama_kategori = $deskripsi = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_kategori = bersihkan_input($_POST['nama_kategori']);
    $deskripsi     = bersihkan_input($_POST['deskripsi']);

    if (empty($nama_kategori)) {
        $error[] = "Nama kategori wajib diisi.";
    }

    // Cek duplikasi nama kategori (opsional tapi baik untuk data yang rapi)
    if (empty($error)) {
        $cek = $koneksi->prepare("SELECT id_kategori FROM kategori_desain WHERE nama_kategori = ?");
        $cek->bind_param("s", $nama_kategori);
        $cek->execute();
        $cek->store_result();
        if ($cek->num_rows > 0) {
            $error[] = "Kategori dengan nama tersebut sudah ada.";
        }
        $cek->close();
    }

    if (empty($error)) {
        $stmt = $koneksi->prepare("INSERT INTO kategori_desain (nama_kategori, deskripsi) VALUES (?, ?)");
        $stmt->bind_param("ss", $nama_kategori, $deskripsi);

        if ($stmt->execute()) {
            set_flash('success', 'Kategori desain berhasil ditambahkan.');
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
        <h4 class="fw-semibold mb-3">Tambah Kategori Desain</h4>

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
                        <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="nama_kategori" class="form-control"
                               value="<?= htmlspecialchars($nama_kategori) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($deskripsi) ?></textarea>
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
