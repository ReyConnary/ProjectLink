<?php
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'goodbooks';
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

session_start();

// Ambil id buku
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil data buku
$stmt = $conn->prepare("
SELECT 
  b.DataBuku, b.Judul, b.ISBN, b.TanggalPublikasi, b.Bahasa, b.JumlahHalaman, b.Format, b.Ringkasan, b.CoverImg, b.Harga,
  p.NamaPenulis, k.NamaKategori, f.NamaFranchise, pb.NamaPenerbit
FROM databuku b
JOIN penulis p ON b.ID_Penulis = p.ID_Penulis
JOIN kategori k ON b.ID_Kategori = k.ID_Kategori
JOIN franchise f ON b.ID_Franchise = f.ID_Franchise
JOIN penerbit pb ON b.ID_Penerbit = pb.ID_Penerbit
WHERE b.DataBuku = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$book = $result->fetch_assoc();
$stmt->close();

$is_purchased = false;

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt2 = $conn->prepare("SELECT 1 FROM pesanan WHERE ID_Pelanggan = ? AND DataBuku = ?");
    $stmt2->bind_param("ii", $user_id, $id);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    $is_purchased = $result2->num_rows > 0;
    $stmt2->close();
}

if (!$book) {
  echo "<h2 class='text-center mt-5'>Buku tidak ditemukan.</h2>";
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($book['Judul']) ?> - GoodBooks</title>
  <link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color: #f9f9f9;">

<div class="container py-5">
  <div class="row">
    <div class="col-md-4">
      <?php if (!empty($book['CoverImg'])): ?>
        <img src="data:image/jpeg;base64,<?= base64_encode($book['CoverImg']) ?>" class="img-fluid" alt="Cover">
      <?php else: ?>
        <img src="default_cover.jpg" class="img-fluid" alt="No Cover">
      <?php endif; ?>
    </div>
    <div class="col-md-8">
      <h2><?= htmlspecialchars($book['Judul']) ?></h2>
      <p><strong>Penulis:</strong> <?= htmlspecialchars($book['NamaPenulis']) ?></p>
      <p><strong>Penerbit:</strong> <?= htmlspecialchars($book['NamaPenerbit']) ?></p>
      <p><strong>Franchise:</strong> <?= htmlspecialchars($book['NamaFranchise']) ?></p>
      <p><strong>Kategori:</strong> <?= htmlspecialchars($book['NamaKategori']) ?></p>
      <p><strong>ISBN:</strong> <?= htmlspecialchars($book['ISBN']) ?></p>
      <p><strong>Bahasa:</strong> <?= htmlspecialchars($book['Bahasa']) ?></p>
      <p><strong>Format:</strong> <?= htmlspecialchars($book['Format']) ?></p>
      <p><strong>Jumlah Halaman:</strong> <?= htmlspecialchars($book['JumlahHalaman']) ?></p>
      <p><strong>Tanggal Publikasi:</strong> <?= htmlspecialchars($book['TanggalPublikasi']) ?></p>
      <p><strong>Ringkasan:</strong><br><?= nl2br(htmlspecialchars($book['Ringkasan'])) ?></p>
      <h4 class="mt-4">Harga: <span class="text-success">Rp<?= number_format($book['Harga'], 0, ',', '.') ?></span></h4>

      <?php if ($is_purchased): ?>
  <a href="pembayaran.php?download_only=1&id=<?= $book['DataBuku'] ?>" class="btn btn-success mt-3">📥 Lanjut ke Halaman Download</a>
  <a href="KatalogBuku.php" class="btn btn-green ms-2">Kembali</a>
<?php else: ?>
  <form action="pembayaran.php" method="POST" class="mt-3">
    <input type="hidden" name="DataBuku" value="<?= $book['DataBuku'] ?>">
    <input type="hidden" name="Harga" value="<?= $book['Harga'] ?>">
    <button type="submit" class="btn btn-primary">🛒 Beli Sekarang</button>
    <a href="KatalogBuku.php" class="btn btn-green ms-2">Kembali</a>
  </form>
<?php endif; ?>

    </div>
  </div>
</div>

</body>
</html>

<?php $conn->close(); ?>
