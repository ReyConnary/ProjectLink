<?php
// Mengizinkan akses dari semua domain 
header("Access-Control-Allow-Origin: *");
// Mengizinkan semua header 
header("Access-Control-Allow-Headers: *");
// Mengatur agar hasil dikirim dalam format JSON
header("Content-Type: application/json");

// Konfigurasi koneksi ke database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "iventory_db";

// Membuat koneksi ke MySQL
$conn = new mysqli($host, $user, $pass, $db);

// Jika koneksi gagal, kirimkan pesan error dalam format JSON
if ($conn->connect_error) {
  die(json_encode(["status" => "error", "message" => "Database connection failed"]));
}

// Mengambil data dari request POST, dengan nilai default jika kosong
$id_barang = $_POST['id_barang'] ?? '';
$jenis = $_POST['jenis'] ?? ''; // Jenis transaksi: masuk / keluar
$jumlah = (int)($_POST['jumlah'] ?? 0); // Jumlah barang ditransaksikan

// Validasi: semua data wajib diisi dan jumlah > 0
if (empty($id_barang) || empty($jenis) || $jumlah <= 0) {
  echo json_encode(["status" => "error", "message" => "Data tidak lengkap"]);
  exit;
}

// Menyimpan data transaksi ke tabel transaksi
$conn->query("
  INSERT INTO transaksi (id_barang, jenis, jumlah, tanggal)
  VALUES ('$id_barang', '$jenis', '$jumlah', NOW())
");

// Jika jenis transaksi "masuk", tambahkan stok barang
if ($jenis == "masuk") {
  $conn->query("UPDATE barang SET jum_barang = jum_barang + $jumlah WHERE id_barang = '$id_barang'");
// Jika jenis transaksi "keluar", kurangi stok barang 
} else if ($jenis == "keluar") {
  $conn->query("UPDATE barang SET jum_barang = GREATEST(jum_barang - $jumlah, 0) WHERE id_barang = '$id_barang'");
}

// Mengirimkan respon sukses dalam format JSON
echo json_encode(["status" => "success", "message" => "Transaksi berhasil disimpan"]);

// Menutup koneksi database
$conn->close();
?>
