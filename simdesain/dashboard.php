<?php
/**
 * =========================================================
 * FILE: dashboard.php
 * FUNGSI: Halaman utama setelah login. Menampilkan ringkasan
 *         jumlah data (statistik) dari tiap modul, dengan
 *         layout sidebar navigasi.
 * =========================================================
 */
require_once 'includes/auth.php';   // proteksi: hanya user login yang boleh akses
require_once 'config/koneksi.php';
require_once 'includes/fungsi.php';

// Menandai menu 'dashboard' sebagai aktif di sidebar
$halaman_aktif = 'dashboard';

/**
 * Mengambil jumlah baris dari sebuah tabel.
 * Menggunakan query sederhana COUNT(*) untuk tiap tabel.
 *
 * @param mysqli $koneksi Koneksi database aktif
 * @param string $tabel   Nama tabel (hanya dipanggil dengan nilai tetap di bawah, bukan input pengguna)
 * @return int Jumlah baris data
 */
function hitung_data($koneksi, $tabel) {
    $hasil = $koneksi->query("SELECT COUNT(*) AS jumlah FROM {$tabel}");
    $baris = $hasil->fetch_assoc();
    return (int) $baris['jumlah'];
}

$total_pelanggan = hitung_data($koneksi, 'pelanggan');
$total_kategori  = hitung_data($koneksi, 'kategori_desain');
$total_proyek    = hitung_data($koneksi, 'proyek_desain');
$total_pesanan   = hitung_data($koneksi, 'pesanan');
?>
<?php require_once 'includes/header.php'; ?>

<div class="wrapper">
    <?php require_once 'includes/sidebar.php'; ?>

    <div class="content-area p-4">
        <?php tampil_flash(); ?>

        <h4 class="fw-semibold mb-1">Dashboard</h4>
        <p class="text-muted mb-4">Ringkasan data layanan desain grafis Anda.</p>

        <!-- Kartu Statistik -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3">
                <div class="card stat-card bg-primary p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-number"><?= $total_pelanggan ?></div>
                            <div class="small">Pelanggan</div>
                        </div>
                        <i class="bi bi-people stat-icon"></i>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card stat-card bg-success p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-number"><?= $total_kategori ?></div>
                            <div class="small">Kategori Desain</div>
                        </div>
                        <i class="bi bi-tags stat-icon"></i>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card stat-card bg-warning p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-number"><?= $total_proyek ?></div>
                            <div class="small">Proyek Desain</div>
                        </div>
                        <i class="bi bi-images stat-icon"></i>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card stat-card bg-danger p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-number"><?= $total_pesanan ?></div>
                            <div class="small">Pesanan</div>
                        </div>
                        <i class="bi bi-cart-check stat-icon"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info akun yang sedang login -->
        <div class="card">
            <div class="card-body">
                <h6 class="fw-semibold mb-3">Info Akun</h6>
                <table class="table table-sm mb-0">
                    <tr>
                        <td class="text-muted" style="width: 160px;">Nama</td>
                        <td><?= htmlspecialchars($_SESSION['nama_lengkap']) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Email</td>
                        <td><?= htmlspecialchars($_SESSION['email']) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Role</td>
                        <td><span class="badge bg-secondary text-uppercase"><?= htmlspecialchars($_SESSION['role']) ?></span></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
