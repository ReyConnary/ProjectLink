<?php
// Mengatur agar respons dikirim dalam format JSON
header("Content-Type: application/json");

// Menyertakan file koneksi ke database
include "koneksi.php";

// Query untuk mengambil data barang dari tabel barang urut berdasarkan ID terbaru
$query = "SELECT id_barang, nama_barang, jum_barang, harga, supplier, deskripsi, tanggal_input 
          FROM barang ORDER BY id_barang DESC";

// Menjalankan query ke database
$result = mysqli_query($conn, $query);

// Jika query berhasil dijalankan
if ($result) {
    $data = []; // Menyiapkan array untuk menampung hasil data

    // Mengambil setiap baris hasil query sebagai array asosiatif
    while ($row = mysqli_fetch_assoc($result)) {
        // Menyusun data tiap barang ke dalam array
        $data[] = [
            "id_barang"   => $row["id_barang"],
            "nama_barang" => $row["nama_barang"],
            "jum_barang"  => $row["jum_barang"],
            "harga"       => $row["harga"],
            "supplier"    => $row["supplier"],  
            "deskripsi"   => $row["deskripsi"],
            "tanggal_input" => $row["tanggal_input"] 
        ];
    }

    // Mengirim hasil dalam format JSON dengan status sukses
    echo json_encode([
        "status" => "success",
        "data"   => $data
    ]);
} else {
    // Jika query gagal dijalankan, kirim pesan error
    echo json_encode([
        "status"  => "error",
        "message" => "Gagal mengambil data: " . mysqli_error($conn)
    ]);
}

// Menutup koneksi database
mysqli_close($conn);
?>
