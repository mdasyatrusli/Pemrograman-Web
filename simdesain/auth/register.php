<?php
/**
 * =========================================================
 * FILE: auth/register.php
 * FUNGSI: Menampilkan form pendaftaran & memproses pendaftaran
 *         akun baru. Role akun baru otomatis 'staff'.
 * =========================================================
 */
session_start();
require_once '../config/koneksi.php';
require_once '../includes/fungsi.php';

// Jika sudah login, tidak perlu register lagi -> lempar ke dashboard
if (isset($_SESSION['id_user'])) {
    header("Location: /simdesain/dashboard.php");
    exit;
}

$error = []; // menampung pesan-pesan error validasi

// --- Proses ketika form disubmit (method POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Ambil & bersihkan input
    $nama_lengkap    = bersihkan_input($_POST['nama_lengkap']);
    $email           = bersihkan_input($_POST['email']);
    $password        = $_POST['password']; // tidak di-htmlspecialchars karena akan di-hash, bukan ditampilkan
    $konfirmasi_pass = $_POST['konfirmasi_password'];

    // --- Validasi input ---
    if (empty($nama_lengkap)) {
        $error[] = "Nama lengkap wajib diisi.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error[] = "Format email tidak valid.";
    }
    if (strlen($password) < 6) {
        $error[] = "Password minimal 6 karakter.";
    }
    if ($password !== $konfirmasi_pass) {
        $error[] = "Konfirmasi password tidak cocok.";
    }

    // Cek apakah email sudah terdaftar (pakai prepared statement)
    if (empty($error)) {
        $cek = $koneksi->prepare("SELECT id_user FROM users WHERE email = ?");
        $cek->bind_param("s", $email);
        $cek->execute();
        $cek->store_result();
        if ($cek->num_rows > 0) {
            $error[] = "Email sudah terdaftar, silakan gunakan email lain.";
        }
        $cek->close();
    }

    // Jika tidak ada error, simpan data ke database
    if (empty($error)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $koneksi->prepare(
            "INSERT INTO users (nama_lengkap, email, password, role) VALUES (?, ?, ?, 'staff')"
        );
        $stmt->bind_param("sss", $nama_lengkap, $email, $password_hash);

        if ($stmt->execute()) {
            set_flash('success', 'Pendaftaran berhasil! Silakan login.');
            header("Location: login.php");
            exit;
        } else {
            $error[] = "Terjadi kesalahan saat menyimpan data. Coba lagi.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - SIMDESAIN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body class="bg-light">
<div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="card shadow-sm" style="max-width: 420px; width: 100%;">
        <div class="card-body p-4">
            <h4 class="card-title text-center mb-4 fw-semibold">
                <i class="bi bi-palette2"></i> Daftar Akun SIMDESAIN
            </h4>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0 ps-3">
                        <?php foreach ($error as $e): ?>
                            <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="register.php">
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" class="form-control"
                           value="<?= isset($nama_lengkap) ? htmlspecialchars($nama_lengkap) : '' ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control"
                           value="<?= isset($email) ? htmlspecialchars($email) : '' ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" minlength="6" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Konfirmasi Password</label>
                    <input type="password" name="konfirmasi_password" class="form-control" minlength="6" required>
                </div>
                <button type="submit" class="btn btn-dark w-100">Daftar</button>
            </form>

            <p class="text-center mt-3 mb-0 small">
                Sudah punya akun? <a href="login.php">Login di sini</a>
            </p>
        </div>
    </div>
</div>
</body>
</html>
