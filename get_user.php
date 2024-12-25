<?php
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
        // Cek apakah UID ada di tabel rfid_users
        $stmt = $conn->prepare("SELECT * FROM rfid_users WHERE uid = ?");
        $stmt->bind_param("s", $uid);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Jika UID ditemukan, simpan log pemindaian ke tabel rfid_logs
            $stmt_insert = $conn->prepare("INSERT INTO rfid_logs (uid) VALUES (?)");
            $stmt_insert->bind_param("s", $uid);
            if ($stmt_insert->execute()) {
                $status_message = "Scan berhasil disimpan!";
            } else {
                $status_message = "Error: " . $stmt_insert->error;
            }
            $stmt_insert->close();
        } else {
            // Jika UID tidak ditemukan, beri pesan bahwa UID tidak terdaftar
            $status_message = "UID tidak terdaftar!";
        }

        $stmt->close();
    } else {
        $status_message = "UID tidak valid!";
    }
}

// Query untuk mendapatkan data scan dan identitas pengguna
$sql = "SELECT rfid_logs.id, rfid_users.nama, rfid_users.nim, rfid_logs.scan_time
        FROM rfid_logs
        LEFT JOIN rfid_users ON rfid_logs.uid = rfid_users.uid
        ORDER BY rfid_logs.id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data RFID</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            text-align: center;
        }
        table {
            width: 80%;
            border-collapse: collapse;
            margin: 20px auto;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #f4f4f4;
        }
        .status {
            margin-bottom: 20px;
            color: red;
        }
    </style>
</head>
<body>
    <h2>Data RFID Tersimpan</h2>

    <?php if ($status_message): ?>
        <p class="status"><?php echo $status_message; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="text" name="uid" placeholder="Masukkan UID RFID" required>
        <button type="submit">Simpan UID</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>NIM</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['nama'] ? $row['nama'] : "Tidak Terdaftar"; ?></td>
                        <td><?php echo $row['nim'] ? $row['nim'] : "Tidak Terdaftar"; ?></td>
                        <td><?php echo $row['scan_time']; ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">Tidak ada data</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>

<?php
// Tutup koneksi
$conn->close();
?>

