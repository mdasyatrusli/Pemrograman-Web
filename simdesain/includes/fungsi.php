<?php
/**
 * =========================================================
 * FILE: includes/fungsi.php
 * FUNGSI: Kumpulan fungsi bantu yang dipakai berulang kali
 *         di seluruh aplikasi (notifikasi & sanitasi input).
 * =========================================================
 */

/**
 * Menyimpan pesan notifikasi ke dalam session.
 * Dipanggil SEBELUM redirect, misal setelah insert/update/delete berhasil.
 *
 * @param string $tipe  'success' atau 'danger' (sesuai class warna Bootstrap alert)
 * @param string $pesan Isi pesan yang ingin ditampilkan ke pengguna
 */
function set_flash($tipe, $pesan) {
    $_SESSION['flash'] = [
        'tipe'  => $tipe,
        'pesan' => $pesan
    ];
}

/**
 * Menampilkan notifikasi (jika ada) dalam bentuk Bootstrap alert,
 * lalu langsung menghapusnya dari session supaya tidak muncul berulang
 * ketika halaman di-refresh.
 * Dipanggil di bagian atas halaman, biasanya tepat di bawah header.php
 */
function tampil_flash() {
    if (isset($_SESSION['flash'])) {
        $tipe  = htmlspecialchars($_SESSION['flash']['tipe']);
        $pesan = htmlspecialchars($_SESSION['flash']['pesan']);

        echo "
        <div class='alert alert-{$tipe} alert-dismissible fade show m-3' role='alert'>
            {$pesan}
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>";

        // Hapus supaya tidak tampil lagi setelah refresh
        unset($_SESSION['flash']);
    }
}

/**
 * Membersihkan input dari spasi berlebih dan tag HTML berbahaya (XSS).
 * Dipanggil untuk setiap data yang berasal dari $_POST sebelum diproses.
 *
 * @param string $data Data mentah dari form
 * @return string Data yang sudah dibersihkan
 */
function bersihkan_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
