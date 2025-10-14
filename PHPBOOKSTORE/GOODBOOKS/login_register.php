<?php
session_start();
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'goodbooks';
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$next_id = 1;
$result = $conn->query("SELECT MAX(ID_Pelanggan) AS max_id FROM pelanggan");
if ($row = $result->fetch_assoc()) {
    $next_id = $row['max_id'] + 1;
}

// Register
if (isset($_POST['register'])) {
    $id_bank = $_POST['ID_Bank'];
    $nama_depan = $_POST['NamaDepan'];
    $nama_belakang = $_POST['NamaBelakang'];
    $notelp = $_POST['NoTelp'];
    $email = $_POST['Email'];
    $password = $_POST['Password'];
    $norek = $_POST['NoRek'];
    $tglregis = date('Y-m-d');

    // Cek apa email udah daftar
    $check = $conn->prepare("SELECT ID_Pelanggan FROM pelanggan WHERE Email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "<script>alert('Email sudah terdaftar. Gunakan email lain.');</script>";
    } else {
        // Email belum daftar, lanjut regis
        $stmt = $conn->prepare("INSERT INTO pelanggan (ID_Pelanggan, ID_Bank, NamaDepan, NamaBelakang, NoTelp, Email, Password, NoRek, TglRegis) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisssssss", $next_id, $id_bank, $nama_depan, $nama_belakang, $notelp, $email, $password, $norek, $tglregis);
        $stmt->execute();
        $stmt->close();

        echo "<script>alert('Registrasi berhasil, silakan login.');</script>";
    }

    $check->close();
}


// Login
if (isset($_POST['login'])) {
    $email = trim($_POST['Email']);
    $password = trim($_POST['Password']);

    $stmt = $conn->prepare("SELECT ID_Pelanggan, Password FROM pelanggan WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($user_id, $stored_password);
        $stmt->fetch();

        if ($password === $stored_password) {
            $_SESSION['user_id'] = $user_id;
            header("Location: GoodBooks.php");
            exit;
        } else {
            echo "<script>alert('Login gagal. Password salah.');</script>";
        }
    } else {
        echo "<script>alert('Login gagal. Email tidak ditemukan.');</script>";
    }

    $stmt->close();
}


$banks = $conn->query("SELECT ID_Bank, NamaBank FROM bank");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login / Register - GoodBooks</title>
  <link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <div class="row">

    <!-- Login -->
    <div class="col-md-6">
      <h3>Login</h3>
      <form method="POST">
        <div class="mb-3">
          <label>Email</label>
          <input type="email" name="Email" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Password</label>
          <input type="password" name="Password" class="form-control" required>
        </div>
        <button type="submit" name="login" class="btn btn-primary">Login</button>
      </form>
    </div>

    <!-- Register -->
    <div class="col-md-6">
      <h3>Register</h3>
      <form method="POST">
        <div class="mb-3">
            <label>ID Pelanggan</label>
            <input type="text" name="ID_Pelanggan" class="form-control" value="<?= $next_id ?>" readonly>
        </div>
        <div class="mb-3">
          <label>Nama Depan</label>
          <input type="text" name="NamaDepan" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Nama Belakang</label>
          <input type="text" name="NamaBelakang" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>No Telp</label>
          <input type="text" name="NoTelp" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Email</label>
          <input type="email" name="Email" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Password</label>
          <input type="password" name="Password" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>No Rekening</label>
          <input type="text" name="NoRek" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Bank</label>
          <select name="ID_Bank" class="form-control" required>
            <option value="">Pilih Bank</option>
            <?php while ($row = $banks->fetch_assoc()): ?>
              <option value="<?= $row['ID_Bank'] ?>"><?= htmlspecialchars($row['NamaBank']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <button type="submit" name="register" class="btn btn-success">Register</button>
      </form>
    </div>
  </div>
</div>
</body>
</html>

<?php $conn->close(); ?>
