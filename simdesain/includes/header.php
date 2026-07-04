<?php
/**
 * =========================================================
 * FILE: includes/header.php
 * FUNGSI: Bagian atas HTML yang dipakai bersama di semua halaman
 *         (buka tag <html>, load Bootstrap, navbar atas).
 *
 * CATATAN: File ini mengasumsikan session sudah dimulai
 *          (biasanya lewat includes/auth.php yang sudah dipanggil
 *          sebelumnya di halaman yang meng-include header ini).
 * =========================================================
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMDESAIN - Sistem Informasi Layanan Desain Grafis</title>

    <!-- Bootstrap 5 CSS via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <!-- CSS kustom tambahan -->
    <link rel="stylesheet" href="/simdesain/assets/css/style.css">
</head>
<body>

<!-- Navbar atas: hanya tampil jika user sudah login (ada session) -->
<?php if (isset($_SESSION['id_user'])): ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-3">
    <a class="navbar-brand fw-semibold" href="/simdesain/dashboard.php">
        <i class="bi bi-palette2"></i> SIMDESAIN
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarMenu">
        <ul class="navbar-nav align-items-lg-center">
            <li class="nav-item me-3 text-light small d-flex align-items-center gap-2">
                <?php if (!empty($_SESSION['foto_profil']) && file_exists(__DIR__ . '/../assets/uploads/profil/' . $_SESSION['foto_profil'])): ?>
                    <img src="/simdesain/assets/uploads/profil/<?= htmlspecialchars($_SESSION['foto_profil']) ?>"
                         alt="Foto"
                         class="rounded-circle"
                         style="width: 30px; height: 30px; object-fit: cover;">
                <?php else: ?>
                    <i class="bi bi-person-circle"></i>
                <?php endif; ?>
                <?= htmlspecialchars($_SESSION['nama_lengkap']) ?>
                <span class="badge bg-secondary text-uppercase"><?= htmlspecialchars($_SESSION['role']) ?></span>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/simdesain/profil/index.php">Profil</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/simdesain/auth/logout.php">Logout</a>
            </li>
        </ul>
    </div>
</nav>
<?php endif; ?>
