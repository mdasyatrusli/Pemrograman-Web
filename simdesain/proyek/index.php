<?php
/**
 * =========================================================
 * FILE: proyek/index.php
 * FUNGSI: Menampilkan daftar proyek desain, lengkap dengan
 *         thumbnail gambar hasil desain dan fitur pencarian.
 * =========================================================
 */
require_once '../includes/auth.php';
require_once '../config/koneksi.php';
require_once '../includes/fungsi.php';

$halaman_aktif = 'proyek';

$kata_kunci = isset($_GET['cari']) ? bersihkan_input($_GET['cari']) : '';

// JOIN ke kategori_desain supaya nama kategori bisa langsung ditampilkan
if ($kata_kunci !== '') {
    $sql = "SELECT p.*, k.nama_kategori
            FROM proyek_desain p
            JOIN kategori_desain k ON p.id_kategori = k.id_kategori
            WHERE p.nama_proyek LIKE ? OR k.nama_kategori LIKE ?
            ORDER BY p.id_proyek DESC";
    $stmt = $koneksi->prepare($sql);
    $pencarian = "%{$kata_kunci}%";
    $stmt->bind_param("ss", $pencarian, $pencarian);
    $stmt->execute();
    $data_proyek = $stmt->get_result();
} else {
    $sql = "SELECT p.*, k.nama_kategori
            FROM proyek_desain p
            JOIN kategori_desain k ON p.id_kategori = k.id_kategori
            ORDER BY p.id_proyek DESC";
    $data_proyek = $koneksi->query($sql);
}
?>
<?php require_once '../includes/header.php'; ?>

<div class="wrapper">
    <?php require_once '../includes/sidebar.php'; ?>

    <div class="content-area p-4">
        <?php tampil_flash(); ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="fw-semibold mb-1">Proyek Desain</h4>
                <p class="text-muted mb-0">Kelola data proyek dan hasil karya desain.</p>
            </div>
            <a href="tambah.php" class="btn btn-dark">
                <i class="bi bi-plus-lg"></i> Tambah Proyek
            </a>
        </div>

        <form method="GET" action="index.php" class="mb-3">
            <div class="input-group" style="max-width: 400px;">
                <input type="text" name="cari" class="form-control"
                       placeholder="Cari nama proyek atau kategori..."
                       value="<?= htmlspecialchars($kata_kunci) ?>">
                <button type="submit" class="btn btn-outline-secondary">
                    <i class="bi bi-search"></i>
                </button>
                <?php if ($kata_kunci !== ''): ?>
                    <a href="index.php" class="btn btn-outline-danger">Reset</a>
                <?php endif; ?>
            </div>
        </form>

        <!-- Ditampilkan sebagai grid kartu, lebih cocok untuk konten visual seperti gambar -->
        <div class="row g-3">
            <?php if ($data_proyek->num_rows > 0): ?>
                <?php while ($row = $data_proyek->fetch_assoc()): ?>
                    <div class="col-sm-6 col-lg-4 col-xl-3">
                        <div class="card h-100">
                            <?php if (!empty($row['gambar_hasil']) && file_exists('../assets/uploads/proyek/' . $row['gambar_hasil'])): ?>
                                <img src="/simdesain/assets/uploads/proyek/<?= htmlspecialchars($row['gambar_hasil']) ?>"
                                     class="card-img-top" style="height: 160px; object-fit: cover;"
                                     alt="<?= htmlspecialchars($row['nama_proyek']) ?>">
                            <?php else: ?>
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center text-muted"
                                     style="height: 160px;">
                                    <i class="bi bi-image" style="font-size: 2rem;"></i>
                                </div>
                            <?php endif; ?>
                            <div class="card-body">
                                <span class="badge bg-secondary mb-2"><?= htmlspecialchars($row['nama_kategori']) ?></span>
                                <h6 class="card-title fw-semibold"><?= htmlspecialchars($row['nama_proyek']) ?></h6>
                                <p class="card-text small text-muted">
                                    <?= htmlspecialchars(substr($row['deskripsi'] ?? '-', 0, 60)) ?><?= strlen($row['deskripsi'] ?? '') > 60 ? '...' : '' ?>
                                </p>
                                <p class="card-text small text-muted mb-2">
                                    <i class="bi bi-calendar3"></i> <?= date('d M Y', strtotime($row['tanggal_dibuat'])) ?>
                                </p>
                            </div>
                            <div class="card-footer bg-white d-flex gap-2">
                                <a href="edit.php?id=<?= $row['id_proyek'] ?>" class="btn btn-sm btn-outline-primary flex-fill">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger flex-fill"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalHapus"
                                        data-id="<?= $row['id_proyek'] ?>"
                                        data-nama="<?= htmlspecialchars($row['nama_proyek']) ?>">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center text-muted py-4">
                            Belum ada data proyek<?= $kata_kunci !== '' ? ' yang cocok dengan pencarian.' : '.' ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
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
                Apakah Anda yakin ingin menghapus proyek
                "<strong id="namaProyekHapus"></strong>"?
                <br><small class="text-muted">Gambar hasil desain terkait juga akan ikut terhapus.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a href="#" id="linkHapusProyek" class="btn btn-danger">Ya, Hapus</a>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('modalHapus').addEventListener('show.bs.modal', function (event) {
    const tombol = event.relatedTarget;
    document.getElementById('namaProyekHapus').textContent = tombol.getAttribute('data-nama');
    document.getElementById('linkHapusProyek').href = 'hapus.php?id=' + tombol.getAttribute('data-id');
});
</script>

<?php require_once '../includes/footer.php'; ?>
