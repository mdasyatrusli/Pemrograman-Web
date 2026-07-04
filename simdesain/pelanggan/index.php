<?php
/**
 * =========================================================
 * FILE: pelanggan/index.php
 * FUNGSI: Menampilkan daftar data pelanggan lengkap dengan
 *         fitur pencarian. Menjadi pintu masuk ke tambah/edit/hapus.
 * =========================================================
 */
require_once '../includes/auth.php';
require_once '../config/koneksi.php';
require_once '../includes/fungsi.php';

$halaman_aktif = 'pelanggan';

// --- Ambil kata kunci pencarian dari URL (?cari=...), jika ada ---
$kata_kunci = isset($_GET['cari']) ? bersihkan_input($_GET['cari']) : '';

// --- Ambil data pelanggan, dengan atau tanpa filter pencarian ---
if ($kata_kunci !== '') {
    // Pakai prepared statement + wildcard LIKE untuk pencarian
    $sql = "SELECT * FROM pelanggan
            WHERE nama_pelanggan LIKE ? OR email LIKE ? OR no_telepon LIKE ?
            ORDER BY id_pelanggan DESC";
    $stmt = $koneksi->prepare($sql);
    $pencarian = "%{$kata_kunci}%";
    $stmt->bind_param("sss", $pencarian, $pencarian, $pencarian);
    $stmt->execute();
    $data_pelanggan = $stmt->get_result();
} else {
    $sql = "SELECT * FROM pelanggan ORDER BY id_pelanggan DESC";
    $data_pelanggan = $koneksi->query($sql);
}
?>
<?php require_once '../includes/header.php'; ?>

<div class="wrapper">
    <?php require_once '../includes/sidebar.php'; ?>

    <div class="content-area p-4">
        <?php tampil_flash(); ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="fw-semibold mb-1">Data Pelanggan</h4>
                <p class="text-muted mb-0">Kelola data pelanggan layanan desain grafis.</p>
            </div>
            <a href="tambah.php" class="btn btn-dark">
                <i class="bi bi-plus-lg"></i> Tambah Pelanggan
            </a>
        </div>

        <!-- Form Pencarian -->
        <form method="GET" action="index.php" class="mb-3">
            <div class="input-group" style="max-width: 400px;">
                <input type="text" name="cari" class="form-control"
                       placeholder="Cari nama, email, atau no. telepon..."
                       value="<?= htmlspecialchars($kata_kunci) ?>">
                <button type="submit" class="btn btn-outline-secondary">
                    <i class="bi bi-search"></i>
                </button>
                <?php if ($kata_kunci !== ''): ?>
                    <a href="index.php" class="btn btn-outline-danger">Reset</a>
                <?php endif; ?>
            </div>
        </form>

        <!-- Tabel Data -->
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Nama Pelanggan</th>
                            <th>Email</th>
                            <th>No. Telepon</th>
                            <th>Alamat</th>
                            <th style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($data_pelanggan->num_rows > 0): ?>
                            <?php $no = 1; ?>
                            <?php while ($row = $data_pelanggan->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['nama_pelanggan']) ?></td>
                                    <td><?= htmlspecialchars($row['email'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($row['no_telepon'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($row['alamat'] ?? '-') ?></td>
                                    <td>
                                        <a href="edit.php?id=<?= $row['id_pelanggan'] ?>"
                                           class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <!-- Tombol hapus memicu modal konfirmasi, bukan langsung menghapus -->
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalHapus"
                                                data-id="<?= $row['id_pelanggan'] ?>"
                                                data-nama="<?= htmlspecialchars($row['nama_pelanggan']) ?>"
                                                title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    Belum ada data pelanggan<?= $kata_kunci !== '' ? ' yang cocok dengan pencarian.' : '.' ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus (satu modal dipakai untuk semua baris, id diisi via JS) -->
<div class="modal fade" id="modalHapus" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus data pelanggan
                "<strong id="namaPelangganHapus"></strong>"?
                <br><small class="text-muted">Data yang sudah dihapus tidak dapat dikembalikan.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a href="#" id="linkHapusPelanggan" class="btn btn-danger">Ya, Hapus</a>
            </div>
        </div>
    </div>
</div>

<script>
// Mengisi modal konfirmasi hapus dengan data baris yang diklik
document.getElementById('modalHapus').addEventListener('show.bs.modal', function (event) {
    const tombol = event.relatedTarget;
    const id = tombol.getAttribute('data-id');
    const nama = tombol.getAttribute('data-nama');

    document.getElementById('namaPelangganHapus').textContent = nama;
    document.getElementById('linkHapusPelanggan').href = 'hapus.php?id=' + id;
});
</script>

<?php require_once '../includes/footer.php'; ?>
