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

$nextID = [
  'penerbit' => getNextId($conn, 'penerbit', 'ID_Penerbit'),
  'bank' => getNextId($conn, 'bank', 'ID_Bank'),
  'franchise' => getNextId($conn, 'franchise', 'ID_Franchise'),
  'kategori' => getNextId($conn, 'kategori', 'ID_Kategori'),
  'penulis' => getNextId($conn, 'penulis', 'ID_Penulis'),
];
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Halaman Metadata</title>
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
          <li class="nav-item"><a class="nav-link active" href="Admin.php">Edit Metadata</a></li>
          <li class="nav-item"><a class="nav-link" href="DataBuku.php">Edit Data Buku</a></li>
          <li class="nav-item"><a class="nav-link" href="DataLaporan.php">Laporan Transaksi</a></li>
          <li class="nav-item"><a class="nav-link" href="DataPelanggan.php">Edit Data Pelanggan</a></li>

        </ul>
      </div>
    </div>
  </nav>

<div class="container py-5">
  <h2 class="mb-4">Tambahkan Data</h2>
  <div class="card shadow" style="background-color: bone;">
      <div class="card-body">
  <form action="insert_data.php" method="POST" enctype="multipart/form-data">
    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-5 g-4">

      <!-- Penerbit -->
      <div class="col">
        <h5>Penerbit</h5>
        <input type="text" class="form-control mb-2" name="ID_Penerbit" value="<?= $nextID['penerbit'] ?>" readonly>
        <input type="text" class="form-control mb-2" name="NamaPenerbit" placeholder="Nama Penerbit">
      </div>

      <!-- Bank -->
      <div class="col">
        <h5>Bank</h5>
        <input type="text" class="form-control mb-2" name="ID_Bank" value="<?= $nextID['bank'] ?>" readonly>
        <input type="text" class="form-control mb-2" name="NamaBank" placeholder="Nama Bank">
      </div>

      <!-- Franchise -->
      <div class="col">
        <h5>Franchise</h5>
        <input type="text" class="form-control mb-2" name="ID_Franchise" value="<?= $nextID['franchise'] ?>" readonly>
        <input type="text" class="form-control mb-2" name="NamaFranchise" placeholder="Nama Franchise">
        <textarea class="form-control mb-2" name="Deskripsi" placeholder="Deskripsi"></textarea>
      </div>

      <!-- Kategori -->
      <div class="col">
        <h5>Kategori</h5>
        <input type="text" class="form-control mb-2" name="ID_Kategori" value="<?= $nextID['kategori'] ?>" readonly>
        <input type="text" class="form-control mb-2" name="NamaKategori" placeholder="Nama Kategori">
      </div>

      <!-- Penulis -->
      <div class="col">
        <h5>Penulis</h5>
        <input type="text" class="form-control mb-2" name="ID_Penulis" value="<?= $nextID['penulis'] ?>" readonly>
        <input type="text" class="form-control mb-2" name="NamaPenulis" placeholder="Nama Penulis">
        <div class="mb-3">
          <label for="FotoPenulis" class="form-label">Upload Foto Penulis</label>
          <input type="file" class="form-control" name="FotoPenulis" id="FotoPenulis" accept="image/*">
        </div>
        <input type="number" class="form-control mb-2" name="TahunMulaiAktif" placeholder="Tahun Mulai Aktif">
        <input type="number" class="form-control mb-2" name="TahunBerhenti" placeholder="Tahun Berhenti">
      </div>
    </div>

    <div class="text-center mt-4">
      <button type="submit" class="btn btn-success px-5">Tambah Semua Data</button>
    </div>
  </form>
</div>

 </div>
    </div>
  </div>



<?php
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$tables = [
  'penerbit' => ['ID_Penerbit', 'NamaPenerbit'],
  'bank' => ['ID_Bank', 'NamaBank'],
  'franchise' => ['ID_Franchise', 'NamaFranchise', 'Deskripsi'],
  'kategori' => ['ID_Kategori', 'NamaKategori'],
  'penulis' => ['ID_Penulis', 'NamaPenulis', 'FotoPenulis', 'TahunMulaiAktif', 'TahunBerhenti']
];

echo '<div class="container mt-5"><h3 class="mb-4">Tampilan Database</h3><div class="row row-cols-1 row-cols-md-2 row-cols-xl-2 g-4">';

foreach ($tables as $table => $columns) {
  $result = $conn->query("SELECT * FROM $table");
  echo '<div class="col"><div class="card"><div class="card-body">';
  echo "<h5 class='card-title mb-3 text-capitalize'>$table Table</h5>";
  echo '<div class="table-responsive"><table class="table table-bordered table-sm"><thead><tr>';
  foreach ($columns as $col) echo "<th>$col</th>";
  echo "<th>Aksi</th></tr></thead><tbody>";
  while ($row = $result->fetch_assoc()) {
    echo '<tr>';
    foreach ($columns as $col) {
      if ($col === 'FotoPenulis') {
        echo '<td><img src="data:image/jpeg;base64,' . base64_encode($row[$col]) . '" height="50"></td>';
      } else {
        echo '<td>' . htmlspecialchars($row[$col]) . '</td>';
      }
    }
    $id_col = $columns[0];
    echo "<td>
            <form action='delete.php' method='POST' onsubmit='return confirm(\"Yakin hapus?\");'>
              <input type='hidden' name='table' value='$table'>
              <input type='hidden' name='id' value='{$row[$id_col]}'>
              <input type='hidden' name='column' value='$id_col'>
              <button type='submit' class='btn btn-danger btn-sm'>Hapus</button>
            </form>
          </td>";
    echo '</tr>';
  }
  echo '</tbody></table></div></div></div></div>';
}

echo '</div></div>';
$conn->close();
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
