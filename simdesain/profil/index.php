<?php
/**
 * =========================================================
 * FILE: profil/index.php
 * FUNGSI: Menampilkan halaman profil pengguna yang sedang login
 *         dan memproses perubahan data (nama, email, password, foto).
 * =========================================================
 */
require_once '../includes/auth.php';
require_once '../config/koneksi.php';
require_once '../includes/fungsi.php';

$halaman_aktif = 'profil';
$error = [];
$success = '';

$id_user = (int) $_SESSION['id_user'];

// Ambil data user dari database
$stmt = $koneksi->prepare("SELECT * FROM users WHERE id_user = ?");
$stmt->bind_param("i", $id_user);
$stmt->execute();
$hasil = $stmt->get_result();
$user = $hasil->fetch_assoc();

$nama_lengkap   = $user['nama_lengkap'];
$email          = $user['email'];
$foto_profil    = $user['foto_profil'];

// Pengaturan upload foto
$folder_upload      = '../assets/uploads/profil/';
$ekstensi_diizinkan = ['jpg', 'jpeg', 'png', 'webp'];
$maks_ukuran_byte   = 2 * 1024 * 1024; // 2 MB

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap_baru = bersihkan_input($_POST['nama_lengkap']);
    $email_baru        = bersihkan_input($_POST['email']);
    $password_lama     = $_POST['password_lama'];
    $password_baru     = $_POST['password_baru'];
    $konfirmasi_pass   = $_POST['konfirmasi_password'];

    $nama_file_final = $foto_profil; // default: tetap pakai foto lama

    // --- Validasi input ---
    if (empty($nama_lengkap_baru)) {
        $error[] = "Nama lengkap wajib diisi.";
    }
    if (empty($email_baru) || !filter_var($email_baru, FILTER_VALIDATE_EMAIL)) {
        $error[] = "Format email tidak valid.";
    }

    // Cek apakah email sudah dipakai user lain
    if (empty($error)) {
        $cek = $koneksi->prepare("SELECT id_user FROM users WHERE email = ? AND id_user != ?");
        $cek->bind_param("si", $email_baru, $id_user);
        $cek->execute();
        $cek->store_result();
        if ($cek->num_rows > 0) {
            $error[] = "Email sudah digunakan oleh pengguna lain.";
        }
        $cek->close();
    }

    // Validasi password jika diisi
    $ubah_password = false;
    if (!empty($password_baru)) {
        if (empty($password_lama)) {
            $error[] = "Password lama wajib diisi jika ingin mengganti password.";
        } elseif (!password_verify($password_lama, $user['password'])) {
            $error[] = "Password lama tidak cocok.";
        } elseif (strlen($password_baru) < 6) {
            $error[] = "Password baru minimal 6 karakter.";
        } elseif ($password_baru !== $konfirmasi_pass) {
            $error[] = "Konfirmasi password baru tidak cocok.";
        } else {
            $ubah_password = true;
        }
    }

    // --- Validasi & proses upload foto (opsional) ---
    $ada_foto_baru = isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] !== UPLOAD_ERR_NO_FILE;

    if ($ada_foto_baru) {
        $file = $_FILES['foto_profil'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $error[] = "Terjadi kesalahan saat mengunggah file.";
        } elseif ($file['size'] > $maks_ukuran_byte) {
            $error[] = "Ukuran foto maksimal 2MB.";
        } else {
            $ekstensi = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ekstensi, $ekstensi_diizinkan)) {
                $error[] = "Format foto harus JPG, JPEG, PNG, atau WEBP.";
            } else {
                $nama_file_final = uniqid('foto_') . '.' . $ekstensi;
            }
        }
    }

    // --- Simpan perubahan jika tidak ada error ---
    if (empty($error)) {
        // Pindahkan file baru HANYA setelah validasi lolos
        if ($ada_foto_baru) {
            move_uploaded_file($_FILES['foto_profil']['tmp_name'], $folder_upload . $nama_file_final);

            // Hapus foto lama dari server supaya tidak jadi file sampah
            if (!empty($foto_profil) && $foto_profil !== $nama_file_final && file_exists($folder_upload . $foto_profil)) {
                unlink($folder_upload . $foto_profil);
            }
        }

        if ($ubah_password) {
            $password_hash = password_hash($password_baru, PASSWORD_DEFAULT);
            $stmt = $koneksi->prepare(
                "UPDATE users SET nama_lengkap = ?, email = ?, password = ?, foto_profil = ? WHERE id_user = ?"
            );
            $stmt->bind_param("ssssi", $nama_lengkap_baru, $email_baru, $password_hash, $nama_file_final, $id_user);
        } else {
            $stmt = $koneksi->prepare(
                "UPDATE users SET nama_lengkap = ?, email = ?, foto_profil = ? WHERE id_user = ?"
            );
            $stmt->bind_param("sssi", $nama_lengkap_baru, $email_baru, $nama_file_final, $id_user);
        }

        if ($stmt->execute()) {
            // Perbarui session agar perubahan langsung tampil tanpa logout-login ulang
            $_SESSION['nama_lengkap'] = $nama_lengkap_baru;
            $_SESSION['email']        = $email_baru;
            $_SESSION['foto_profil']  = $nama_file_final;

            $success = 'Profil berhasil diperbarui.';
            $foto_profil = $nama_file_final; // untuk tampilan
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
        <?php tampil_flash(); ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> <?= htmlspecialchars($success) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
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

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="fw-semibold mb-1">Profil Saya</h4>
                <p class="text-muted mb-0">Kelola data diri dan foto profil Anda.</p>
            </div>
        </div>

        <div class="row g-4">
            <!-- Kartu Foto Profil -->
            <div class="col-12 col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <?php if (!empty($foto_profil) && file_exists($folder_upload . $foto_profil)): ?>
                            <img src="/simdesain/assets/uploads/profil/<?= htmlspecialchars($foto_profil) ?>"
                                 alt="Foto Profil"
                                 class="rounded-circle img-thumbnail mb-3"
                                 style="width: 150px; height: 150px; object-fit: cover;">
                        <?php else: ?>
                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mx-auto mb-3"
                                 style="width: 150px; height: 150px;">
                                <i class="bi bi-person-fill text-white" style="font-size: 4rem;"></i>
                            </div>
                        <?php endif; ?>
                        <h5 class="fw-semibold"><?= htmlspecialchars($user['nama_lengkap']) ?></h5>
                        <span class="badge bg-secondary text-uppercase"><?= htmlspecialchars($user['role']) ?></span>
                    </div>
                </div>
            </div>

            <!-- Form Edit Profil -->
            <div class="col-12 col-md-8">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="index.php" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama_lengkap" class="form-control"
                                       value="<?= htmlspecialchars($nama_lengkap) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control"
                                       value="<?= htmlspecialchars($email) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Foto Profil</label>
                                <input type="file" name="foto_profil" class="form-control"
                                       accept=".jpg,.jpeg,.png,.webp">
                                <div class="form-text">Format JPG/JPEG/PNG/WEBP, maksimal 2MB. Kosongkan jika tidak ingin mengganti foto.</div>
                            </div>

                            <hr class="my-4">
                            <h6 class="fw-semibold mb-3">Ganti Password</h6>
                            <p class="text-muted small mb-3">Kosongkan jika tidak ingin mengganti password.</p>

                            <div class="mb-3">
                                <label class="form-label">Password Lama</label>
                                <input type="password" name="password_lama" class="form-control"
                                       placeholder="Diisi jika ingin ganti password">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password Baru</label>
                                <input type="password" name="password_baru" class="form-control" minlength="6"
                                       placeholder="Minimal 6 karakter">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Konfirmasi Password Baru</label>
                                <input type="password" name="konfirmasi_password" class="form-control" minlength="6"
                                       placeholder="Ulangi password baru">
                            </div>

                            <button type="submit" class="btn btn-dark">
                                <i class="bi bi-save"></i> Simpan Perubahan
                            </button>
                            <a href="/simdesain/dashboard.php" class="btn btn-secondary">Kembali ke Dashboard</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>