<?php
/**
 * =========================================================
 * FILE: includes/sidebar.php
 * FUNGSI: Menu navigasi utama ke semua modul aplikasi.
 *         Di-include di dashboard.php dan semua halaman modul CRUD.
 *
 * CATATAN: Variabel $halaman_aktif dipakai untuk menyorot
 *          menu mana yang sedang dibuka (opsional, di-set
 *          di halaman pemanggil sebelum include ini).
 *          Contoh: $halaman_aktif = 'pelanggan';
 * =========================================================
 */
if (!isset($halaman_aktif)) {
    $halaman_aktif = '';
}

// Fungsi kecil untuk menentukan class 'active' pada menu
function menu_aktif($nama, $halaman_aktif) {
    return $nama === $halaman_aktif ? 'active bg-dark-subtle' : '';
}
?>
<div class="sidebar bg-white border-end">
    <div class="list-group list-group-flush pt-2">
        <a href="/simdesain/dashboard.php"
           class="list-group-item list-group-item-action <?= menu_aktif('dashboard', $halaman_aktif) ?>">
            <i class="bi bi-speedometer2 me-2"></i> Dashboard
        </a>
        <a href="/simdesain/pelanggan/index.php"
           class="list-group-item list-group-item-action <?= menu_aktif('pelanggan', $halaman_aktif) ?>">
            <i class="bi bi-people me-2"></i> Data Pelanggan
        </a>
        <a href="/simdesain/kategori/index.php"
           class="list-group-item list-group-item-action <?= menu_aktif('kategori', $halaman_aktif) ?>">
            <i class="bi bi-tags me-2"></i> Kategori Desain
        </a>
        <a href="/simdesain/proyek/index.php"
           class="list-group-item list-group-item-action <?= menu_aktif('proyek', $halaman_aktif) ?>">
            <i class="bi bi-images me-2"></i> Proyek Desain
        </a>
        <a href="/simdesain/pesanan/index.php"
           class="list-group-item list-group-item-action <?= menu_aktif('pesanan', $halaman_aktif) ?>">
            <i class="bi bi-cart-check me-2"></i> Data Pesanan
        </a>
        <a href="/simdesain/profil/index.php"
           class="list-group-item list-group-item-action <?= menu_aktif('profil', $halaman_aktif) ?>">
            <i class="bi bi-person-gear me-2"></i> Profil Saya
        </a>
    </div>
</div>
