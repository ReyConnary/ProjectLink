<?php
// Aktifkan error display (debug)
ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = "localhost"; 
$user = "root"; 
$pass = ""; 
$db   = "perangkingan";

$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_error) die("Koneksi DB gagal: " . $mysqli->connect_error);

$id_judul = $_GET['id_judul'] ?? '';
$judul    = $_GET['judul'] ?? '';

if ($id_judul == '') {
    echo "ID Judul tidak ditemukan. <a href='HalamanUtama.php'>Kembali</a>";
    exit;
}

$isSubmitted = ($_SERVER['REQUEST_METHOD'] === 'POST');

/* ==========================================================
   1️⃣ Ambil data kriteria + bobot + status (B/C)
========================================================== */
$q_kriteria = "
    SELECT ID_Kriteria, nama_kriteria, status,
           (SELECT bobot FROM ahp_bobot_kriteria 
            WHERE id_kriteria=k.ID_Kriteria AND id_judul=k.ID_Judul) AS bobot
    FROM kriteria k
    WHERE k.ID_Judul = ?
";
$stmt = $mysqli->prepare($q_kriteria);
$stmt->bind_param("s", $id_judul);
$stmt->execute();
$res_kriteria = $stmt->get_result();
$kriteria = [];
while ($row = $res_kriteria->fetch_assoc()) {
    if ($row['bobot'] !== null) $kriteria[] = $row;
}
$stmt->close();

if (count($kriteria) == 0) {
    echo "<p>Belum ada bobot AHP untuk judul ini.</p>";
    exit;
}

/* ==========================================================
   2️⃣ Jika form disubmit → simpan status (enum B/C)
========================================================== */
if ($isSubmitted && !empty($_POST['status'])) {
    $stmtUpd = $mysqli->prepare("UPDATE kriteria SET status=? WHERE ID_Kriteria=? AND ID_Judul=?");
    $mysqli->begin_transaction();
    try {
        foreach ($_POST['status'] as $idk => $st) {
            $st = ($st === 'B' || $st === 'C') ? $st : (($st === 'Benefit') ? 'B' : 'C');
            $stmtUpd->bind_param("sss", $st, $idk, $id_judul);
            $stmtUpd->execute();
        }
        $mysqli->commit();
    } catch (Exception $e) {
        $mysqli->rollback();
        die("Gagal menyimpan status: " . $e->getMessage());
    }
    $stmtUpd->close();

    foreach ($kriteria as &$k) {
        if (isset($_POST['status'][$k['ID_Kriteria']])) {
            $k['status'] = $_POST['status'][$k['ID_Kriteria']];
        }
    }
}

// Fetch kriteria
$stmt = $mysqli->prepare($q_kriteria);
$stmt->bind_param("s", $id_judul);
$stmt->execute();
$res_kriteria = $stmt->get_result();
$kriteria = [];
while ($row = $res_kriteria->fetch_assoc()) {
    if ($row['bobot'] !== null) $kriteria[] = $row;
}
$stmt->close();


/* ==========================================================
   3️⃣ Cek apakah masih ada kriteria tanpa status
========================================================== */
$needStatus = false;
foreach ($kriteria as $k) {
    if ($k['status'] == null || $k['status'] == '') {
        $needStatus = true;
        break;
    }
}

/* ==========================================================
   4️⃣ Jika masih ada yang kosong → tampilkan dropdown form
========================================================== */
if (!$isSubmitted && $needStatus):
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Set Benefit/Cost</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg,#667eea,#764ba2); margin: 0; padding: 20px; color: #333; }
        .container { background: #fff; border-radius: 20px; padding: 30px; max-width: 850px; margin: auto; box-shadow: 0 8px 25px rgba(0,0,0,0.2); }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        th { background: #f39c12; color: #fff; }
        .btn { background: #f39c12; color: white; padding: 10px 20px; border-radius: 10px; text-decoration: none; border: none; cursor: pointer; }
    </style>
</head>
<body>
<div class="container">
    <h2>Set Status Benefit / Cost</h2>
    <form method="POST">
        <table>
            <tr><th>Nama Kriteria</th><th>Bobot</th><th>Status</th></tr>
            <?php foreach ($kriteria as $k): 
                $status = $k['status'];
                $statusText = ($status === 'B') ? 'Benefit' : (($status === 'C') ? 'Cost' : '');
            ?>
                <tr>
                    <td><?= htmlspecialchars($k['nama_kriteria']) ?></td>
                    <td><?= number_format($k['bobot'], 6) ?></td>
                    <td>
                        <select name="status[<?= htmlspecialchars($k['ID_Kriteria']) ?>]" required>
                            <option value="">-- Pilih --</option>
                            <option value="B" <?= ($statusText == 'Benefit') ? 'selected' : '' ?>>Benefit</option>
                            <option value="C" <?= ($statusText == 'Cost') ? 'selected' : '' ?>>Cost</option>
                        </select>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <br>
        <button class="btn" type="submit">Simpan & Proses SAW</button>
    </form>
</div>
</body>
</html>
<?php
exit;
endif;

/* ==========================================================
   5️⃣ Ambil nilai alternatif per kriteria
========================================================== */
$q_alt = "
    SELECT a.ID_Alternatif, a.nama_alternatif, n.ID_Kriteria, n.nilai
    FROM alternatif a
    JOIN nilai_alternatif n ON a.ID_Alternatif = n.ID_Alternatif
    WHERE a.ID_Judul = ?
";
$stmt = $mysqli->prepare($q_alt);
$stmt->bind_param("s", $id_judul);
$stmt->execute();
$res_alt = $stmt->get_result();

$alternatif = [];
while ($row = $res_alt->fetch_assoc()) {
    $alt_id = $row['ID_Alternatif'];
    if (!isset($alternatif[$alt_id])) {
        $alternatif[$alt_id] = [
            'nama' => $row['nama_alternatif'],
            'nilai' => []
        ];
    }
    $alternatif[$alt_id]['nilai'][$row['ID_Kriteria']] = $row['nilai'];
}
$stmt->close();

if (count($alternatif) == 0) {
    echo "<p>Tidak ada data nilai alternatif.</p>";
    exit;
}

/* ==========================================================
   6️⃣ Normalisasi nilai (SAW berdasarkan B/C)
========================================================== */
$norm = [];
foreach ($kriteria as $k) {
    $idk = $k['ID_Kriteria'];
    $status = strtolower($k['status'] === 'C' ? 'cost' : 'benefit');
    $values = [];

    foreach ($alternatif as $a) {
        $values[] = isset($a['nilai'][$idk]) ? $a['nilai'][$idk] : 0;
    }

    $max = max($values);
    $min = min($values);

    foreach ($alternatif as $id_alt => $data) {
        $val = isset($data['nilai'][$idk]) ? $data['nilai'][$idk] : 0;
        if ($status == 'cost') {
            $norm[$id_alt][$idk] = ($val > 0) ? $min / $val : 0;
        } else {
            $norm[$id_alt][$idk] = ($max > 0) ? $val / $max : 0;
        }
    }
}

/* ==========================================================
   7️⃣ Hitung skor total SAW
========================================================== */
$hasil = [];
foreach ($alternatif as $id_alt => $data) {
    $skor = 0;
    foreach ($kriteria as $k) {
        $idk = $k['ID_Kriteria'];
        $bobot = $k['bobot'];
        $skor += (isset($norm[$id_alt][$idk]) ? $norm[$id_alt][$idk] : 0) * $bobot;
    }
    $hasil[$id_alt] = [
        'id'   => $id_alt,
        'nama' => $data['nama'],
        'skor' => $skor
    ];
}

/* ==========================================================
   🧩 7.5 Simpan nilai_akhir ke tabel alternatif
========================================================== */
$stmtSave = $mysqli->prepare("UPDATE alternatif SET nilai_akhir = ? WHERE ID_Alternatif = ? AND ID_Judul = ?");
foreach ($hasil as $row) {
    $stmtSave->bind_param("dss", $row['skor'], $row['id'], $id_judul);
    $stmtSave->execute();
}
$stmtSave->close();

/* ==========================================================
   8️⃣ Urutkan hasil descending
========================================================== */
usort($hasil, function($a, $b) {
    return $b['skor'] <=> $a['skor'];
});
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hasil Akhir AHP + SAW</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg,#667eea,#764ba2); margin: 0; padding: 20px; color: #333; }
        .container { background: #fff; border-radius: 20px; padding: 30px; max-width: 950px; margin: auto; box-shadow: 0 8px 25px rgba(0,0,0,0.2); text-align: center; }
        table { border-collapse: collapse; margin: 20px auto; width: 90%; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
        th { background: #f39c12; color: white; }
        td:first-child { text-align: left; }
        h2 { margin-bottom: 10px; }
        .btn { background: #f39c12; color: white; padding: 10px 20px; border-radius: 10px; text-decoration: none; font-size: 15px; transition: 0.3s; }
        .btn:hover { background: #e67e22; }
        .note { font-size: 14px; color: #555; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Hasil Akhir Perhitungan AHP + SAW</h2>
        <p><b>Judul:</b> <?= htmlspecialchars($judul) ?> (ID: <?= htmlspecialchars($id_judul) ?>)</p>

        <table>
            <tr>
                <th>Ranking</th>
                <th>Alternatif</th>
                <th>Skor Total</th>
            </tr>
            <?php 
            $rank = 1;
            foreach ($hasil as $row): ?>
                <tr>
                    <td><b><?= $rank++ ?></b></td>
                    <td><?= htmlspecialchars($row['nama']) ?></td>
                    <td><?= number_format($row['skor'], 4) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <p class="note">
            Skor di atas merupakan hasil gabungan: 
            <b>Bobot dari AHP</b> dan <b>Normalisasi nilai alternatif (SAW)</b>,
            dihitung menggunakan status <b>Benefit / Cost</b> (disimpan sebagai enum <b>B/C</b>).
            <br><br>
            Semua skor juga telah disimpan ke tabel <b>alternatif</b> dalam kolom <b>nilai_akhir</b>.
        </p>

        <a href="HalamanUtama.php" class="btn">← Kembali ke Halaman Utama</a>
    </div>
</body>
</html>
