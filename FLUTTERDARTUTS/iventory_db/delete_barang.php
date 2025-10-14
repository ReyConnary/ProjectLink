<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); 
// Aktifkan semua error agar mudah mendeteksi kesalahan

header("Access-Control-Allow-Origin: *"); // Izinkan akses dari semua domain 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); // Izinkan metode HTTP tertentu
header("Access-Control-Allow-Headers: Content-Type"); // Izinkan header Content-Type
header("Content-Type: application/json"); // Set format respon menjadi JSON

include 'koneksi.php'; // Mengimpor file koneksi database

// Ambil nilai id_barang dari request POST, pastikan dalam bentuk integer
$id = isset($_POST['id_barang']) ? (int) $_POST['id_barang'] : 0;

// Validasi: jika ID tidak ada atau tidak valid (<=0), hentikan proses
if ($id <= 0) {
    echo json_encode(["status" => "error", "message" => "ID kosong atau tidak valid"]);
    exit; // Hentikan eksekusi
}

// Buat query SQL untuk menghapus data berdasarkan id_barang
$stmt = $conn->prepare("DELETE FROM barang WHERE id_barang = ?");
$stmt->bind_param("i", $id); // "i" = tipe data integer

// Eksekusi query dan cek hasilnya
if ($stmt->execute() && $stmt->affected_rows > 0) {
    // Jika berhasil dan ada baris yang terhapus
    echo json_encode(["status" => "success", "message" => "Data dihapus"]);
} else {
    // Jika gagal atau tidak ada data yang terhapus
    echo json_encode([
        "status" => "error",
        "message" => $stmt->error ?: "Tidak ada data dihapus",
    ]);
}

// Tutup statement dan koneksi database
$stmt->close();
$conn->close();
?>

