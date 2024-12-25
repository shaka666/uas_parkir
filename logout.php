<?php
include('config.php');
// Mulai session hanya jika belum ada sesi yang dimulai
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Menghancurkan sesi saat logout
session_unset();  // Menghapus semua session
session_destroy();  // Menghancurkan session
header("Location: login.php");  // Pengalihan ke halaman login
exit();
?>

