<?php
date_default_timezone_set('Asia/Jakarta');

// Start PHP session untuk menginisialisasi sesi. Penyimpanan dan pengelolaan data antar halaman dengan mudah
session_start();

// Mengambil nilai dari session, jika ada. Kalau username ada maka akan tampil, jika tidak ada username maka jadi guest.
$Username = isset($_SESSION['Username']) ? $_SESSION['Username'] : 'Guest'; // Default 'Guest' jika tidak login
$UserPhoto = isset($_SESSION['UserPhoto']) ? $_SESSION['UserPhoto'] : 'default-profile.jpg'; // Foto pengguna, default jika tidak ada
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "rfid_db";

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Mengambil nilai dari form pencarian, jika ada, dan trim untuk menghapus spasi
$search_nim = isset($_GET['search_nim']) ? trim($_GET['search_nim']) : '';

// Menghitung offset dan page untuk pagination
$records_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page - 1) * $records_per_page;

// Query untuk mengambil data dengan pencarian jika ada
$sql = "SELECT rfid_logs.id, rfid_users.nama, rfid_users.nim, rfid_logs.scan_time, rfid_logs.status, 
        rfid_logs.nim as nimLog, rfid_logs.waktu_keluar, rfid_logs.plat, rfid_logs.jenis_kendaraan, rfid_logs.tarif, rfid_logs.waktu_masuk
        FROM rfid_logs
        LEFT JOIN rfid_users ON rfid_logs.uid = rfid_users.uid";

// Jika ada pencarian berdasarkan NIM
if ($search_nim != '') {
    $sql .= " WHERE rfid_users.nim LIKE '%$search_nim%' OR rfid_logs.nim LIKE '%$search_nim%'";
}

$sql .= " ORDER BY rfid_logs.id DESC LIMIT $start_from, $records_per_page";

// Menjalankan query untuk mengambil data
$result = $conn->query($sql);

// Query untuk menghitung jumlah total data setelah pencarian
$sql_count = "SELECT COUNT(*) AS total FROM rfid_logs
              LEFT JOIN rfid_users ON rfid_logs.uid = rfid_users.uid";
if ($search_nim != '') {
    $sql_count .= " WHERE rfid_users.nim LIKE '%$search_nim%' OR rfid_logs.nim LIKE '%$search_nim%'";
}
$count_result = $conn->query($sql_count);
$count_row = $count_result->fetch_assoc();
$total_records = $count_row['total'];
$total_pages = ceil($total_records / $records_per_page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Font Awesome untuk ikon -->
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #F8F8FF;
        }

        .navbar-main {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #0d6efd;
            padding: 15px 20px;
            color: white;
        }

        .navbar-main .user-info {
            display: flex;
            align-items: center;
        }

        .navbar-main img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .navbar-main .user-info div {
            margin-right: 20px;
        }

        .navbar-main .user-info i {
            margin-left: 10px;
            cursor: pointer;
        }

        .table-container {
            margin: 20px auto;
            width: 90%;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .table-container table {
            width: 100%;
            border-collapse: collapse;
        }

        .table-container th, .table-container td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        .table-container th {
            background-color: #f4f4f4;
        }

        .pagination {
            text-align: center;
            margin-top: 20px;
        }

        .pagination a {
            padding: 8px 16px;
            margin: 0 5px;
            text-decoration: none;
            border: 1px solid #ddd;
            color: #333;
        }

        .pagination a:hover {
            background-color: #f0f0f0;
        }

        .pagination a.active {
            font-weight: bold;
            background-color: #4CAF50;
            color: white;
        }

        /* Styling for Search Bar */
        form {
            margin-bottom: 20px;
            text-align: center;
        }

        input[type="text"] {
            padding: 8px;
            width: 200px;
            font-size: 16px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        button {
            padding: 8px 16px;
            font-size: 16px;
            background-color: #0d6efd;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }
        button:disabled {
            background-color: #cccccc; /* Grey background for disabled state */
            color: #888888; /* Grey text for disabled state */
            cursor: not-allowed; /* Change the cursor to indicate that it's not clickable */
        }

        button:disabled:hover {
            background-color: #cccccc; /* No hover effect when disabled */
        }

        
    </style>
</head>
<body>
    <nav class="navbar-main">
        <div class="user-info">
            <!-- Foto Pengguna -->
            <img src="images/fotosaya.jpg" alt="User Photo">
            <!-- Pesan Welcome -->
            <div>Welcome, <?php echo htmlspecialchars($Username); ?>!</div>
            <!-- Ikon My Account dan Logout -->
            <i class="fas fa-sign-out-alt" title="Logout" onclick="window.location.href='logout.php';"></i>
        </div>    
        <div>
            <a href="./index.php">index</a>
        </div>
        <div>SUMARDI UNIVERSITY</div>
    </nav>

    <div class="table-container">
        <h2>Data Kendaraan</h2>

        <!-- Form Pencarian -->
        <form method="GET" action="">
            <input type="text" name="search_nim" placeholder="Cari berdasarkan NIM" value="<?php echo isset($_GET['search_nim']) ? htmlspecialchars($_GET['search_nim']) : ''; ?>">
            <button type="submit">Cari</button>
        </form>

        <table>
            <tr>
                <th>Nama</th>
                <th>NIM</th>
                <th>Tarif</th>
                <th>Kendaraan</th>
                <th>Status</th>
                <th>Masuk</th>
                <th>Keluar</th>
                <th>Aksi</th>
            </tr>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['nama'] ? $row['nama'] : $row['plat']; ?></td>
                            <td><?php echo $row['nim'] ? $row['nim'] : $row['nimLog']; ?></td>
                            <td><?php echo $row['tarif']; ?></td>
                            <td><?php echo $row['jenis_kendaraan']; ?></td>
                            <td><?php echo $row['status']; ?></td>
                            <td><?php echo $row['waktu_masuk'] ? $row['waktu_masuk'] : $row['scan_time']; ?></td>
                            <td><?php echo $row['waktu_keluar']; ?></td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="log_id" value="<?php echo $row['id']; ?>">
                                    <input type="hidden" name="action" value="<?php echo $row['status']; ?>">
                                    <button type="submit" <?php echo (!empty($row['waktu_keluar'])) ? 'disabled' : ''; ?>>
                                        <?php echo ($row['status'] == "MASUK") ? "Keluar" : "Masuk"; ?>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">Tidak ada data</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Pagination links -->
        <div class="pagination">
            <?php
            if ($page > 1) {
                echo '<a href="?page=' . ($page - 1) . '&search_nim=' . htmlspecialchars($search_nim) . '">Previous</a>';
            }

            for ($i = 1; $i <= $total_pages; $i++) {
                echo '<a href="?page=' . $i . '&search_nim=' . htmlspecialchars($search_nim) . '"';
                if ($i == $page) {
                    echo ' class="active"';
                }
                echo '>' . $i . '</a>';
            }

            if ($page < $total_pages) {
                echo '<a href="?page=' . ($page + 1) . '&search_nim=' . htmlspecialchars($search_nim) . '">Next</a>';
            }
            ?>
        </div>
    </div>

</body>
</html>
