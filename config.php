<?php
// config.php - File konfigurasi untuk menangani session

// Mulai session hanya jika belum ada sesi yang dimulai
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
