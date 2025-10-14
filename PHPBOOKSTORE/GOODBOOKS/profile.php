<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login_register.php");
    exit;
}

$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'goodbooks';
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $id_bank = $_POST['ID_Bank'];
    $nama_depan = $_POST['NamaDepan'];
    $nama_belakang = $_POST['NamaBelakang'];
    $notelp = $_POST['NoTelp'];
    $email = $_POST['Email'];
    $norek = $_POST['NoRek'];

    $stmt = $conn->prepare("UPDATE pelanggan SET ID_Bank = ?, NamaDepan = ?, NamaBelakang = ?, NoTelp = ?, Email = ?, NoRek = ? WHERE ID_Pelanggan = ?");
    $stmt->bind_param("isssssi", $id_bank, $nama_depan, $nama_belakang, $notelp, $email, $norek, $user_id);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Data berhasil diperbarui!');</script>";
}

$stmt = $conn->prepare("SELECT ID_Bank, NamaDepan, NamaBelakang, NoTelp, Email, NoRek, Saldo FROM pelanggan WHERE ID_Pelanggan = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($id_bank, $nama_depan, $nama_belakang, $notelp, $email, $norek, $saldo);
$stmt->fetch();
$stmt->close();

$banks = $conn->query("SELECT ID_Bank, NamaBank FROM bank");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Your Profile - GoodBooks</title>
  <link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

  <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: blueviolet;">
    <div class="container">
        <a class="navbar-brand" href="#" style="margin-right: 50px;">
  <img src="img/book-icon-134.png" width="50" height="50" alt="Book Icon" >
  <span style="font-weight: bold; font-size: 1 rem; color: white;">GoodBooks</span>
</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link" href="GoodBooks.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="Search.php">Search</a></li>
          <li class="nav-item"><a class="nav-link" href="KatalogBuku.php">All Books</a></li>
          <?php if (isset($_SESSION['user_id'])): ?>
    <li class="nav-item">
        <a class="nav-link active" href="profile.php">Your Profile</a>
    </li>
<?php else: ?>
    <li class="nav-item">
        <a class="nav-link" href="login_register.php">Login / Register</a>
    </li>
<?php endif; ?>
          <li class="nav-item"><a class="nav-link" href="Contact.php">Contact</a></li>
        </ul>
      </div>
    </div>
  </nav>

<div class="container py-5">
  <h2 class="mb-4">Your Profile</h2>
  <form method="POST">
    <div class="mb-3">
      <label>Nama Depan</label>
      <input type="text" name="NamaDepan" class="form-control" value="<?= htmlspecialchars($nama_depan) ?>" required>
    </div>
    <div class="mb-3">
      <label>Nama Belakang</label>
      <input type="text" name="NamaBelakang" class="form-control" value="<?= htmlspecialchars($nama_belakang) ?>" required>
    </div>
    <div class="mb-3">
      <label>No Telp</label>
      <input type="text" name="NoTelp" class="form-control" value="<?= htmlspecialchars($notelp) ?>" required>
    </div>
    <div class="mb-3">
      <label>Email</label>
      <input type="email" name="Email" class="form-control" value="<?= htmlspecialchars($email) ?>" required>
    </div>
    <div class="mb-3">
      <label>No Rekening</label>
      <input type="text" name="NoRek" class="form-control" value="<?= htmlspecialchars($norek) ?>" required>
    </div>
    <div class="mb-3">
      <label>Bank</label>
      <select name="ID_Bank" class="form-control" required>
        <?php while ($row = $banks->fetch_assoc()): ?>
          <option value="<?= $row['ID_Bank'] ?>" <?= $row['ID_Bank'] == $id_bank ? 'selected' : '' ?>>
            <?= htmlspecialchars($row['NamaBank']) ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="alert alert-info">
  <strong>Saldo Anda:</strong> Rp <?= number_format($saldo, 0, ',', '.') ?>
</div>
    <hr class="my-4">
    <button type="submit" name="save" class="btn btn-primary">Save</button>
    <a href="logout.php" class="btn btn-danger ms-2">Logout</a>
    <a href="GoodBooks.php" class="btn btn-green ms-2">Kembali</a>
  </form>

  <hr class="my-5">
<h3>Daftar Buku yang Telah Dibeli</h3>

<?php
$stmt = $conn->prepare("
  SELECT b.DataBuku, b.Judul, b.Harga, p.TanggalPembayaran
  FROM pesanan ps
  JOIN databuku b ON ps.DataBuku = b.DataBuku
  JOIN pembayaran p ON ps.ID_Pembayaran = p.ID_Pembayaran
  WHERE ps.ID_Pelanggan = ?
  ORDER BY p.TanggalPembayaran DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0):
?>
  <div class="table-responsive">
    <table class="table table-bordered mt-3">
      <thead class="table-light">
        <tr>
          <th>Judul Buku</th>
          <th>Harga</th>
          <th>Tanggal Pembayaran</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><a href="book.php?id=<?= $row['DataBuku'] ?>"><?= htmlspecialchars($row['Judul']) ?></a></td>
            <td>Rp <?= number_format($row['Harga'], 0, ',', '.') ?></td>
            <td><?= htmlspecialchars($row['TanggalPembayaran']) ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
<?php else: ?>
  <p class="text-muted mt-3">Kamu belum membeli buku apapun.</p>
<?php endif;

$stmt->close();
?>


</div>
</body>
</html>

<?php $conn->close(); ?>
