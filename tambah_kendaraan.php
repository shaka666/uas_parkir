<?php 
date_default_timezone_set('Asia/Jakarta'); // Atur waktu zona asia
$servername = "localhost"; // Server local host karena server satu komputer dengan program. Kalau program dan server beda komputer, localhost dapat diganti dengan ip server komputer yang terhubung.
$username = "root"; // Nama server
$password = ""; // Password server. Apabila username dan passoword diubah maka untuk bisa mengakses database harus memasukan username dan password yang sudah dibuat.
$dbname = "rfid_db"; // rfid db adalah database yang ada di server. maka dari itu saya bisa mengakses rfid db. jika ada selain rfid db maka tinggal ubah saja rfid db dengan db lainnya.

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname); // $Conn untuk mengubungkan SQL ke program agar bisa mengakses servername, username, passsword dan db.
// Jika berhasil terhubung maka fungsi seperti select, update, insert dan delete dapat digunakan

// $SERVER akan menerima data jika ada yang POST ke $SERVER tambah_kendaraan.php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form kendaraan
    $nim = $_POST['nim']; // Mengambil NIM dari form yang ada di index.php yang nge POST ke file ini
    $plat = $_POST['plat']; // Mengambil plat dari form yang ada di index.php yang nge POST ke file ini
    $vehicle = $_POST['vehicle'] ? $_POST['vehicle'] : "MOTOR";

    // Waktu masuk: jika tidak ada input waktu masuk, gunakan waktu saat ini
    $waktu_masuk = !empty($_POST['waktu_masuk']) ? $_POST['waktu_masuk'] : date("Y-m-d H:i:s");

    // Jika waktu keluar diisi, gunakan nilai tersebut
    $waktu_keluar = isset($_POST['waktu_keluar']) ? $_POST['waktu_keluar'] : null;

    // Insert data ke tabel rfid_logs
    $stmt_insert = $conn->prepare("INSERT INTO rfid_logs (nim, plat, jenis_kendaraan, waktu_masuk, waktu_keluar) VALUES (?, ?, ?, ?, ?)");
    $stmt_insert->bind_param("sssss", $nim, $plat, $vehicle, $waktu_masuk, $waktu_keluar);

    if ($stmt_insert->execute()) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://192.168.1.16/gerakkan_servo");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "MASUK");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response === false) {
            error_log("Error mengirim permintaan ke servo: " . curl_error($ch));
        }
        header("Location: index.php");
        exit(); 
    } else {
        $status_message = "Error: " . $stmt_insert->error;
    }

    $stmt_insert->close();
}


?>