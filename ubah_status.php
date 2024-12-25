<?php 
date_default_timezone_set('Asia/Jakarta'); // Atur waktu zona asia
$servername = "localhost"; // Server local host karena server satu komputer dengan program. Kalau program dan server beda komputer, localhost dapat diganti dengan ip server komputer yang terhubung.
$username = "root"; // Nama server
$password = ""; // Password server. Apabila username dan passoword diubah maka untuk bisa mengakses database harus memasukan username dan password yang sudah dibuat.
$dbname = "rfid_db"; // rfid db adalah database yang ada di server. maka dari itu saya bisa mengakses rfid db. jika ada selain rfid db maka tinggal ubah saja rfid db dengan db lainnya.

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname); // $Conn untuk mengubungkan SQL ke program agar bisa mengakses servername, username, passsword dan db.
// Jika berhasil terhubung maka fungsi seperti select, update, insert dan delete dapat digunakan

$status_message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];
    $log_id = $_POST['log_id'];

    // Tentukan status baru
    $new_status = ($action === "MASUK") ? "KELUAR" : "MASUK";

    // Jika aksi adalah "Keluar", set waktu_keluar ke waktu saat ini
    $waktu_keluar = null;
    if ($new_status === "KELUAR") {
        $waktu_keluar = date("Y-m-d H:i:s");  // Waktu keluar saat ini
    }

    // Update status dan waktu_keluar (jika ada) di tabel rfid_logs
    $stmt_update = $conn->prepare("UPDATE rfid_logs SET status = ?, waktu_keluar = ? WHERE id = ?");
    $stmt_update->bind_param("ssi", $new_status, $waktu_keluar, $log_id);

    if ($stmt_update->execute()) {
        // Dapatkan data waktu_masuk dan waktu_keluar setelah update
        $stmt_get = $conn->prepare("SELECT waktu_masuk, waktu_keluar, uid FROM rfid_logs WHERE id = ?");
        $stmt_get->bind_param("i", $log_id);
        $stmt_get->execute();
        $result = $stmt_get->get_result();
        $row = $result->fetch_assoc();

        $waktu_masuk = new DateTime($row['waktu_masuk']);
        
        // Jika waktu_keluar kosong (masih di status MASUK), maka skip tarif
        if ($row['waktu_keluar'] !== null) {
            $waktu_keluar = new DateTime($row['waktu_keluar']);
            $durasi = $waktu_masuk->diff($waktu_keluar);
            
            // Hitung tarif jika tidak ada UID
            $tarif = 0;
            if (empty($row['uid'])) {  // Tidak ada UID
                $jam = $durasi->h + ($durasi->days * 24); // Hitung total jam durasi

                if ($jam < 2) {
                    $tarif = 2000; // Kurang dari 2 jam = 2 ribu
                } elseif ($jam < 4) {
                    $tarif = 4000; // Kurang dari 4 jam = 4 ribu
                } else {
                    $tarif = ceil($jam / 2) * 2000; // Setiap 2 jam = 2 ribu
                }
            }

            // Simpan tarif ke tabel (misalnya, ada kolom 'tarif' dalam tabel)
            $stmt_tarif = $conn->prepare("UPDATE rfid_logs SET tarif = ? WHERE id = ?");
            $stmt_tarif->bind_param("ii", $tarif, $log_id);
            $stmt_tarif->execute();

            $status_message = "Status berhasil diubah menjadi $new_status! Tarif: Rp $tarif";
        }

        header("Location: index.php");
        exit();
    } else {
        $status_message = "Error: " . $stmt_update->error;
    }

    $stmt_update->close();
}

?>