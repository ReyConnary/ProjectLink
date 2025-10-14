<?php
// Menghubungkan ke database menggunakan file koneksi.php
include 'koneksi.php';

// Query untuk mengambil semua data transaksi
// Bergabung (JOIN) dengan tabel barang agar bisa menampilkan nama barang juga
// Hasil diurutkan berdasarkan tanggal transaksi terbaru 
$sql = "SELECT t.*, b.nama_barang 
        FROM transaksi t 
        JOIN barang b ON t.id_barang = b.id_barang
        ORDER BY t.tanggal DESC";

// Menjalankan query ke database
$result = $conn->query($sql);

// Menampung hasil query dalam array
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row; // Menambahkan setiap baris hasil ke array $data
}

// Mengirim hasil data dalam format JSON ke frontend 
echo json_encode($data);

// Menutup koneksi ke database
$conn->close();
?>
