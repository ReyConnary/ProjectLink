<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); 
// Menampilkan semua error untuk debugging

header("Access-Control-Allow-Origin: *"); // Izinkan akses dari semua domain 
header("Access-Control-Allow-Headers: *"); // Izinkan semua header
header("Content-Type: application/json");  // Set output menjadi format JSON

// Konfigurasi koneksi ke database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "iventory_db";

// Membuat koneksi ke database MySQL
$conn = new mysqli($host, $user, $pass, $db);

// Jika koneksi gagal, kirim pesan error
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Koneksi database gagal"]);
    exit;
}

// Ambil data dari request POST, dan beri nilai default jika tidak ada
$nama       = $_POST['nama_barang'] ?? '';
$jumlah     = isset($_POST['jum_barang']) ? (int)$_POST['jum_barang'] : 0;
$harga      = isset($_POST['harga']) ? (float)$_POST['harga'] : 0;
$supplier   = $_POST['supplier'] ?? '-';
$deskripsi  = $_POST['deskripsi'] ?? '';
$tanggal    = $_POST['tanggal_input'] ?? date('Y-m-d'); // Gunakan tanggal hari ini jika tidak diisi

// Validasi: nama barang wajib diisi
if (empty($nama)) {
    echo json_encode(["status" => "error", "message" => "Nama barang wajib diisi"]);
    exit;
}

// Query SQL untuk menambahkan data barang baru
$stmt = $conn->prepare("
    INSERT INTO barang (nama_barang, jum_barang, harga, supplier, deskripsi, tanggal_input)
    VALUES (?, ?, ?, ?, ?, ?)
");

// Mengikat parameter ke query
// Format "sidsss":
// s = string, i = integer, d = double
$stmt->bind_param("sidsss", $nama, $jumlah, $harga, $supplier, $deskripsi, $tanggal);

// Eksekusi query dan kirim respon JSON berdasarkan hasilnya
if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Barang berhasil ditambahkan"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}

// Tutup statement dan koneksi database
$stmt->close();
$conn->close();
?>
