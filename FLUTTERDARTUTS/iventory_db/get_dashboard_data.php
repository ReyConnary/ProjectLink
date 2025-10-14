<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

$host = "localhost";
$user = "root";
$pass = "";
$db   = "iventory_db";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die(json_encode(["error" => "Database connection failed"]));
}

// =========================
//  1. Total jenis barang
// =========================
$total_barang = $conn->query("
  SELECT COUNT(*) AS total 
  FROM barang
")->fetch_assoc()['total'];

// =========================
//  2. Barang masuk hari ini
// =========================
// HANYA dihitung dari tabel transaksi
$total_masuk_hari_ini = $conn->query("
  SELECT IFNULL(SUM(jumlah), 0) AS total
  FROM transaksi
  WHERE jenis = 'masuk' AND DATE(tanggal) = CURDATE()
")->fetch_assoc()['total'];

// =========================
//  3. Barang keluar hari ini
// =========================
$total_keluar_hari_ini = $conn->query("
  SELECT IFNULL(SUM(jumlah), 0) AS total
  FROM transaksi
  WHERE jenis = 'keluar' AND DATE(tanggal) = CURDATE()
")->fetch_assoc()['total'];

// =========================
//  4. Barang hampir habis
// =========================
$stok_habis = $conn->query("
  SELECT nama_barang, jum_barang
  FROM barang
  WHERE jum_barang < 10
");

$low_stock = [];
while ($row = $stok_habis->fetch_assoc()) {
  $low_stock[] = $row;
}

// =========================
//  5. Nilai total stok
// =========================
$nilai_stok = $conn->query("
  SELECT IFNULL(SUM(harga * jum_barang), 0) AS total 
  FROM barang
")->fetch_assoc()['total'];

// =========================
//  6. Output JSON
// =========================
echo json_encode([
  "status" => "success",
  "total_barang" => (int)$total_barang,
  "barang_masuk_hari_ini" => (int)$total_masuk_hari_ini,
  "barang_keluar_hari_ini" => (int)$total_keluar_hari_ini,
  "nilai_stok" => (float)$nilai_stok,
  "stok_hampir_habis" => $low_stock
], JSON_PRETTY_PRINT);

$conn->close();
?>
