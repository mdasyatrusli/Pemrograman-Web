<?php
/**
 * =========================================================
 * FILE: proyek/tambah.php
 * FUNGSI: Menampilkan form tambah proyek (dengan dropdown
 *         kategori & upload gambar) dan memproses penyimpanannya.
 * =========================================================
 */
require_once '../includes/auth.php';
require_once '../config/koneksi.php';
require_once '../includes/fungsi.php';

$halaman_aktif = 'proyek';
$error = [];

$nama_proyek = $deskripsi = $tanggal_dibuat = '';
$id_kategori = '';

// Ambil daftar kategori untuk dropdown
$daftar_kategori = $koneksi->query("SELECT * FROM kategori_desain ORDER BY nama_kategori ASC");

// Pengaturan upload gambar
$folder_upload   = '../assets/uploads/proyek/';
$ekstensi_diizinkan = ['jpg', 'jpeg', 'png', 'webp'];
$maks_ukuran_byte   = 2 * 1024 * 1024; // 2 MB

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_kategori    = (int) $_POST['id_kategori'];
    $nama_proyek    = bersihkan_input($_POST['nama_proyek']);
    $deskripsi      = bersihkan_input($_POST['deskripsi']);
    $tanggal_dibuat = bersihkan_input($_POST['tanggal_dibuat']);
    $nama_file_final = null; // nama file yang akan disimpan ke database

    // --- Validasi input teks ---
    if ($id_kategori <= 0) {
        $error[] = "Kategori wajib dipilih.";
    }
    if (empty($nama_proyek)) {
        $error[] = "Nama proyek wajib diisi.";
    }
    if (empty($tanggal_dibuat)) {
        $error[] = "Tanggal wajib diisi.";
    }

    // --- Validasi & proses upload gambar (opsional, boleh tidak upload) ---
    if (isset($_FILES['gambar_hasil']) && $_FILES['gambar_hasil']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['gambar_hasil'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $error[] = "Terjadi kesalahan saat mengunggah file.";
        } elseif ($file['size'] > $maks_ukuran_byte) {
            $error[] = "Ukuran gambar maksimal 2MB.";
        } else {
            $ekstensi = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ekstensi, $ekstensi_diizinkan)) {
                $error[] = "Format gambar harus JPG, JPEG, PNG, atau WEBP.";
            } else {
                // Nama file unik supaya tidak bentrok dengan file lain
                $nama_file_final = uniqid('proyek_') . '.' . $ekstensi;
            }
        }
    }

    // --- Simpan ke database jika tidak ada error ---
    if (empty($error)) {
        // Pindahkan file ke folder upload HANYA setelah validasi lolos
        if ($nama_file_final !== null) {
            move_uploaded_file($_FILES['gambar_hasil']['tmp_name'], $folder_upload . $nama_file_final);
        }

        $stmt = $koneksi->prepare(
            "INSERT INTO proyek_desain (id_kategori, nama_proyek, deskripsi, gambar_hasil, tanggal_dibuat)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("issss", $id_kategori, $nama_proyek, $deskripsi, $nama_file_final, $tanggal_dibuat);

        if ($stmt->execute()) {
            set_flash('success', 'Proyek desain berhasil ditambahkan.');
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
        <h4 class="fw-semibold mb-3">Tambah Proyek Desain</h4>

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
                <!-- enctype WAJIB multipart/form-data agar file bisa terkirim -->
                <form method="POST" action="tambah.php" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Kategori <span class="text-danger">*</span></label>
                        <select name="id_kategori" class="form-select" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php while ($kat = $daftar_kategori->fetch_assoc()): ?>
                                <option value="<?= $kat['id_kategori'] ?>" <?= ($id_kategori == $kat['id_kategori']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($kat['nama_kategori']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Proyek <span class="text-danger">*</span></label>
                        <input type="text" name="nama_proyek" class="form-control"
                               value="<?= htmlspecialchars($nama_proyek) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($deskripsi) ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Dibuat <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_dibuat" class="form-control"
                               value="<?= htmlspecialchars($tanggal_dibuat) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gambar Hasil Desain</label>
                        <input type="file" name="gambar_hasil" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                        <div class="form-text">Format JPG/PNG/WEBP, maksimal 2MB. Opsional.</div>
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
