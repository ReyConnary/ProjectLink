<?php
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'goodbooks';

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

function getPostVal($key) {
    return isset($_POST[$key]) ? $_POST[$key] : null;
}

if ($id = getPostVal('DataBuku')) {
    $idFranchise = getPostVal('ID_Franchise');
    $idKategori = getPostVal('ID_Kategori');
    $idPenerbit = getPostVal('ID_Penerbit');
    $idPenulis = getPostVal('ID_Penulis');
    $judul = getPostVal('Judul');
    $isbn = getPostVal('ISBN');
    $tanggal = getPostVal('TanggalPublikasi') ?: null;
    $bahasa = getPostVal('Bahasa');
    $jumlahHalaman = getPostVal('JumlahHalaman') ?: null;
    $format = getPostVal('Format');
    $ringkasan = getPostVal('Ringkasan');
    $harga = getPostVal('Harga');
    $driveLink = getPostVal('DriveLink');

    if ($judul && $driveLink) {
        $imgData = null;
        $hasImage = isset($_FILES['CoverImg']) && $_FILES['CoverImg']['error'] === UPLOAD_ERR_OK;

        if ($hasImage) {
            $imgData = file_get_contents($_FILES['CoverImg']['tmp_name']);
        }

        $stmt = $conn->prepare("INSERT INTO databuku 
        (DataBuku, ID_Franchise, ID_Kategori, ID_Penerbit, ID_Penulis,
        Judul, ISBN, TanggalPublikasi, Bahasa, JumlahHalaman,
        Format, Ringkasan, Harga, CoverImg, DriveLink)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $null = NULL;

        $stmt->bind_param("iiiisssssissdbs", 
            $id, $idFranchise, $idKategori, $idPenerbit, $idPenulis,
            $judul, $isbn, $tanggal, $bahasa, $jumlahHalaman,
            $format, $ringkasan, $harga, $null, $driveLink
        );

        if ($hasImage) {
            $stmt->send_long_data(13, $imgData); // CoverImg is at index 13 (0-based)
        }

        $stmt->execute();
        $stmt->close();
    }
}

$conn->close();
header("Location: DataBuku.php");
?>