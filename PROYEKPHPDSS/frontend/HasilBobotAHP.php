<?php
// Aktifkan error display (debug)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Koneksi ke database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "perangkingan";
$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_error) die("Koneksi DB gagal: " . $mysqli->connect_error);

$id_judul = $_GET['id_judul'] ?? '';
$judul    = $_GET['judul'] ?? '';

if ($id_judul === '') {
    echo "ID Judul tidak ditemukan. <a href='HalamanUtama.php'>Kembali</a>";
    exit;
}

// Ambil data kriteria + bobot dari tabel ahp_bobot_kriteria
$query = "
    SELECT k.nama_kriteria, b.bobot
    FROM ahp_bobot_kriteria b
    JOIN kriteria k ON k.ID_Kriteria = b.id_kriteria
    WHERE b.id_judul = ?
    ORDER BY k.ID_Kriteria
";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("s", $id_judul);
$stmt->execute();
$res = $stmt->get_result();

$kriteria = [];
$total_bobot = 0;
while ($row = $res->fetch_assoc()) {
    $kriteria[] = $row;
    $total_bobot += $row['bobot'];
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hasil Bobot AHP</title>
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
            text-align: center;
        }
        h2 { font-weight: 600; margin-bottom: 20px; }
        table { border-collapse: collapse; margin: 20px auto; width: 80%; }
        th, td { border: 1px solid #ddd; padding: 10px 15px; text-align: center; }
        th { background: #f39c12; color: white; }
        td:first-child { text-align: left; }
        .btn {
            display: inline-block;
            background: #f39c12;
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            margin: 10px;
            border-radius: 10px;
            font-size: 15px;
            transition: 0.3s;
        }
        .btn:hover { background: #e67e22; }
        .note { color: #555; font-size: 14px; margin-top: 10px; }
        .back { margin-top: 20px; display: inline-block; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Hasil Bobot Kriteria – AHP</h2>
        <p><b>Judul:</b> <?= htmlspecialchars($judul) ?> (ID: <?= htmlspecialchars($id_judul) ?>)</p>

        <?php if (count($kriteria) === 0): ?>
            <p>Belum ada hasil bobot yang dihitung untuk judul ini.</p>
            <a href="InputAHP.php?id_judul=<?= urlencode($id_judul) ?>&judul=<?= urlencode($judul) ?>" class="btn">Hitung AHP</a>
        <?php else: ?>
            <table>
                <tr>
                    <th>No</th>
                    <th>Nama Kriteria</th>
                    <th>Bobot AHP</th>
                </tr>
                <?php foreach ($kriteria as $i => $k): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($k['nama_kriteria']) ?></td>
                        <td><?= number_format($k['bobot'], 4) ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <th colspan="2">Total</th>
                    <th><?= number_format($total_bobot, 4) ?></th>
                </tr>
            </table>

            <p class="note">Silakan pilih metode selanjutnya untuk melanjutkan proses perhitungan:</p>

            <div>
                <a href="HasilAHP+SAW.php?id_judul=<?= urlencode($id_judul) ?>&judul=<?= urlencode($judul) ?>" class="btn">Lanjut ke SAW</a>
                <a href="HasilAHP+WP.php?id_judul=<?= urlencode($id_judul) ?>&judul=<?= urlencode($judul) ?>" class="btn">Lanjut ke WP</a>
                <a href="InputSubkriteria.php?id_judul=<?= urlencode($id_judul) ?>&judul=<?= urlencode($judul) ?>" class="btn">Lanjut ke AHP (Subkriteria)</a>
            </div>
        <?php endif; ?>

        <div class="back">
            <a href="HalamanUtama.php" style="color:#f39c12; text-decoration:none;">← Kembali ke Halaman Utama</a>
        </div>
    </div>
</body>
</html>
