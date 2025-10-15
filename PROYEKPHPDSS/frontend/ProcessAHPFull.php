<?php
// ProcessAHPFull.php?id_judul=...
$host = "localhost";
$user = "root";
$pass = "";
$db   = "perangkingan";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

$id_judul = $_GET['id_judul'] ?? '';
if (!$id_judul) die("ID Judul tidak ditemukan.");

// --- Ambil semua bobot kriteria ---
$q = $conn->prepare("SELECT ID_Kriteria, bobot FROM ahp_bobot_kriteria WHERE ID_Judul=?");
$q->bind_param("s", $id_judul);
$q->execute();
$res = $q->get_result();
$bobot_kriteria = [];
while ($r = $res->fetch_assoc()) {
    $bobot_kriteria[$r['ID_Kriteria']] = (float)$r['bobot'];
}
$q->close();

// --- Ambil bobot subkriteria ---
$q = $conn->prepare("SELECT ID_Kriteria, ID_Sub, bobot FROM ahp_bobot_sub WHERE ID_Judul=?");
$q->bind_param("s", $id_judul);
$q->execute();
$res = $q->get_result();
$bobot_sub = [];
while ($r = $res->fetch_assoc()) {
    $bobot_sub[$r['ID_Sub']] = [
        'id_kriteria' => $r['ID_Kriteria'],
        'bobot'       => (float)$r['bobot']
    ];
}
$q->close();

// --- Ambil alternatif ---
$q = $conn->prepare("SELECT ID_Alternatif, nama_alternatif FROM alternatif WHERE ID_Judul=?");
$q->bind_param("s", $id_judul);
$q->execute();
$res = $q->get_result();
$alternatif = $res->fetch_all(MYSQLI_ASSOC);
$q->close();

// --- Ambil nilai subkriteria yang dipilih tiap alternatif ---
$q = $conn->prepare("SELECT id_alternatif, id_sub FROM nilai_subkriteria WHERE id_judul=?");
$q->bind_param("s", $id_judul);
$q->execute();
$res = $q->get_result();
$pilihan = [];
while ($r = $res->fetch_assoc()) {
    $pilihan[$r['id_alternatif']][] = $r['id_sub'];
}
$q->close();

// --- Hitung skor total tiap alternatif ---
foreach ($alternatif as $alt) {
    $id_alt = $alt['ID_Alternatif'];
    $total  = 0.0;

    if (!empty($pilihan[$id_alt])) {
        foreach ($pilihan[$id_alt] as $id_sub) {
            if (isset($bobot_sub[$id_sub])) {
                $id_kriteria = $bobot_sub[$id_sub]['id_kriteria'];
                $bobot_k     = $bobot_kriteria[$id_kriteria] ?? 0;
                $bobot_s     = $bobot_sub[$id_sub]['bobot'] ?? 0;
                $total      += $bobot_k * $bobot_s;
            }
        }
    }

    // Simpan nilai ke tabel alternatif
    $stmt = $conn->prepare("
        UPDATE alternatif 
        SET nilai_akhir=? 
        WHERE ID_Alternatif=? AND ID_Judul=?
    ");
    $stmt->bind_param("dss", $total, $id_alt, $id_judul);
    $stmt->execute();
    $stmt->close();
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Hasil Perhitungan AHP Full</title>
<style>
body {
    font-family: "Segoe UI", sans-serif;
    background: #fdfdfd;
    color: #333;
    margin: 40px auto;
    max-width: 900px;
}
h2 {
    text-align: center;
    color: #2c3e50;
    margin-bottom: 30px;
}
table {
    border-collapse: collapse;
    width: 100%;
    margin-bottom: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
th, td {
    border: 1px solid #ccc;
    padding: 10px 12px;
    text-align: center;
}
th {
    background: #f0f0f0;
    font-weight: bold;
    color: #2c3e50;
}
tr:nth-child(even) {
    background: #fafafa;
}
tr:hover {
    background: #eaf3ff;
}
a {
    display: inline-block;
    text-decoration: none;
    color: #fff;
    background: #3498db;
    padding: 10px 18px;
    border-radius: 6px;
    transition: background 0.3s;
}
a:hover {
    background: #2980b9;
}
.result-container {
    text-align: center;
}
</style>
</head>
<body>

<h2>Hasil Perhitungan AHP Full</h2>

<table>
    <tr>
        <th>Alternatif</th>
        <th>Nilai Akhir</th>
    </tr>
    <?php
    $q = $conn->prepare("
        SELECT nama_alternatif, nilai_akhir 
        FROM alternatif 
        WHERE ID_Judul=? 
        ORDER BY nilai_akhir DESC
    ");
    $q->bind_param("s", $id_judul);
    $q->execute();
    $res = $q->get_result();
    while ($r = $res->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($r['nama_alternatif']) . "</td>
                <td><b>" . round($r['nilai_akhir'], 4) . "</b></td>
              </tr>";
    }
    $q->close();
    ?>
</table>

<div class="result-container">
    <a href="HalamanUtama.php">⟵ Kembali ke Beranda</a>
</div>

</body>
</html>
