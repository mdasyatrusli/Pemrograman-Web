<?php
/**
 * =========================================================
 * FILE: pelanggan/edit.php
 * FUNGSI: Menampilkan form edit dengan data lama pelanggan,
 *         dan memproses pembaruan data ke database.
 * =========================================================
 */
require_once '../includes/auth.php';
require_once '../config/koneksi.php';
require_once '../includes/fungsi.php';

$halaman_aktif = 'pelanggan';
$error = [];

// --- Ambil ID dari URL, wajib berupa angka ---
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    set_flash('danger', 'Data pelanggan tidak ditemukan.');
    header("Location: index.php");
    exit;
}

// --- Ambil data pelanggan lama berdasarkan ID ---
$stmt = $koneksi->prepare("SELECT * FROM pelanggan WHERE id_pelanggan = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$hasil = $stmt->get_result();

if ($hasil->num_rows === 0) {
    set_flash('danger', 'Data pelanggan tidak ditemukan.');
    header("Location: index.php");
    exit;
}

$pelanggan = $hasil->fetch_assoc();

// Nilai default form diambil dari data lama
$nama_pelanggan = $pelanggan['nama_pelanggan'];
$email          = $pelanggan['email'];
$no_telepon     = $pelanggan['no_telepon'];
$alamat         = $pelanggan['alamat'];

// --- Proses update ketika form disubmit ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_pelanggan = bersihkan_input($_POST['nama_pelanggan']);
    $email          = bersihkan_input($_POST['email']);
    $no_telepon     = bersihkan_input($_POST['no_telepon']);
    $alamat         = bersihkan_input($_POST['alamat']);

    if (empty($nama_pelanggan)) {
        $error[] = "Nama pelanggan wajib diisi.";
    }
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error[] = "Format email tidak valid.";
    }
    if (!empty($no_telepon) && !preg_match('/^[0-9+\-\s]+$/', $no_telepon)) {
        $error[] = "No. telepon hanya boleh berisi angka, spasi, tanda + atau -.";
    }

    if (empty($error)) {
        $stmt = $koneksi->prepare(
            "UPDATE pelanggan SET nama_pelanggan = ?, email = ?, no_telepon = ?, alamat = ? WHERE id_pelanggan = ?"
        );
        $stmt->bind_param("ssssi", $nama_pelanggan, $email, $no_telepon, $alamat, $id);

        if ($stmt->execute()) {
            set_flash('success', 'Data pelanggan berhasil diperbarui.');
            header("Location: index.php");
            exit;
        } else {
            $error[] = "Terjadi kesalahan saat memperbarui data.";
        }
        $stmt->close();
    }
}
?>
<?php require_once '../includes/header.php'; ?>

<div class="wrapper">
    <?php require_once '../includes/sidebar.php'; ?>

    <div class="content-area p-4">
        <h4 class="fw-semibold mb-3">Edit Pelanggan</h4>

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
                        <i class="bi bi-save"></i> Perbarui
                    </button>
                    <a href="index.php" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
