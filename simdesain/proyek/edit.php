<?php
/**
 * =========================================================
 * FILE: proyek/edit.php
 * FUNGSI: Menampilkan form edit proyek dengan data & gambar lama,
 *         memproses pembaruan, dan mengganti gambar jika di-upload baru.
 * =========================================================
 */
require_once '../includes/auth.php';
require_once '../config/koneksi.php';
require_once '../includes/fungsi.php';

$halaman_aktif = 'proyek';
$error = [];

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    set_flash('danger', 'Data proyek tidak ditemukan.');
    header("Location: index.php");
    exit;
}

$stmt = $koneksi->prepare("SELECT * FROM proyek_desain WHERE id_proyek = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$hasil = $stmt->get_result();

if ($hasil->num_rows === 0) {
    set_flash('danger', 'Data proyek tidak ditemukan.');
    header("Location: index.php");
    exit;
}

$proyek = $hasil->fetch_assoc();
$id_kategori    = $proyek['id_kategori'];
$nama_proyek    = $proyek['nama_proyek'];
$deskripsi      = $proyek['deskripsi'];
$tanggal_dibuat = $proyek['tanggal_dibuat'];
$gambar_lama    = $proyek['gambar_hasil'];

$daftar_kategori = $koneksi->query("SELECT * FROM kategori_desain ORDER BY nama_kategori ASC");

$folder_upload      = '../assets/uploads/proyek/';
$ekstensi_diizinkan = ['jpg', 'jpeg', 'png', 'webp'];
$maks_ukuran_byte   = 2 * 1024 * 1024;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_kategori    = (int) $_POST['id_kategori'];
    $nama_proyek    = bersihkan_input($_POST['nama_proyek']);
    $deskripsi      = bersihkan_input($_POST['deskripsi']);
    $tanggal_dibuat = bersihkan_input($_POST['tanggal_dibuat']);
    $nama_file_final = $gambar_lama; // default: tetap pakai gambar lama

    if ($id_kategori <= 0) {
        $error[] = "Kategori wajib dipilih.";
    }
    if (empty($nama_proyek)) {
        $error[] = "Nama proyek wajib diisi.";
    }
    if (empty($tanggal_dibuat)) {
        $error[] = "Tanggal wajib diisi.";
    }

    // --- Cek apakah user upload gambar baru ---
    $ada_gambar_baru = isset($_FILES['gambar_hasil']) && $_FILES['gambar_hasil']['error'] !== UPLOAD_ERR_NO_FILE;

    if ($ada_gambar_baru) {
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
                $nama_file_final = uniqid('proyek_') . '.' . $ekstensi;
            }
        }
    }

    if (empty($error)) {
        // Pindahkan file baru HANYA setelah validasi lolos
        if ($ada_gambar_baru) {
            move_uploaded_file($_FILES['gambar_hasil']['tmp_name'], $folder_upload . $nama_file_final);

            // Hapus gambar lama dari server supaya tidak jadi file sampah,
            // tapi hanya jika memang ada gambar lama sebelumnya
            if (!empty($gambar_lama) && file_exists($folder_upload . $gambar_lama)) {
                unlink($folder_upload . $gambar_lama);
            }
        }

        $update = $koneksi->prepare(
            "UPDATE proyek_desain
             SET id_kategori = ?, nama_proyek = ?, deskripsi = ?, gambar_hasil = ?, tanggal_dibuat = ?
             WHERE id_proyek = ?"
        );
        $update->bind_param("issssi", $id_kategori, $nama_proyek, $deskripsi, $nama_file_final, $tanggal_dibuat, $id);

        if ($update->execute()) {
            set_flash('success', 'Proyek desain berhasil diperbarui.');
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
        <h4 class="fw-semibold mb-3">Edit Proyek Desain</h4>

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
                <form method="POST" action="edit.php?id=<?= $id ?>" enctype="multipart/form-data">
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

                        <?php if (!empty($gambar_lama) && file_exists($folder_upload . $gambar_lama)): ?>
                            <div class="mb-2">
                                <img src="/simdesain/assets/uploads/proyek/<?= htmlspecialchars($gambar_lama) ?>"
                                     style="max-height: 120px; border-radius: 8px;" alt="Gambar saat ini">
                                <div class="form-text">Gambar saat ini. Unggah file baru di bawah untuk menggantinya.</div>
                            </div>
                        <?php endif; ?>

                        <input type="file" name="gambar_hasil" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                        <div class="form-text">Format JPG/PNG/WEBP, maksimal 2MB. Kosongkan jika tidak ingin mengganti gambar.</div>
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
