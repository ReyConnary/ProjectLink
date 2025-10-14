<?php
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'goodbooks';
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="laporan_transaksi.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['ID Pembayaran', 'ID Pelanggan', 'Judul Buku', 'Harga Satuan', 'Total Harga Transaksi', 'Tanggal Pembayaran']);

$sql = "
SELECT 
  pembayaran.ID_Pembayaran,
  pembayaran.ID_Pelanggan,
  pembayaran.TotalHarga,
  pembayaran.TanggalPembayaran,
  databuku.Judul,
  pesanan.HargaSatuan
FROM pembayaran
JOIN pesanan ON pembayaran.ID_Pembayaran = pesanan.ID_Pembayaran
JOIN databuku ON pesanan.DataBuku = databuku.DataBuku
ORDER BY pembayaran.TanggalPembayaran DESC
";

$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
  fputcsv($output, [
    $row['ID_Pembayaran'],
    $row['ID_Pelanggan'],
    $row['Judul'],
    $row['HargaSatuan'],
    $row['TotalHarga'],
    $row['TanggalPembayaran']
  ]);
}
fclose($output);
$conn->close();
exit;
