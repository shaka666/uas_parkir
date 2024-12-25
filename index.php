<?php
date_default_timezone_set('Asia/Jakarta'); // Atur waktu zona asia

// Start PHP session untuk menginisialisasi sesi. Penyimpanan dan pengelolaan data antar halaman dengan mudah
session_start();


// Mengambil nilai dari session, jika ada. Kalau username ada maka akan tampil, jika tidak ada username maka jadi guest.
$Username = isset($_SESSION['Username']) ? $_SESSION['Username'] : 'Guest'; // Default 'Guest' jika tidak login
$UserPhoto = isset($_SESSION['UserPhoto']) ? $_SESSION['UserPhoto'] : 'default-profile.jpg'; // Foto pengguna, default jika tidak ada
$servername = "localhost"; // Server local host karena server satu komputer dengan program. Kalau program dan server beda komputer, localhost dapat diganti dengan ip server komputer yang terhubung.
$username = "root"; // Nama server
$password = ""; // Password server. Apabila username dan passoword diubah maka untuk bisa mengakses database harus memasukan username dan password yang sudah dibuat.
$dbname = "rfid_db"; // rfid db adalah databas  e yang ada di server. maka dari itu saya bisa mengakses rfid db. jika ada selain rfid db maka tinggal ubah saja rfid db dengan db lainnya.

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname); // $Conn untuk mengubungkan SQL ke program agar bisa mengakses servername, username, passsword dan db.
// Jika berhasil terhubung maka fungsi seperti select, update, insert dan delete dapat digunakan



// Query untuk mendapatkan data scan dan identitas pengguna
$records_per_page = 3;

// Menentukan halaman saat ini
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page - 1) * $records_per_page;

// Query untuk mengambil data dengan LIMIT berdasarkan halaman
$sql = "SELECT rfid_logs.id, rfid_users.nama, rfid_users.nim, rfid_logs.scan_time, rfid_logs.status, 
        rfid_logs.nim as nimLog, rfid_logs.waktu_keluar, rfid_logs.plat, rfid_logs.jenis_kendaraan, rfid_logs.tarif, rfid_logs.waktu_masuk
        FROM rfid_logs
        LEFT JOIN rfid_users ON rfid_logs.uid = rfid_users.uid
        ORDER BY rfid_logs.id DESC
        LIMIT $start_from, $records_per_page";

$result = $conn->query($sql);

// Query untuk menghitung jumlah total data
$sql_count = "SELECT COUNT(*) AS total FROM rfid_logs";
$count_result = $conn->query($sql_count);
$count_row = $count_result->fetch_assoc();
$total_records = $count_row['total'];
$total_pages = ceil($total_records / $records_per_page);

$slot = 100;

// Query untuk menghitung total data
$sql_count = "SELECT COUNT(*) AS total FROM rfid_logs WHERE status = 'MASUK'";
$count_result = $conn->query($sql_count);
$count_row = $count_result->fetch_assoc();
$total = $count_row['total'];

// Query untuk menghitung total data dengan status "KELUAR"
$sql_count_keluar = "SELECT COUNT(*) AS total_keluar FROM rfid_logs WHERE status = 'KELUAR'";
$count_keluar_result = $conn->query($sql_count_keluar);
$count_keluar_row = $count_keluar_result->fetch_assoc();
$total_keluar = $count_keluar_row['total_keluar'];

// Menghitung slot tersisa
$total_slots = $slot - $total;

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

        .main-content {
            display: flex;
            justify-content: space-between;
            margin: 20px;
        }

        .form-container {
            width: 45%;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-container input, .form-container button {
            width: calc(100% - 22px);
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .slot-container {
            width: 45%;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
        }

        .slot-item {
            text-align: center;
            width: 30%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f8f8ff;
        }

        .slot-item h3 {
            margin-bottom: 10px;
        }

        .slot-item p {
            font-size: 2em;
            font-weight: bold;
            color: #0d6efd;
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
            <img src="images/fotosaya.jpg" <?php echo htmlspecialchars($UserPhoto); ?>" alt="User Photo">
            <!-- Pesan Welcome -->
            <div>Welcome, <?php echo htmlspecialchars($Username); ?>!</div>
            <!-- Ikon My Account dan Logout -->
            <i class="fas fa-sign-out-alt" title="Logout" onclick="window.location.href='logout.php';"></i>
        </div>    
    <div>
        <a href="./data.php">data</a>
    </div>
    <div>SUMARDI UNIVERSITY</div>
        
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Form Masukan Kendaraan -->
        <div class="form-container">
            <h2>Masukan Kendaraan</h2>
            <form method="POST" action="tambah_kendaraan.php">
                <input type="text" name="nim" placeholder="Masukan NIM" required>
                <input type="text" name="plat" placeholder="Masukan Plat Nomor" required>

                <div class="input-group">
                    <label>Jenis Kendaraan</label>
                    <div>
                        <input type="checkbox" name="vehicle[]" value="MOTOR"> Motor
                        <input type="checkbox" name="vehicle[]" value="MOBIL"> Mobil
                    </div>
                </div>

                <div class="input-group">
                    <label>Waktu Masuk</label>
                    <input type="datetime-local" name="waktu_masuk" placeholder="Masukkan Waktu Masuk" value="">
                    <!-- Value kosong, jika tidak diisi, default akan digunakan -->
                </div>

                <button type="submit">Submit</button>
            </form>


        </div>

        <!-- Slot Kendaraan -->
        <div class="slot-container">
            <div class="slot-item">
                <h3>Total Slot</h3>
                <p id="total-slots"><?php echo $total_slots; ?></p>
                </div>
            <div class="slot-item">
                <h3>Masuk</h3>
                <p id="slot-in-count">
                    <?= $total ?>
                </p>
            </div>
            <div class="slot-item">
                <h3>Keluar</h3>
                <p id="slot-out-count">
                    <?= $total_keluar?>
                </p>
            </div>
        </div>
    </div>

    <!-- Tabel Data Kendaraan -->
    <div class="table-container">
        <h2>Data Kendaraan</h2>
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
                                <form method="POST" action="ubah_status.php">
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
                echo '<a href="?page=' . ($page - 1) . '">Previous</a>';
            }

            for ($i = 1; $i <= $total_pages; $i++) {
                echo '<a href="?page=' . $i . '"';
                if ($i == $page) {
                    echo ' class="active"';
                }
                echo '>' . $i . '</a>';
            }

            if ($page < $total_pages) {
                echo '<a href="?page=' . ($page + 1) . '">Next</a>';
            }
            ?>
        </div>
</div>


    <!-- <script>
        let totalSlots = 100; 
        let masukCount = 0; 
        let keluarCount = 0; 

        function updateSlotCountsAndData() {
            fetch('get_user.php')
                .then(response => response.json())
                .then(data => {
                    if (data.uid && data.status) {
                        // Update slot
                        if (data.status === 'IN' && totalSlots > 0) {
                            masukCount++;
                            totalSlots--;
                        } else if (data.status === 'OUT' && masukCount > 0) {
                            keluarCount++;
                            totalSlots++;
                        }
                        document.getElementById('slot-in-count').textContent = masukCount;
                        document.getElementById('slot-out-count').textContent = keluarCount;
                        document.getElementById('total-slots').textContent = totalSlots;

                        fetch('get_vehicle_logs.php') 
                            .then(response => response.json())
                            .then(logData => {
                                const tableBody = document.getElementById('vehicle-data');
                                tableBody.innerHTML = ""; 

                                logData.forEach((log, index) => {
                                    const row = `<tr>
                                        <td>${index + 1}</td>
                                        <td>${log.nama || 'Tidak Terdaftar'}</td>
                                        <td>${log.nim || 'Tidak Terdaftar'}</td>
                                        <td>${log.jenis_kendaraan}</td>
                                        <td>${log.waktu}</td>
                                    </tr>`;
                                    tableBody.innerHTML += row;
                                });
                            });
                    }
                })
                .catch(error => console.error('Error fetching data:', error));
        }

        setInterval(updateSlotCountsAndData, 5000);

        function validateCheckbox() {
            const checkboxes = document.querySelectorAll('input[name="vehicle[]"]:checked');
            if (checkboxes.length === 0) {
                alert("Harap pilih jenis kendaraan!");
                return false;
            }
            return true;
        }
    </script> -->
</body>
</html>

