<?php
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'goodbooks';
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$result = $conn->query("SELECT * FROM pelanggan");

$bankRes = $conn->query("SELECT * FROM bank");
$banks = [];
while ($b = $bankRes->fetch_assoc()) {
  $banks[$b['ID_Bank']] = $b['NamaBank'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Data Pelanggan</title>
  <link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color: #f2f2f2;">
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: blueviolet;">
  <div class="container">
    <a class="navbar-brand" href="#">GoodBooks Admin</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="Admin.php">Edit Metadata</a></li>
        <li class="nav-item"><a class="nav-link" href="DataBuku.php">Edit Data Buku</a></li>
         <li class="nav-item"><a class="nav-link" href="DataLaporan.php">Laporan Transaksi</a></li>
        <li class="nav-item"><a class="nav-link active" href="DataPelanggan.php">Edit Data Pelanggan</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container py-5">
  <h2 class="mb-4">Edit Data Pelanggan</h2>
  <div class="table-responsive">
    <table class="table table-bordered table-striped align-middle">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Bank</th>
          <th>Nama Depan</th>
          <th>Nama Belakang</th>
          <th>No. Telp</th>
          <th>Email</th>
          <th>Password</th>
          <th>No Rek</th>
          <th>Tanggal Registrasi</th>
          <th>Saldo</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <form action="update_pelanggan.php" method="POST">
            <td><input type="text" name="ID_Pelanggan" value="<?= $row['ID_Pelanggan'] ?>" readonly class="form-control-plaintext" style="width: 60px;"></td>
            <td>
              <select name="ID_Bank" class="form-select form-select-sm">
                <?php foreach ($banks as $id => $name): ?>
                  <option value="<?= $id ?>" <?= $id == $row['ID_Bank'] ? 'selected' : '' ?>><?= $name ?></option>
                <?php endforeach; ?>
              </select>
            </td>
            <td><input type="text" name="NamaDepan" value="<?= $row['NamaDepan'] ?>" class="form-control form-control-sm"></td>
            <td><input type="text" name="NamaBelakang" value="<?= $row['NamaBelakang'] ?>" class="form-control form-control-sm"></td>
            <td><input type="text" name="NoTelp" value="<?= $row['NoTelp'] ?>" class="form-control form-control-sm"></td>
            <td><input type="email" name="Email" value="<?= $row['Email'] ?>" class="form-control form-control-sm"></td>
            <td><input type="text" name="Password" value="<?= $row['Password'] ?>" class="form-control form-control-sm"></td>
            <td><input type="text" name="NoRek" value="<?= $row['NoRek'] ?>" class="form-control form-control-sm"></td>
            <td><input type="text" name="TglRegis" value="<?= $row['TglRegis'] ?>" class="form-control form-control-sm"></td>
            <td><input type="text" name="Saldo" value="<?= $row['Saldo'] ?>" class="form-control form-control-sm"></td>
            <td class="d-flex gap-1">
  <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
</form>
<form action="delete.php" method="POST" onsubmit="return confirm('Yakin ingin menghapus akun ini?');">
  <input type="hidden" name="table" value="pelanggan">
  <input type="hidden" name="id" value="<?= $row['ID_Pelanggan'] ?>">
  <input type="hidden" name="column" value="ID_Pelanggan">
  <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
</form>
</td>

          </form>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
