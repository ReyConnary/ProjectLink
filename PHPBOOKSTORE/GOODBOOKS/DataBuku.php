<?php
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'goodbooks';
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

function getNextId($conn, $table, $column) {
  $res = $conn->query("SELECT MAX($column) AS max_id FROM $table");
  $row = $res->fetch_assoc();
  return $row['max_id'] + 1 ?: 1;
}

$nextBookID = getNextId($conn, 'databuku', 'DataBuku');

function Opsi($conn, $table, $idCol, $nameCol) {
  $result = $conn->query("SELECT $idCol, $nameCol FROM $table");
  $options = [];
  while ($row = $result->fetch_assoc()) {
    $options[] = $row;
  }
  return $options;
}

$franchises = Opsi($conn, 'franchise', 'ID_Franchise', 'NamaFranchise');
$kategoris = Opsi($conn, 'kategori', 'ID_Kategori', 'NamaKategori');
$penerbits = Opsi($conn, 'penerbit', 'ID_Penerbit', 'NamaPenerbit');
$penulis = Opsi($conn, 'penulis', 'ID_Penulis', 'NamaPenulis');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Halaman Data Buku</title>
  <link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: blueviolet;">
    <div class="container">
        <a class="navbar-brand" href="#" style="margin-right: 50px;">
  <span style="font-weight: bold; font-size: 1 rem; color: white;">GoodBooks Halaman Admin</span>
</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link" href="Admin.php">Edit Metadata</a></li>
          <li class="nav-item"><a class="nav-link active" href="DataBuku.php">Edit Data Buku</a></li>
          <li class="nav-item"><a class="nav-link" href="DataLaporan.php">Laporan Transaksi</a></li>
          <li class="nav-item"><a class="nav-link" href="DataPelanggan.php">Edit Data Pelanggan</a></li>

        </ul>
      </div>
    </div>
  </nav>

<div class="container py-5">
  <h2 class="mb-4">Tambah Data Buku</h2>
  <div class="card shadow">
    <div class="card-body">
      <form action="insert_book.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="DataBuku" value="<?= $nextBookID ?>">

        <div class="row g-3">

          <div class="col-md-6">
            <label class="form-label">Franchise</label>
            <select class="form-select" name="ID_Franchise" required>
              <option value="">Pilih Franchise</option>
              <?php foreach ($franchises as $f): ?>
                <option value="<?= $f['ID_Franchise'] ?>"><?= $f['NamaFranchise'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label">Kategori</label>
            <select class="form-select" name="ID_Kategori" required>
              <option value="">Pilih Kategori</option>
              <?php foreach ($kategoris as $k): ?>
                <option value="<?= $k['ID_Kategori'] ?>"><?= $k['NamaKategori'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label">Penerbit</label>
            <select class="form-select" name="ID_Penerbit" required>
              <option value="">Pilih Penerbit</option>
              <?php foreach ($penerbits as $p): ?>
                <option value="<?= $p['ID_Penerbit'] ?>"><?= $p['NamaPenerbit'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label">Penulis</label>
            <select class="form-select" name="ID_Penulis" required>
              <option value="">Pilih Penulis</option>
              <?php foreach ($penulis as $pen): ?>
                <option value="<?= $pen['ID_Penulis'] ?>"><?= $pen['NamaPenulis'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-6">
            <input type="text" class="form-control" name="Judul" placeholder="Judul Buku" required>
          </div>

          <div class="col-md-6">
            <input type="text" class="form-control" name="ISBN" placeholder="ISBN" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Input Tanggal Publikasi</label>
            <input type="date" class="form-control" name="TanggalPublikasi" placeholder="Tanggal Publikasi" required>
          </div>

          <div class="col-md-6">
            <input type="text" class="form-control" name="Bahasa" placeholder="Bahasa" required>
          </div>

          <div class="col-md-6">
            <input type="number" class="form-control" name="JumlahHalaman" placeholder="Jumlah Halaman" required>
          </div>

          <div class="col-md-6">
  <label class="form-label">Format Buku</label>
  <select class="form-select" name="Format" required>
    <option value="">Pilih Format</option>
    <option value="Manga">Manga</option>
    <option value="Comic">Comic</option>
    <option value="Novel">Novel</option>
  </select>
</div>


          <div class="col-12">
            <textarea class="form-control" name="Ringkasan" placeholder="Ringkasan" rows="3" required></textarea>
          </div>

          <div class="col-12">
            <label class="form-label">Upload Cover Buku (JPEG/JPG only)</label>
            <input type="file" class="form-control" name="CoverImg" accept="image/*" required>
          </div>

          <div class="col-12">
            <label class="form-label">Masukkan harga</label>
            <input type="number" class="form-control" name="Harga" placeholder="Harga" required>
          </div>
          <div class="col-12">
  <label class="form-label">Google Drive Link Buku</label>
  <input type="url" class="form-control" name="DriveLink" placeholder="https://drive.google.com/..." required>
</div>

        </div>

        <div class="text-center mt-4">
          <button type="submit" class="btn btn-success px-4">Simpan Data Buku</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Display Tabel Buku -->
  <?php
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$table = 'databuku';
$columns = [
  'DataBuku', 'ID_Franchise', 'ID_Kategori', 'ID_Penerbit', 'ID_Penulis',
  'Judul', 'ISBN', 'TanggalPublikasi', 'Bahasa', 'JumlahHalaman',
  'Format', 'CoverImg', 'Harga', 'DriveLink'
];

echo '<div class="container mt-5">';
echo '<h3 class="mb-4">Tampilan Data Buku</h3>';
echo '<div class="card"><div class="card-body">';
echo "<h5 class='card-title mb-3 text-capitalize'>$table Table</h5>";
echo '<div class="table-responsive"><table class="table table-bordered table-sm"><thead><tr>';

foreach ($columns as $col) echo "<th>$col</th>";
echo "<th>Aksi</th></tr></thead><tbody>";

$result = $conn->query("SELECT * FROM databuku");
while ($row = $result->fetch_assoc()) {
  echo '<tr>';
  foreach ($columns as $col) {
  if ($col === 'CoverImg') {
    echo '<td><img src="data:image/jpeg;base64,' . base64_encode($row[$col]) . '" height="50"></td>';
  } elseif ($col === 'DriveLink') {
    echo '<td><a href="' . htmlspecialchars($row[$col]) . '" target="_blank">Lihat Link</a></td>';
  } else {
    echo '<td>' . htmlspecialchars($row[$col]) . '</td>';
  }
}


  echo "<td>
          <form action='delete.php' method='POST' onsubmit='return confirm(\"Yakin hapus?\");'>
            <input type='hidden' name='table' value='databuku'>
            <input type='hidden' name='id' value='{$row['DataBuku']}'>
            <input type='hidden' name='column' value='DataBuku'>
            <button type='submit' class='btn btn-danger btn-sm'>Hapus</button>
          </form>
        </td>";
  echo '</tr>';
}

echo '</tbody></table></div></div></div></div>';
$conn->close();
?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
