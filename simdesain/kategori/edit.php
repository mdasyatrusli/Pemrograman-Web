<?php
/**
 * =========================================================
 * FILE: kategori/edit.php
 * FUNGSI: Menampilkan form edit dengan data lama kategori,
 *         dan memproses pembaruan data ke database.
 * =========================================================
 */
require_once '../includes/auth.php';
require_once '../config/koneksi.php';
require_once '../includes/fungsi.php';

$halaman_aktif = 'kategori';
$error = [];

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    set_flash('danger', 'Data kategori tidak ditemukan.');
    header("Location: index.php");
    exit;
}

$stmt = $koneksi->prepare("SELECT * FROM kategori_desain WHERE id_kategori = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$hasil = $stmt->get_result();

if ($hasil->num_rows === 0) {
    set_flash('danger', 'Data kategori tidak ditemukan.');
    header("Location: index.php");
    exit;
}

$kategori = $hasil->fetch_assoc();
$nama_kategori = $kategori['nama_kategori'];
$deskripsi     = $kategori['deskripsi'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_kategori = bersihkan_input($_POST['nama_kategori']);
    $deskripsi     = bersihkan_input($_POST['deskripsi']);

    if (empty($nama_kategori)) {
        $error[] = "Nama kategori wajib diisi.";
    }

    // Cek duplikasi nama kategori, kecuali dengan dirinya sendiri
    if (empty($error)) {
        $cek = $koneksi->prepare("SELECT id_kategori FROM kategori_desain WHERE nama_kategori = ? AND id_kategori != ?");
        $cek->bind_param("si", $nama_kategori, $id);
        $cek->execute();
        $cek->store_result();
        if ($cek->num_rows > 0) {
            $error[] = "Kategori dengan nama tersebut sudah ada.";
        }
        $cek->close();
    }

    if (empty($error)) {
        $update = $koneksi->prepare("UPDATE kategori_desain SET nama_kategori = ?, deskripsi = ? WHERE id_kategori = ?");
        $update->bind_param("ssi", $nama_kategori, $deskripsi, $id);

        if ($update->execute()) {
            set_flash('success', 'Kategori desain berhasil diperbarui.');
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
        <h4 class="fw-semibold mb-3">Edit Kategori Desain</h4>

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
                        <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="nama_kategori" class="form-control"
                               value="<?= htmlspecialchars($nama_kategori) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($deskripsi) ?></textarea>
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
