<?php
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'goodbooks';
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$sql = "
SELECT 
  pembayaran.ID_Pembayaran,
  pembayaran.ID_Pelanggan,
  pembayaran.TotalHarga,
  pembayaran.TanggalPembayaran,
  pesanan.ID_Pesanan,
  databuku.Judul,
  databuku.Harga,
  pesanan.HargaSatuan
FROM pembayaran
JOIN pesanan ON pembayaran.ID_Pembayaran = pesanan.ID_Pembayaran
JOIN databuku ON pesanan.DataBuku = databuku.DataBuku
ORDER BY pembayaran.TanggalPembayaran DESC
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Laporan Transaksi</title>
  <link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: blueviolet;">
  <div class="container">
    <a class="navbar-brand" href="#">GoodBooks Halaman Admin</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="Admin.php">Edit Metadata</a></li>
        <li class="nav-item"><a class="nav-link" href="DataBuku.php">Edit Data Buku</a></li>
        <li class="nav-item"><a class="nav-link active" href="DataLaporan.php">Laporan Transaksi</a></li>
        <li class="nav-item"><a class="nav-link" href="DataPelanggan.php">Edit Data Pelanggan</a></li>

      </ul>
    </div>
  </div>
</nav>

<div class="container py-5">
  <h2 class="mb-4">Laporan Transaksi</h2>
  <form method="post" action="export_laporan.php" class="mb-4">
  <button type="submit" class="btn btn-success">Export Transaksi ke CSV</button>
</form>
  <div class="table-responsive">
    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>ID Pembayaran</th>
          <th>ID Pelanggan</th>
          <th>Judul Buku</th>
          <th>Harga</th>
          <th>Tanggal Pembayaran</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $row['ID_Pembayaran'] ?></td>
          <td><?= $row['ID_Pelanggan'] ?></td>
          <td><?= htmlspecialchars($row['Judul']) ?></td>
          <td>Rp<?= number_format($row['HargaSatuan']) ?></td>
          <td><?= $row['TanggalPembayaran'] ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <hr class="my-5">

  <h3 class="mb-3">Rekap Jumlah Buku Terjual</h3>
  <?php
  // Semua buku yang ud dijual
  $summary = $conn->query("
    SELECT databuku.Judul, COUNT(*) AS JumlahTerjual
    FROM pesanan
    JOIN databuku ON pesanan.DataBuku = databuku.DataBuku
    GROUP BY databuku.Judul
    ORDER BY JumlahTerjual DESC
  ");
  ?>
  <table class="table table-bordered table-sm">
    <thead class="table-secondary">
      <tr>
        <th>Judul Buku</th>
        <th>Jumlah Terjual</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $summary->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($row['Judul']) ?></td>
        <td><?= $row['JumlahTerjual'] ?></td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
