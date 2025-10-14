<?php
// Izinkan akses dari aplikasi Flutter Web (agar API ini bisa diakses dari luar server)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); // Izinkan metode GET, POST, dan OPTIONS
header("Access-Control-Allow-Headers: Content-Type"); // Izinkan header Content-Type
header("Content-Type: application/json"); // Respons API akan dalam format JSON

// Konfigurasi koneksi ke database MySQL
$host = "localhost";
$user = "root";
$pass = "";
$db   = "iventory_db";

// Membuat koneksi ke database menggunakan mysqli
$conn = new mysqli($host, $user, $pass, $db);

// Mengecek apakah koneksi berhasil atau gagal
if ($conn->connect_error) {
    // Jika gagal, kirim respons JSON dengan pesan error
    echo json_encode(["status" => "error", "message" => "Koneksi gagal: " . $conn->connect_error]);
    exit; // Hentikan eksekusi script
}
?>
