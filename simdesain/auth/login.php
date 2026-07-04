<?php
/**
 * =========================================================
 * FILE: auth/login.php
 * FUNGSI: Menampilkan form login & memverifikasi kredensial.
 *         Jika berhasil, membuat session login pengguna.
 * =========================================================
 */
session_start();
require_once '../config/koneksi.php';
require_once '../includes/fungsi.php';

// Jika sudah login, langsung lempar ke dashboard
if (isset($_SESSION['id_user'])) {
    header("Location: /simdesain/dashboard.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = bersihkan_input($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Email dan password wajib diisi.";
    } else {
        // Ambil data user berdasarkan email menggunakan prepared statement
        $stmt = $koneksi->prepare("SELECT id_user, nama_lengkap, email, password, role, foto_profil FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $hasil = $stmt->get_result();

        if ($hasil->num_rows === 1) {
            $user = $hasil->fetch_assoc();

            // Verifikasi password dengan hash yang tersimpan di database
            if (password_verify($password, $user['password'])) {
                // Password cocok -> buat session login
                $_SESSION['id_user']      = $user['id_user'];
                $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                $_SESSION['email']        = $user['email'];
                $_SESSION['role']         = $user['role'];
                $_SESSION['foto_profil']  = $user['foto_profil'];

                header("Location: /simdesain/dashboard.php");
                exit;
            } else {
                $error = "Email atau password salah.";
            }
        } else {
            $error = "Email atau password salah.";
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
    <title>Login - SIMDESAIN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body class="bg-light">
<div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="card shadow-sm" style="max-width: 400px; width: 100%;">
        <div class="card-body p-4">
            <h4 class="card-title text-center mb-4 fw-semibold">
                <i class="bi bi-palette2"></i> Login SIMDESAIN
            </h4>

            <?php tampil_flash(); // menampilkan notifikasi, misal setelah register berhasil ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-dark w-100">Login</button>
            </form>

            <p class="text-center mt-3 mb-0 small">
                Belum punya akun? <a href="register.php">Daftar di sini</a>
            </p>

            <div class="alert alert-secondary mt-3 mb-0 small">
                <strong>Akun demo:</strong><br>
                Email: admin@simdesain.test<br>
                Password: admin123
            </div>
        </div>
    </div>
</div>
</body>
</html>
