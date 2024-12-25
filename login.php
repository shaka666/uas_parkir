<?php
include('config.php');

// Cek apakah sudah login
if (isset($_SESSION['Username'])) {
    // Jika pengguna sudah login, arahkan ke halaman utama
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

    $inputUsername = $_POST['Username'];
    $inputPassword = $_POST['Password'];

    // Query untuk mengambil password yang ter-hash dari database
    $stmt = $conn->prepare("SELECT id, Username, Password FROM admin WHERE Username = ?");
    $stmt->bind_param("s", $inputUsername);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Verifikasi password
        if ($inputPassword === $user['Password']) {
            $_SESSION['Username'] = $user['Username'];
            header("Location: index.php");
            exit();
        } else {
            $error = "Username atau Password salah";
        }
        
    } else {
        $error = "Username tidak ditemukan";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>

        .navbar-text {
            color: white;
            font-size: 1.2em;
            margin-left: auto;
        }

        .navbar-brand-box {
            padding: 5px 15px;
            border: 2px solid white;
            border-radius: 10px;
            background-color: white;
            color:rgb(0, 34, 255);
            font-weight: bold;
            display: inline-block;
        }
        .navbar-brand-box span {
            color: red;
        }

        body {
            background-color: #F8F8FF !important; /* Ganti dengan warna yang diinginkan */
            font-family: Arial, sans-serif;
            margin: 0;
            height: 100%;
        }

        /* Mengatur body agar mengambil seluruh tinggi layar*/ 
        body, html {
            height: 100%;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        /* Menggunakan flexbox untuk memusatkan konten*/
        .container {
            display: flex;
            justify-content: center; /* Memusatkan secara horizontal */
            align-items: center;     /* Memusatkan secara vertikal */
            height: 100%;
        }

        .login-form {
            text-align: center;
            padding: 20px;
            border: 2px solid #000000;
            border-radius: 8px;
            width: 350px;
            box-shadow: 0 4px 8px rgba(238, 12, 12, 0.1);
            background-color: #F5F5F5;
        }   

        .form-group {
            position: relative;
            margin-bottom: 20px;
        }

        .form-group label {
            position: absolute;
            top: 50%;
            left: 10px;
            transform: translateY(-50%);
            font-size: 16px;
            color: #aaa;
            transition: all 0,3s ease;
            pointer-events: none;

        }

        .form-group input {
            width: 100%; /* Menjadikan input selebar kotak */
            padding: 10px; /* Memberikan ruang dalam input */
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            outline: none;
            background-color: transparent;
        }

        .form-group input:focus + label,
        .form-group input:not(:placeholder-shown) + label {
            top: -10px;
            left: 10px;
            font-size: 12px;
            color:black
        }

form button {
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    background-color: #007bff;
    color: white;
    font-size: 16px;
    cursor: pointer;
}

form button:hover {
    background-color: #0056b3;
}
    </style>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <script src="js/bootstrap.bundle.min.js"></script>
</head>
<body>
<nav class="navbar navbar-primary bg-primary" style="border: 1px solid black;">
  <div class="container-fluid">
    <div style="display: flex; align-items:center">
    <a class="navbar-brand" href="#" style="display: flex; align-items:center;">
      <img src="images/parkir.png" alt="" width="50" height="50" class="me-3">
      <div class="navbar-brand-box">
      Parkir<span>KUY!</span>
      </div>
    </a>
    </div>
    <div class="navbar-text">
    <img src="images/uslogo.jpg" alt="" width="50" height="50" class="me-3">
            SUMARDI UNIVERSITY
        </div>
</nav>
    <div class="container">
        <div class="login-form">
            <h2>Login</h2>
        
            <!-- Menampilkan error jika ada -->
            <?php if (isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>
        
            <form method="POST" action="login.php">
                <div class="form-group">
                <input type="text" id="Username" name="Username" placeholder=" " required>
                <label for="Username">Username</label>
            </div>
            <div class="form-group">
            <input type="password" id="Password" name="Password" placeholder=" " required>
            <label for="Password">Password</label>
        </div>
                
                <button type="submit">Login</button>
            </form>
        </div>
    </div>
</body>
</html>

