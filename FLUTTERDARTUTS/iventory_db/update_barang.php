<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); 
// Mengaktifkan semua error untuk debugging

header("Access-Control-Allow-Origin: *"); // Izinkan akses dari semua domain
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); // Izinkan metode HTTP tertentu
header("Access-Control-Allow-Headers: Content-Type"); // Izinkan header Content-Type
header("Content-Type: application/json"); // Set output menjadi format JSON

include 'koneksi.php'; // Menghubungkan ke file koneksi database

// Mengambil data dari request POST, jika tidak ada, gunakan nilai default
$id        = $_POST['id_barang'] ?? '';
$nama      = $_POST['nama_barang'] ?? '';
$jumlah    = isset($_POST['jum_barang']) ? (int)$_POST['jum_barang'] : 0;
$harga     = isset($_POST['harga']) ? (float)$_POST['harga'] : 0;
$supplier  = $_POST['supplier'] ?? '-';
$deskripsi = $_POST['deskripsi'] ?? '';

// Validasi sederhana: pastikan ID dan nama tidak kosong
if (empty($id) || empty($nama)) {
    echo json_encode(["status" => "error", "message" => "ID dan nama wajib diisi"]);
    exit; // Hentikan eksekusi jika data tidak lengkap
}

// Membuat query SQL untuk mengupdate data barang berdasarkan ID
$stmt = $conn->prepare("
    UPDATE barang 
    SET nama_barang = ?, jum_barang = ?, harga = ?, supplier = ?, deskripsi = ?
    WHERE id_barang = ?
");

// Mengikat parameter ke query
// Format "sidssi" artinya:
// s = string, i = integer, d = double
$stmt->bind_param("sidssi", $nama, $jumlah, $harga, $supplier, $deskripsi, $id);

// Eksekusi query dan kirimkan respon JSON sesuai hasilnya
if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Data berhasil diupdate"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}

// Tutup statement dan koneksi
$stmt->close();
$conn->close();
?>
