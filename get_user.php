<?php
date_default_timezone_set('Asia/Jakarta'); // Atur waktu zona asia

// Konfigurasi database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "rfid_db";
// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Pesan untuk status
$status_message = "";
// Periksa apakah data UID dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['uid'])) {
    $uid = $_POST['uid'];

    // Validasi UID
    if (!empty($uid) && strlen($uid) <= 20) {
        // Cek apakah ada entri UID yang masih berstatus MASUK
        $stmt_check = $conn->prepare("SELECT id, waktu_masuk FROM rfid_logs WHERE uid = ? AND status = 'MASUK'");
        $stmt_check->bind_param("s", $uid);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            // Jika ada UID dengan status MASUK, ubah menjadi KELUAR
            $row = $result_check->fetch_assoc();
            $log_id = $row['id'];
            $waktu_keluar = date("Y-m-d H:i:s");

            $stmt_update = $conn->prepare("UPDATE rfid_logs SET status = 'KELUAR', waktu_keluar = ? WHERE id = ?");
            $stmt_update->bind_param("si", $waktu_keluar, $log_id);

            if ($stmt_update->execute()) {
                echo "KELUAR"; // Berhasil mengubah status menjadi KELUAR
            } else {
                echo "Error: " . $stmt_update->error; // Jika terjadi kesalahan
            }

            $stmt_update->close();
        } else {
            // Cek apakah UID ada di tabel rfid_users
            $stmt = $conn->prepare("SELECT * FROM rfid_users WHERE uid = ?");
            $stmt->bind_param("s", $uid);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Jika UID ditemukan, simpan log pemindaian ke tabel rfid_logs
                $stmt_insert = $conn->prepare("INSERT INTO rfid_logs (uid, status, waktu_masuk) VALUES (?, 'MASUK', ?)");
                $waktu_masuk = date("Y-m-d H:i:s");
                $stmt_insert->bind_param("ss", $uid, $waktu_masuk);

                if ($stmt_insert->execute()) {
                    echo "MASUK"; // Berhasil menyimpan status MASUK
                } else {
                    echo "Error: " . $stmt_insert->error; // Jika terjadi kesalahan
                }

                $stmt_insert->close();
            } else {
                // Jika UID tidak ditemukan di tabel rfid_users
                echo "UID tidak terdaftar!";
            }

            $stmt->close();
        }

        $stmt_check->close();
    } else {
        echo "UID tidak valid!";
    }
}


// Query untuk mendapatkan data scan dan identitas pengguna
$sql = "SELECT rfid_logs.id, rfid_users.nama, rfid_users.nim, rfid_logs.scan_time
        FROM rfid_logs
        LEFT JOIN rfid_users ON rfid_logs.uid = rfid_users.uid
        ORDER BY rfid_logs.id DESC";
$result = $conn->query($sql);
?>
<?php
// Tutup koneksi
$conn->close();
?>

