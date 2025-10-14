<?php
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'goodbooks';
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$keyword = $_GET['keyword'] ?? '';
$filter = $_GET['filter'] ?? 'judul';

$sql = "SELECT db.DataBuku, db.Judul, db.CoverImg 
        FROM databuku db 
        JOIN kategori k ON db.ID_Kategori = k.ID_Kategori
        JOIN penulis p ON db.ID_Penulis = p.ID_Penulis
        JOIN penerbit pub ON db.ID_Penerbit = pub.ID_Penerbit
        JOIN franchise f ON db.ID_Franchise = f.ID_Franchise";

switch ($filter) {
  case 'kategori':
    $sql .= " WHERE k.NamaKategori LIKE ?";
    break;
  case 'penulis':
    $sql .= " WHERE p.NamaPenulis LIKE ?";
    break;
  case 'penerbit':
    $sql .= " WHERE pub.NamaPenerbit LIKE ?";
    break;
  case 'franchise':
    $sql .= " WHERE f.NamaFranchise LIKE ?";
    break;
  case 'judul':
  default:
    $sql .= " WHERE db.Judul LIKE ?";
    break;
}

$stmt = $conn->prepare($sql);
$search = '%' . $keyword . '%';
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("SQL Error: " . $conn->error);
}

$stmt->bind_param("s", $search);

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Search Results - GoodBooks</title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
<div class="container py-5">
  <h3>Search Results</h3>
  <?php if ($result->num_rows > 0): ?>
    <ul class="list-group">
  <?php while ($row = $result->fetch_assoc()): ?>
    <li class="list-group-item d-flex align-items-center">
      <img src="data:image/jpeg;base64,<?= base64_encode($row['CoverImg']) ?>" class="img-thumbnail me-3" style="height: 100px;" alt="Cover">
      <a href="book.php?id=<?= $row['DataBuku'] ?>" style="font-size: 1.2rem;">
        <?= htmlspecialchars($row['Judul']) ?>
      </a>
    </li>
  <?php endwhile; ?>
</ul>
  <?php else: ?>
    <p>No books found matching your criteria.</p>
  <?php endif; ?>
</div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
