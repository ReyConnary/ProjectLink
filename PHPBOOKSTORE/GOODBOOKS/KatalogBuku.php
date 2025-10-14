<?php
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'goodbooks';

session_start();

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Gabung metadata
$query = "
SELECT 
  b.DataBuku, b.Judul, b.ISBN, b.TanggalPublikasi, b.Bahasa, b.JumlahHalaman, b.Format, b.Ringkasan, b.CoverImg, b.Harga,
  p.NamaPenulis, k.NamaKategori, f.NamaFranchise, pb.NamaPenerbit
FROM databuku b
JOIN penulis p ON b.ID_Penulis = p.ID_Penulis
JOIN kategori k ON b.ID_Kategori = k.ID_Kategori
JOIN franchise f ON b.ID_Franchise = f.ID_Franchise
JOIN penerbit pb ON b.ID_Penerbit = pb.ID_Penerbit
ORDER BY b.Judul ASC
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Catalog Buku - GoodBooks</title>
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #f8f9fa; }
    .card img { object-fit: cover; height: 200px; }
    .truncate { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    a.card-link { text-decoration: none; color: inherit; }
    a.card-link:hover { text-decoration: none; }
  </style>
</head>
<body>

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
          <li class="nav-item"><a class="nav-link active" href="KatalogBuku.php">All Books</a></li>
          <?php if (isset($_SESSION['user_id'])): ?>
    <li class="nav-item">
        <a class="nav-link" href="profile.php">Your Profile</a>
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
  <h2 class="mb-4 text-center">📚 Daftar Buku Tersedia</h2>
  <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
    <?php while ($row = $result->fetch_assoc()): ?>
      <div class="col">
        <a href="book.php?id=<?= $row['DataBuku'] ?>" class="card-link">
          <div class="card h-100">
            <?php if (!empty($row['CoverImg'])): ?>
              <img src="data:image/jpeg;base64,<?= base64_encode($row['CoverImg']) ?>" class="card-img-top" alt="Cover">
            <?php else: ?>
              <img src="default_cover.jpg" class="card-img-top" alt="No Cover">
            <?php endif; ?>
            <div class="card-body">
              <h5 class="card-title"><?= htmlspecialchars($row['Judul']) ?></h5>
              <p class="mb-1"><strong>Penulis:</strong> <?= htmlspecialchars($row['NamaPenulis']) ?></p>
              <p class="mb-1"><strong>Penerbit:</strong> <?= htmlspecialchars($row['NamaPenerbit']) ?></p>
              <p class="mb-1"><strong>Franchise:</strong> <?= htmlspecialchars($row['NamaFranchise']) ?></p>
              <p class="mb-1"><strong>Kategori:</strong> <?= htmlspecialchars($row['NamaKategori']) ?></p>
              <p class="mb-1"><strong>ISBN:</strong> <?= htmlspecialchars($row['ISBN']) ?></p>
              <p class="mb-1"><strong>Bahasa:</strong> <?= htmlspecialchars($row['Bahasa']) ?></p>
              <p class="mb-1"><strong>Format:</strong> <?= htmlspecialchars($row['Format']) ?></p>
              <p class="mb-1"><strong>Halaman:</strong> <?= htmlspecialchars($row['JumlahHalaman']) ?></p>
              <p class="mb-1"><strong>Tanggal Publikasi:</strong> <?= htmlspecialchars($row['TanggalPublikasi']) ?></p>
              <p class="mb-1"><strong>Harga:</strong> Rp<?= number_format($row['Harga'], 0, ',', '.') ?></p>
              <p class="mt-2"><strong>Ringkasan:</strong><br><?= nl2br(htmlspecialchars($row['Ringkasan'])) ?></p>
            </div>
          </div>
        </a>
      </div>
    <?php endwhile; ?>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
