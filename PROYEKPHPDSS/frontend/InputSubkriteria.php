<?php
// Debug mode
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Koneksi database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "perangkingan";
$mysqli = new mysqli($host, $user, $pass, $db);

if ($mysqli->connect_error) {
    die("Koneksi DB gagal: " . $mysqli->connect_error);
}

$id_judul = $_GET['id_judul'] ?? '';
$judul    = $_GET['judul'] ?? '';

if ($id_judul === '') {
    echo "ID Judul tidak ditemukan. <a href='HalamanUtama.php'>Kembali</a>";
    exit;
}

// Ambil daftar kriteria berdasarkan ID_Judul
$query = "SELECT ID_Kriteria, nama_kriteria FROM kriteria WHERE ID_Judul = ?";
$stmt = $mysqli->prepare($query);
if (!$stmt) {
    die("Query gagal disiapkan: " . $mysqli->error);
}
$stmt->bind_param("s", $id_judul);
$stmt->execute();
$res = $stmt->get_result();
$kriteria = $res->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Proses simpan subkriteria
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['subkriteria'])) {
        foreach ($_POST['subkriteria'] as $id_kriteria => $listSub) {
            foreach ($listSub as $nama_sub) {
                if (trim($nama_sub) !== '') {
                    $id_sub = "SUB-" . uniqid();
                    $sql = "INSERT INTO subkriteria (ID_Sub, ID_Kriteria, nama_sub, ID_Judul) VALUES (?, ?, ?, ?)";
                    $insert = $mysqli->prepare($sql);
                    if (!$insert) {
                        die("Gagal insert: " . $mysqli->error);
                    }
                    $insert->bind_param("ssss", $id_sub, $id_kriteria, $nama_sub, $id_judul);
                    $insert->execute();
                    $insert->close();
                }
            }
        }

        // ✅ Redirect otomatis ke tahap berikutnya (AssignSubToAlternatif.php)
        echo "<script>
                alert('Subkriteria berhasil disimpan! Sekarang lanjut ke tahap assign subkriteria ke alternatif.');
                window.location='AssignSubToAlternatif.php?id_judul=" . urlencode($id_judul) . "&judul=" . urlencode($judul) . "';
              </script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Input Subkriteria</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea, #764ba2);
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .container {
            background: #fff;
            border-radius: 20px;
            padding: 30px;
            max-width: 900px;
            margin: auto;
            box-shadow: 0px 8px 25px rgba(0,0,0,0.2);
        }
        h2, h3 { text-align: center; font-weight: 600; }
        .kriteria-box { margin: 20px 0; padding: 15px; border: 1px solid #ccc; border-radius: 10px; }
        input[type="number"], input[type="text"] {
            width: 80%;
            padding: 8px;
            margin: 5px 0;
            border-radius: 8px;
            border: 1px solid #ccc;
        }
        .btn {
            background: #f39c12;
            color: white;
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 10px;
            display: inline-block;
            margin-top: 10px;
            transition: 0.3s;
            border: none;
            cursor: pointer;
        }
        .btn:hover { background: #e67e22; }
        .back { text-align: center; margin-top: 20px; }
    </style>
    <script>
        function generateSubInputs(idKriteria) {
            const jumlah = document.getElementById('jumlah_' + idKriteria).value;
            const container = document.getElementById('subkriteria-' + idKriteria);
            container.innerHTML = ''; // reset

            for (let i = 1; i <= jumlah; i++) {
                const input = document.createElement('input');
                input.type = 'text';
                input.name = 'subkriteria[' + idKriteria + '][]';
                input.placeholder = 'Nama Subkriteria ' + i;
                container.appendChild(input);
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Input Subkriteria</h2>
        <h3>Untuk: <?= htmlspecialchars($judul) ?> (ID: <?= htmlspecialchars($id_judul) ?>)</h3>

        <form method="POST">
            <?php foreach ($kriteria as $k): ?>
                <div class="kriteria-box">
                    <h4><?= htmlspecialchars($k['nama_kriteria']) ?></h4>
                    <label>Jumlah Subkriteria: </label>
                    <input type="number" id="jumlah_<?= $k['ID_Kriteria'] ?>" min="1" max="10" value="3">
                    <button type="button" class="btn" onclick="generateSubInputs('<?= $k['ID_Kriteria'] ?>')">Generate</button>
                    <div id="subkriteria-<?= $k['ID_Kriteria'] ?>" style="margin-top:10px;"></div>
                </div>
            <?php endforeach; ?>

            <center><button type="submit" class="btn">Simpan & Lanjut</button></center>
        </form>
    </div>
</body>
</html>
