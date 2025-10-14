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

// Penerbit
if ($id = getPostVal('ID_Penerbit')) {
    $nama = getPostVal('NamaPenerbit');
    if ($nama) {
        $stmt = $conn->prepare("INSERT INTO penerbit (ID_Penerbit, NamaPenerbit) VALUES (?, ?)");
        $stmt->bind_param("is", $id, $nama);
        $stmt->execute();
        $stmt->close();
    }
}

// Bank
if ($id = getPostVal('ID_Bank')) {
    $nama = getPostVal('NamaBank');
    if ($nama) {
        $stmt = $conn->prepare("INSERT INTO bank (ID_Bank, NamaBank) VALUES (?, ?)");
        $stmt->bind_param("is", $id, $nama);
        $stmt->execute();
        $stmt->close();
    }
}

// Franchise
if ($id = getPostVal('ID_Franchise')) {
    $nama = getPostVal('NamaFranchise');
    $deskripsi = getPostVal('Deskripsi');
    if ($nama) {
        $stmt = $conn->prepare("INSERT INTO franchise (ID_Franchise, NamaFranchise, Deskripsi) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $id, $nama, $deskripsi);
        $stmt->execute();
        $stmt->close();
    }
}

// Kategori
if ($id = getPostVal('ID_Kategori')) {
    $nama = getPostVal('NamaKategori');
    if ($nama) {
        $stmt = $conn->prepare("INSERT INTO kategori (ID_Kategori, NamaKategori) VALUES (?, ?)");
        $stmt->bind_param("is", $id, $nama);
        $stmt->execute();
        $stmt->close();
    }
}

// Penulis
if ($id = getPostVal('ID_Penulis')) {
    $nama = getPostVal('NamaPenulis');
    $tahunMulai = getPostVal('TahunMulaiAktif') ?: null;
    $tahunBerhenti = getPostVal('TahunBerhenti') ?: null;

    if ($nama) {
        $imgData = null;
        $hasImage = isset($_FILES['FotoPenulis']) && $_FILES['FotoPenulis']['error'] === UPLOAD_ERR_OK;

        if ($hasImage) {
            $imgData = file_get_contents($_FILES['FotoPenulis']['tmp_name']);
        }

        $stmt = $conn->prepare("INSERT INTO penulis (ID_Penulis, NamaPenulis, FotoPenulis, TahunMulaiAktif, TahunBerhenti) VALUES (?, ?, ?, ?, ?)");
        $null = NULL;
        $stmt->bind_param("isbii", $id, $nama, $null, $tahunMulai, $tahunBerhenti);
        if ($hasImage) {
            $stmt->send_long_data(2, $imgData);
        }
        $stmt->execute();
        $stmt->close();
    }
}


$conn->close();
header("Location: admin.php");
?>
