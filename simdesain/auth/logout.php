<?php
/**
 * =========================================================
 * FILE: auth/logout.php
 * FUNGSI: Menghapus semua data session (mengakhiri login),
 *         lalu mengarahkan pengguna kembali ke halaman login.
 * =========================================================
 */
session_start();

// Menghapus semua variabel session
$_SESSION = [];

// Menghancurkan session secara keseluruhan
session_destroy();

// Arahkan kembali ke halaman login
header("Location: /simdesain/auth/login.php");
exit;
