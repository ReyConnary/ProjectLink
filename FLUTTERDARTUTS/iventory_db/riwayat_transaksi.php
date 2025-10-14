<?php
// Mengizinkan akses API dari semua domain (agar bisa diakses oleh Flutter)
header("Access-Control-Allow-Origin: *");
// Mengizinkan semua jenis header dari request
header("Access-Control-Allow-Headers: *");
// Menentukan bahwa output API berupa JSON
header("Content-Type: application/json");

// Konfigurasi koneksi ke database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "iventory_db";

// Membuat koneksi ke database menggunakan mysqli
$conn = new mysqli($host, $user, $pass, $db);

// Mengecek apakah koneksi gagal
if ($conn->connect_error) {
  // Jika gagal, kirim respon JSON error dan hentikan proses
  die(json_encode(["status" => "error", "message" => "Database connection failed"]));
}

// Menjalankan query untuk mengambil semua data transaksi dari tabel transaksi
// Data diurutkan berdasarkan tanggal terbaru
$result = $conn->query("
  SELECT 
    t.id_transaksi,
    t.id_barang,
    b.nama_barang,
    t.jenis,
    t.jumlah,
    t.tanggal
  FROM transaksi t
  LEFT JOIN barang b ON t.id_barang = b.id_barang
  ORDER BY t.tanggal DESC
");


// Menampung hasil query dalam array
$data = [];
while ($row = $result->fetch_assoc()) {
  $data[] = $row; // Tambahkan setiap baris data ke dalam array
}

// Mengecek apakah ada data transaksi
if (count($data) > 0) {
  // Jika ada data, kirim respon JSON dengan status sukses
  echo json_encode(["status" => "success", "data" => $data]);
} else {
  // Jika tidak ada data, kirim pesan kosong
  echo json_encode(["status" => "empty", "message" => "Tidak ada data transaksi"]);
}

// Menutup koneksi database
$conn->close();
?>
