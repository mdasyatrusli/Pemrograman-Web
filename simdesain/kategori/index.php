<?php
/**
 * =========================================================
 * FILE: kategori/index.php
 * FUNGSI: Menampilkan daftar kategori desain lengkap dengan
 *         fitur pencarian. Tombol hapus hanya tampil untuk admin.
 * =========================================================
 */
require_once '../includes/auth.php';
require_once '../config/koneksi.php';
require_once '../includes/fungsi.php';

$halaman_aktif = 'kategori';

$kata_kunci = isset($_GET['cari']) ? bersihkan_input($_GET['cari']) : '';

if ($kata_kunci !== '') {
    $sql = "SELECT * FROM kategori_desain WHERE nama_kategori LIKE ? ORDER BY id_kategori DESC";
    $stmt = $koneksi->prepare($sql);
    $pencarian = "%{$kata_kunci}%";
    $stmt->bind_param("s", $pencarian);
    $stmt->execute();
    $data_kategori = $stmt->get_result();
} else {
    $data_kategori = $koneksi->query("SELECT * FROM kategori_desain ORDER BY id_kategori DESC");
}
?>
<?php require_once '../includes/header.php'; ?>

<div class="wrapper">
    <?php require_once '../includes/sidebar.php'; ?>

    <div class="content-area p-4">
        <?php tampil_flash(); ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="fw-semibold mb-1">Kategori Desain</h4>
                <p class="text-muted mb-0">Kelola jenis layanan desain yang ditawarkan.</p>
            </div>
            <a href="tambah.php" class="btn btn-dark">
                <i class="bi bi-plus-lg"></i> Tambah Kategori
            </a>
        </div>

        <form method="GET" action="index.php" class="mb-3">
            <div class="input-group" style="max-width: 400px;">
                <input type="text" name="cari" class="form-control"
                       placeholder="Cari nama kategori..."
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
                            <th>Nama Kategori</th>
                            <th>Deskripsi</th>
                            <th style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($data_kategori->num_rows > 0): ?>
                            <?php $no = 1; ?>
                            <?php while ($row = $data_kategori->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['nama_kategori']) ?></td>
                                    <td><?= htmlspecialchars($row['deskripsi'] ?? '-') ?></td>
                                    <td>
                                        <a href="edit.php?id=<?= $row['id_kategori'] ?>"
                                           class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <?php if ($_SESSION['role'] === 'admin'): ?>
                                            <!-- Tombol hapus HANYA tampil untuk admin -->
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalHapus"
                                                    data-id="<?= $row['id_kategori'] ?>"
                                                    data-nama="<?= htmlspecialchars($row['nama_kategori']) ?>"
                                                    title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    Belum ada data kategori<?= $kata_kunci !== '' ? ' yang cocok dengan pencarian.' : '.' ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php if ($_SESSION['role'] !== 'admin'): ?>
            <p class="text-muted small mt-2">
                <i class="bi bi-info-circle"></i> Hanya admin yang dapat menghapus kategori desain.
            </p>
        <?php endif; ?>
    </div>
</div>

<?php if ($_SESSION['role'] === 'admin'): ?>
<!-- Modal Konfirmasi Hapus (hanya perlu di-render untuk admin) -->
<div class="modal fade" id="modalHapus" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus kategori
                "<strong id="namaKategoriHapus"></strong>"?
                <br><small class="text-muted">Data yang sudah dihapus tidak dapat dikembalikan.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a href="#" id="linkHapusKategori" class="btn btn-danger">Ya, Hapus</a>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('modalHapus').addEventListener('show.bs.modal', function (event) {
    const tombol = event.relatedTarget;
    const id = tombol.getAttribute('data-id');
    const nama = tombol.getAttribute('data-nama');

    document.getElementById('namaKategoriHapus').textContent = nama;
    document.getElementById('linkHapusKategori').href = 'hapus.php?id=' + id;
});
</script>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>
