<?php
require_once 'auth.php';
requireLogin();

$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'goodbooks';
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$user_id = $_SESSION['user_id'];

function convertToDirectDownload($driveLink) {
    if (preg_match('/\/d\/(.+?)\//', $driveLink, $matches)) {
        $fileId = $matches[1];
        return "https://drive.google.com/uc?export=download&id=$fileId";
    }
    return $driveLink;
}

// Klo sudah beli
if (isset($_GET['download_only']) && isset($_GET['id'])) {
    $id_buku = (int) $_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM databuku WHERE DataBuku = ?");
    $stmt->bind_param("i", $id_buku);
    $stmt->execute();
    $buku = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$buku) {
        die("<div class='container py-5'><h3>Buku tidak ditemukan.</h3></div>");
    }

    // Apa user sudah beli buku ini
    $stmt = $conn->prepare("SELECT 1 FROM pesanan WHERE ID_Pelanggan = ? AND DataBuku = ?");
    $stmt->bind_param("ii", $user_id, $id_buku);
    $stmt->execute();
    $result = $stmt->get_result();
    $already_purchased = $result->num_rows > 0;
    $stmt->close();

    if (!$already_purchased) {
        die("<div class='container py-5'><h3>Anda belum membeli buku ini.</h3></div>");
    }

    // download
    $judul = $buku['Judul'];
    $harga = $buku['Harga'];
    $driveLink = convertToDirectDownload($buku['DriveLink']);

    echo "<div class='container py-5'>";
    echo "<h3>Unduh Buku</h3>";
    echo "<p><strong>Buku:</strong> $judul</p>";
    echo "<p><strong>Harga:</strong> Rp " . number_format($harga, 0, ',', '.') . "</p>";
    echo "<p><strong>File Buku:</strong> <a href='$driveLink' target='_blank' class='btn btn-success'>Download Buku</a></p>";
    echo "<a href='GoodBooks.php' class='btn btn-primary mt-3'>Kembali ke Beranda</a>";
    echo "</div>";
    exit;
}

$id_buku = isset($_POST['DataBuku']) ? $_POST['DataBuku'] : '';

$user = $conn->query("SELECT b.NamaBank, p.NoRek FROM pelanggan p JOIN bank b ON p.ID_Bank = b.ID_Bank WHERE p.ID_Pelanggan = $user_id")->fetch_assoc();

$stmt = $conn->prepare("SELECT * FROM databuku WHERE DataBuku = ?");
$stmt->bind_param("i", $id_buku);
$stmt->execute();
$buku = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$buku) die("Buku tidak ditemukan.");

// Beli
if (isset($_POST['confirm'])) {
    $harga = $buku['Harga'];
    $judul = $buku['Judul'];
    $tanggal = date("Y-m-d");

    // 1. Cek saldo
    $result = $conn->query("SELECT Saldo FROM pelanggan WHERE ID_Pelanggan = $user_id");
    $row = $result->fetch_assoc();
    $saldo = $row['Saldo'];

    if ($saldo < $harga) {
        echo "<div class='container py-5'>";
        echo "<h3 class='text-danger'>Saldo tidak cukup untuk membeli buku ini.</h3>";
        echo "<p><strong>Saldo Anda:</strong> Rp " . number_format($saldo, 0, ',', '.') . "</p>";
        echo "<p><strong>Harga Buku:</strong> Rp " . number_format($harga, 0, ',', '.') . "</p>";
        echo "<a href='GoodBooks.php' class='btn btn-primary mt-3'>Kembali</a>";
        echo "</div>";
        exit;
    }

    // 2. increment ID_Pembayaran
    $next_id = 1;
$result = $conn->query("SELECT MAX(ID_Pembayaran) AS max_id FROM pembayaran");
if ($result) {
    $row = $result->fetch_assoc();
    if ($row && $row['max_id'] !== null) {
        $next_id = $row['max_id'] + 1;
    }
}

$pesan_id = 1;
$result = $conn->query("SELECT MAX(ID_Pesanan) AS max_id FROM pesanan");
if ($result) {
    $row = $result->fetch_assoc();
    if ($row && $row['max_id'] !== null) {
        $pesan_id = $row['max_id'] + 1;
    }
}

    // 3. Insert pembayaran
    $stmt1 = $conn->prepare("INSERT INTO pembayaran (ID_Pembayaran, ID_Pelanggan, TotalHarga, TanggalPembayaran) VALUES (?, ?, ?, ?)");
    $stmt1->bind_param("iids", $next_id, $user_id, $harga, $tanggal);
    $stmt1->execute();
    $stmt1->close();

    // 4. Insert pesanan
    $stmt2 = $conn->prepare("INSERT INTO pesanan (ID_Pesanan, ID_Pelanggan, DataBuku, ID_Pembayaran, HargaSatuan) VALUES (?, ?, ?, ?, ?)");
    $stmt2->bind_param("iiiid", $pesan_id, $user_id, $id_buku, $next_id, $harga);
    $stmt2->execute();
    $stmt2->close();

    // 5. Kurangin saldo
    $stmt3 = $conn->prepare("UPDATE pelanggan SET Saldo = Saldo - ? WHERE ID_Pelanggan = ?");
    $stmt3->bind_param("di", $harga, $user_id);
    $stmt3->execute();
    $stmt3->close();

    // 6. Buat kwitansi
    require_once 'fpdf186/fpdf.php';

$pdf = new FPDF();
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 16);
$pdf->SetFillColor(220, 220, 255);
$pdf->Cell(0, 15, 'KWITANSI PEMBAYARAN', 0, 1, 'C', true);
$pdf->Ln(5);

$pdf->SetFont('Arial', '', 12);
$pdf->SetTextColor(0);

function addRow($pdf, $label, $value) {
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(50, 10, $label, 0, 0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, ": $value", 0, 1);
}

addRow($pdf, 'Nama Buku', $judul);
addRow($pdf, 'Harga', 'Rp ' . number_format($harga, 0, ',', '.'));
addRow($pdf, 'Tanggal', $tanggal);
addRow($pdf, 'Bank', $user['NamaBank']);
addRow($pdf, 'No. Rekening', $user['NoRek']);
addRow($pdf, 'ID Pembayaran', $next_id);

$pdf->Ln(5);
$pdf->SetDrawColor(100, 100, 100);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(10);

$pdf->SetFont('Arial', 'I', 11);
$pdf->SetTextColor(50, 50, 50);
$pdf->MultiCell(0, 10, "Terima kasih telah melakukan pembelian di GoodBooks.\nBuku yang telah dibeli dapat diunduh melalui link yang tersedia di sistem.");

$pdf->Ln(10);

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Hormat Kami,', 0, 1, 'R');
$pdf->Ln(15);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'GoodBooks Team', 0, 1, 'R');

// Save
if (!is_dir('kwitansi')) mkdir('kwitansi');
$filename = "kwitansi/kwitansi_" . time() . ".pdf";
$pdf->Output('F', $filename);


    $driveLink = convertToDirectDownload($buku['DriveLink']);

    echo "<div class='container py-5'>";
    echo "<h3>Pembayaran Berhasil!</h3>";
    echo "<p><strong>Buku:</strong> $judul</p>";
    echo "<p><strong>Harga:</strong> Rp " . number_format($harga, 0, ',', '.') . "</p>";
    echo "<p><strong>Saldo Tersisa:</strong> Rp " . number_format($saldo - $harga, 0, ',', '.') . "</p>";
    echo "<p><strong>Kwitansi:</strong> <a href='$filename' download>Download Kwitansi</a></p>";
    echo "<p><strong>File Buku:</strong> <a href='$driveLink' target='_blank' class='btn btn-success'>Download Buku</a></p>";
    echo "<a href='GoodBooks.php' class='btn btn-primary mt-3'>Kembali ke Beranda</a>";
    echo "</div>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Pembayaran - GoodBooks</title>
  <link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <h2>Konfirmasi Pembayaran</h2>
  <p><strong>Buku:</strong> <?= htmlspecialchars($buku['Judul']) ?></p>
  <p><strong>Harga:</strong> Rp <?= number_format($buku['Harga'], 0, ',', '.') ?></p>
  <p><strong>Bank:</strong> <?= htmlspecialchars($user['NamaBank']) ?></p>
  <p><strong>No Rekening:</strong> <?= htmlspecialchars($user['NoRek']) ?></p>

  <form method="POST">
    <input type="hidden" name="DataBuku" value="<?= htmlspecialchars($buku['DataBuku']) ?>">
    <button type="submit" name="confirm" class="btn btn-success">Konfirmasi Pembelian</button>
    <a href="GoodBooks.php" class="btn btn-secondary">Batal</a>
  </form>
</div>
</body>
</html>
