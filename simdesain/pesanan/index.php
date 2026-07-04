<?php
/**
 * =========================================================
 * FILE: pesanan/index.php
 * FUNGSI: Menampilkan daftar pesanan, lengkap dengan nama
 *         pelanggan, proyek, status (badge warna), dan pencarian.
 * =========================================================
 */
require_once '../includes/auth.php';
require_once '../config/koneksi.php';
require_once '../includes/fungsi.php';

$halaman_aktif = 'pesanan';

$kata_kunci = isset($_GET['cari']) ? bersihkan_input($_GET['cari']) : '';

// JOIN ke pelanggan & proyek_desain supaya nama-nama terkait bisa langsung ditampilkan
$sql_dasar = "SELECT ps.*, pl.nama_pelanggan, pr.nama_proyek
              FROM pesanan ps
              JOIN pelanggan pl ON ps.id_pelanggan = pl.id_pelanggan
              JOIN proyek_desain pr ON ps.id_proyek = pr.id_proyek";

if ($kata_kunci !== '') {
    $sql = $sql_dasar . " WHERE pl.nama_pelanggan LIKE ? OR ps.status_pesanan LIKE ?
                          ORDER BY ps.id_pesanan DESC";
    $stmt = $koneksi->prepare($sql);
    $pencarian = "%{$kata_kunci}%";
    $stmt->bind_param("ss", $pencarian, $pencarian);
    $stmt->execute();
    $data_pesanan = $stmt->get_result();
} else {
    $data_pesanan = $koneksi->query($sql_dasar . " ORDER BY ps.id_pesanan DESC");
}

/**
 * Menentukan warna badge Bootstrap berdasarkan status pesanan,
 * supaya status mudah dipindai secara visual di tabel.
 */
function warna_status($status) {
    return match ($status) {
        'baru'    => 'bg-info text-dark',
        'proses'  => 'bg-warning text-dark',
        'selesai' => 'bg-success',
        'batal'   => 'bg-danger',
        default   => 'bg-secondary',
    };
}
?>
<?php require_once '../includes/header.php'; ?>

<div class="wrapper">
    <?php require_once '../includes/sidebar.php'; ?>

    <div class="content-area p-4">
        <?php tampil_flash(); ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="fw-semibold mb-1">Data Pesanan</h4>
                <p class="text-muted mb-0">Kelola pesanan layanan desain dari pelanggan.</p>
            </div>
            <a href="tambah.php" class="btn btn-dark">
                <i class="bi bi-plus-lg"></i> Tambah Pesanan
            </a>
        </div>

        <form method="GET" action="index.php" class="mb-3">
            <div class="input-group" style="max-width: 400px;">
                <input type="text" name="cari" class="form-control"
                       placeholder="Cari nama pelanggan atau status..."
                       value="<?= htmlspecialchars($kata_kunci) ?>">
                <button type="submit" class="btn btn-outline-secondary">
                    <i class="bi bi-search"></i>
                </button>
                <?php if ($kata_kunci !== ''): ?>
                    <a href="index.php" class="btn btn-outline-danger">Reset</a>
                <?php endif; ?>
            </div>
        </form>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Pelanggan</th>
                            <th>Proyek</th>
                            <th>Tanggal Pesan</th>
                            <th>Status</th>
                            <th style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($data_pesanan->num_rows > 0): ?>
                            <?php $no = 1; ?>
                            <?php while ($row = $data_pesanan->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['nama_pelanggan']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_proyek']) ?></td>
                                    <td><?= date('d M Y', strtotime($row['tanggal_pesan'])) ?></td>
                                    <td>
                                        <span class="badge <?= warna_status($row['status_pesanan']) ?> text-uppercase">
                                            <?= htmlspecialchars($row['status_pesanan']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="edit.php?id=<?= $row['id_pesanan'] ?>"
                                           class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalHapus"
                                                data-id="<?= $row['id_pesanan'] ?>"
                                                data-nama="<?= htmlspecialchars($row['nama_pelanggan'] . ' - ' . $row['nama_proyek']) ?>"
                                                title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    Belum ada data pesanan<?= $kata_kunci !== '' ? ' yang cocok dengan pencarian.' : '.' ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="modalHapus" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus pesanan
                "<strong id="namaPesananHapus"></strong>"?
                <br><small class="text-muted">Data yang sudah dihapus tidak dapat dikembalikan.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a href="#" id="linkHapusPesanan" class="btn btn-danger">Ya, Hapus</a>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('modalHapus').addEventListener('show.bs.modal', function (event) {
    const tombol = event.relatedTarget;
    document.getElementById('namaPesananHapus').textContent = tombol.getAttribute('data-nama');
    document.getElementById('linkHapusPesanan').href = 'hapus.php?id=' + tombol.getAttribute('data-id');
});
</script>

<?php require_once '../includes/footer.php'; ?>
