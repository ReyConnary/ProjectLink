<?php
// ProsesWP.php - Menggunakan Metode Weighted Product (WP)
// Menjalankan ProgramWP.exe, parsing output [MATRIXX],[MATRIXR],[RESULT]
// Pastikan ProgramWP.exe ada di folder yang sama

$host = "localhost";
$user = "root";
$pass = "";
$db   = "perangkingan";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

// 1. Ambil ID_Judul dan Validasi
$id_judul = $_GET['id_judul'] ?? null;
// Gunakan prepared statement untuk keamanan
$stmtCheck = $conn->prepare("SELECT namajudul FROM judul WHERE ID_Judul=?");
$stmtCheck->bind_param("s", $id_judul);
$stmtCheck->execute();
$resCheck = $stmtCheck->get_result();

if (!$id_judul || $resCheck->num_rows === 0) {
    $stmtCheck->close();
    die("ID_Judul tidak valid atau tidak ditemukan.");
}

$nama_judul = $resCheck->fetch_assoc()['namajudul'];
$stmtCheck->close();


$isSubmitted = ($_SERVER['REQUEST_METHOD'] === 'POST');

// -----------------------------
// 2. Ambil Kriteria + Bobot + Status
// -----------------------------
$kriteria = [];
$stmtK = $conn->prepare("
    SELECT k.ID_Kriteria, k.nama_kriteria,
           COALESCE(bk.bobot, k.bobot, 0) AS bobot,
           k.status
    FROM kriteria k
    LEFT JOIN ahp_bobot_kriteria bk
      ON bk.id_kriteria = k.ID_Kriteria AND bk.id_judul = ?
    WHERE k.ID_Judul = ?
    ORDER BY k.ID_Kriteria
");
$stmtK->bind_param("ss", $id_judul, $id_judul);
$stmtK->execute();
$resKrt = $stmtK->get_result();
while ($row = $resKrt->fetch_assoc()) {
    $row['bobot'] = (float)$row['bobot']; // Pastikan bobot adalah float
    $kriteria[] = $row;
}
$stmtK->close();

// Mapping Kriteria: ID_Kriteria -> Index (untuk array nilai)
$kriteria_map = array_column($kriteria, 'ID_Kriteria');

// -----------------------------
// 3. Ambil Alternatif
// -----------------------------
$alternatif_data = []; // ID_Alternatif => Data
$alternatif = []; // Array berurutan
$stmtAlt = $conn->prepare("SELECT ID_Alternatif, nama_alternatif FROM alternatif WHERE ID_Judul=? ORDER BY ID_Alternatif");
$stmtAlt->bind_param("s", $id_judul);
$stmtAlt->execute();
$resAlt = $stmtAlt->get_result();

while ($alt = $resAlt->fetch_assoc()) {
    // Inisialisasi array nilai dengan 0, sesuai jumlah kriteria
    $alt['nilai'] = array_fill(0, count($kriteria), 0); 
    $alternatif_data[$alt['ID_Alternatif']] = $alt;
}
$stmtAlt->close();

// -----------------------------
// 4. Ambil Semua Nilai Alternatif dalam 1 Kueri (Optimasi)
// -----------------------------
if (!empty($alternatif_data)) {
    $stmtValAll = $conn->prepare("
        SELECT ID_Alternatif, ID_Kriteria, nilai
        FROM nilai_alternatif
        WHERE ID_Judul=?
    ");
    $stmtValAll->bind_param("s", $id_judul);
    $stmtValAll->execute();
    $resValAll = $stmtValAll->get_result();
    
    while ($r = $resValAll->fetch_assoc()) {
        $id_alt = $r['ID_Alternatif'];
        $id_krt = $r['ID_Kriteria'];
        $nilai = $r['nilai'];
        
        // Cari index kriteria
        $krt_index = array_search($id_krt, $kriteria_map);
        
        if ($krt_index !== false && isset($alternatif_data[$id_alt])) {
            $alternatif_data[$id_alt]['nilai'][$krt_index] = $nilai;
        }
    }
    $stmtValAll->close();
    
    // Konversi array map ke array berurutan untuk ditampilkan
    $alternatif = array_values($alternatif_data);
}

// -----------------------------
// 5. HTML dan Logic WP
// -----------------------------
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Weighted Product - <?= htmlspecialchars($nama_judul) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #667eea, #764ba2); margin: 0; padding: 20px; }
        .container { background: #fff; border-radius: 16px; padding: 30px; max-width: 1000px; margin: auto; box-shadow: 0 8px 25px rgba(0,0,0,0.15) }
        h2 { text-align: center; margin-bottom: 12px }
        table { width: 100%; border-collapse: collapse; margin: 16px 0 }
        th, td { border: 1px solid #eee; padding: 8px 10px; text-align: center }
        th { background: #f0f0f0 }
        .btn { background: #007bff; color: #fff; padding: 10px 16px; border-radius: 8px; border: none; cursor: pointer }
        /* Debug dihapus dari output */
    </style>
</head>
<body>
    <div class="container">
        <h2>Hasil Perhitungan WP<br><em><?= htmlspecialchars($nama_judul) ?></em></h2>

<?php
// Cek apakah ada kriteria tanpa status
$needStatus = false;
foreach ($kriteria as $k) if ($k['status'] === null || $k['status'] === '') $needStatus = true;

if (!$isSubmitted && $needStatus):
    // Tampilkan form set status
?>
    <form method="POST">
        <h3>Set Status Kriteria (Benefit / Cost)</h3>
        <table>
            <tr><th>Nama Kriteria</th><th>Bobot</th><th>Status</th></tr>
            <?php foreach ($kriteria as $k): ?>
                <tr>
                    <td><?= htmlspecialchars($k['nama_kriteria']) ?></td>
                    <td><?= number_format((float)$k['bobot'], 6) ?></td>
                    <td>
                        <select name="status[<?= htmlspecialchars($k['ID_Kriteria']) ?>]" required>
                            <option value="B" <?= ($k['status'] == 'B') ? 'selected' : '' ?>>Benefit</option>
                            <option value="C" <?= ($k['status'] == 'C') ? 'selected' : '' ?>>Cost</option>
                        </select>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <button class="btn" type="submit">Simpan & Proses WP</button>
    </form>

<?php else:
// Jika form dikirim, simpan status (jika ada) dan proses WP
    if (!empty($_POST['status']) && is_array($_POST['status'])) {
        $stmtUpdStatus = $conn->prepare("UPDATE kriteria SET status=? WHERE ID_Kriteria=? AND ID_Judul=?");
        $conn->begin_transaction();
        try {
            foreach ($kriteria as &$k) { // Gunakan referensi untuk update array kriteria
                $idk = $k['ID_Kriteria'];
                if (isset($_POST['status'][$idk])) {
                    $st = $_POST['status'][$idk];
                    $stmtUpdStatus->bind_param("sss", $st, $idk, $id_judul);
                    $stmtUpdStatus->execute();
                    $k['status'] = $st; // Update status di array kriteria
                }
            }
            unset($k);
            $conn->commit();
        } catch (Exception $e) {
            $conn->rollback();
            error_log("Gagal menyimpan status kriteria: " . $e->getMessage());
        }
        $stmtUpdStatus->close();
    }

    // --------- Susun input untuk ProgramWP.exe ----------
    $input = "";
    // Jumlah alternatif
    $input .= count($alternatif) . PHP_EOL;
    // Jumlah kriteria
    $input .= count($kriteria) . PHP_EOL;

    // Nama alternatif
    foreach ($alternatif as $alt) $input .= trim($alt['nama_alternatif']) . PHP_EOL;

    // Data kriteria
    foreach ($kriteria as $k) {
        $input .= trim($k['nama_kriteria']) . PHP_EOL;
        $input .= trim((string)$k['bobot']) . PHP_EOL;
        $input .= (($k['status'] !== null && $k['status'] !== '') ? $k['status'] : 'B') . PHP_EOL;
    }

    // Nilai-nilai alternatif
    foreach ($alternatif as $alt) {
        foreach ($alt['nilai'] as $v) $input .= trim((string)$v) . PHP_EOL;
    }

    // Jalankan ProgramWP.exe via proc_open
    $exePath = __DIR__ . DIRECTORY_SEPARATOR . "ProgramWP.exe";
    $output = '';
    $stderr = '';
    $return_value = null; // Tetap dipertahankan untuk kebutuhan eksekusi, tapi tidak ditampilkan.

    if (!file_exists($exePath)) {
        $stderr = "ProgramWP.exe tidak ditemukan di: $exePath";
    } else {
        $descriptorspec = [0 => ["pipe", "r"], 1 => ["pipe", "w"], 2 => ["pipe", "w"]];
        $process = proc_open($exePath, $descriptorspec, $pipes);
        if (is_resource($process)) {
            fwrite($pipes[0], $input);
            fclose($pipes[0]);

            $output = stream_get_contents($pipes[1]);
            fclose($pipes[1]);
            $stderr  = stream_get_contents($pipes[2]);
            fclose($pipes[2]);

            $return_value = proc_close($process);
        } else {
            $stderr = "proc_open gagal dijalankan.";
        }
    }

    // normalisasi newline
    $output = str_replace(["\r\n", "\r"], "\n", $output);

    // Parsing output dari exe
    $hasil = [];
    $matrixR = []; // Matriks S (Vektor S)
    $matrixX = []; // Matriks Keputusan (X)
    foreach (explode("\n", trim($output)) as $line) {
        $line = trim($line);
        if ($line === '') continue;
        if (strpos($line, "[RESULT]") === 0) {
            $p = array_map('trim', explode(",", $line));
            if (count($p) >= 4) $hasil[] = ['rank' => $p[1], 'nama' => $p[2], 'skor' => $p[3]];
            continue;
        }
        if (strpos($line, "[MATRIXR]") === 0) {
            $p = explode(",", $line);
            if (count($p) >= 3) $matrixR[trim($p[1])] = array_map('trim', array_slice($p, 2));
            continue;
        }
        if (strpos($line, "[MATRIXX]") === 0) {
            $p = explode(",", $line);
            if (count($p) >= 3) $matrixX[trim($p[1])] = array_map('trim', array_slice($p, 2));
            continue;
        }
    }

    // ---------- Simpan nilai_akhir ke tabel 'alternatif' ----------
    if (!empty($hasil)) {
        $conn->begin_transaction();
        
        $stmtFind = $conn->prepare("SELECT ID_Alternatif FROM alternatif WHERE nama_alternatif = ? AND ID_Judul = ? LIMIT 1");
        $stmtUpd  = $conn->prepare("UPDATE alternatif SET nilai_akhir = ? WHERE ID_Alternatif = ? AND ID_Judul = ?");

        try {
            foreach ($hasil as $h) {
                $nama_alt = $h['nama'];
                $skor = (float)$h['skor'];

                $stmtFind->bind_param("ss", $nama_alt, $id_judul);
                $stmtFind->execute();
                $res = $stmtFind->get_result();

                if ($row = $res->fetch_assoc()) {
                    $idAlt = $row['ID_Alternatif'];
                    $stmtUpd->bind_param("dss", $skor, $idAlt, $id_judul);
                    $stmtUpd->execute();
                }
            }

            $stmtFind->close();
            $stmtUpd->close();
            $conn->commit();
        } catch (Exception $e) {
            $conn->rollback();
            error_log("Gagal menyimpan nilai_akhir: " . $e->getMessage());
        }
    }

    // ---------------- TAMPILKAN HASIL ----------------
    ?>
    <h3>Kriteria & Bobot</h3>
    <table>
        <tr><th>Nama Kriteria</th><th>Bobot</th><th>Status</th></tr>
        <?php foreach ($kriteria as $k): ?>
            <tr>
                <td><?= htmlspecialchars($k['nama_kriteria']) ?></td>
                <td><?= number_format((float)$k['bobot'], 6) ?></td>
                <td><?= ($k['status'] == 'C') ? 'Cost' : 'Benefit' ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <h3>Nilai Alternatif (Matriks Keputusan)</h3>
    <table>
        <tr>
            <th>Alternatif</th><?php foreach ($kriteria as $k): ?><th><?= htmlspecialchars($k['nama_kriteria']) ?></th><?php endforeach; ?>
        </tr>
        <?php 
        // Gunakan $alternatif_data jika program WP.exe gagal mengeluarkan MatrixX
        $displayMatrix = empty($matrixX) ? $alternatif : $alternatif_data; 
        
        foreach ($displayMatrix as $alt): 
            $altName = isset($alt['nama']) ? $alt['nama'] : $alt['nama_alternatif'];
            $altValues = isset($alt['nilai']) ? $alt['nilai'] : $alt;
        ?>
            <tr>
                <td><?= htmlspecialchars($altName) ?></td>
                <?php 
                // Jika dari DB (tanpa $matrixX), gunakan $alt['nilai']
                if (isset($alt['nilai'])):
                    foreach ($alt['nilai'] as $v): ?><td><?= htmlspecialchars($v) ?></td><?php endforeach; 
                // Jika dari $matrixX
                elseif (isset($matrixX[$altName])):
                    foreach ($matrixX[$altName] as $vv): ?><td><?= htmlspecialchars($vv) ?></td><?php endforeach;
                endif;
                ?>
            </tr>
        <?php endforeach; ?>
    </table>


    <?php if (!empty($matrixR)): ?>
        <h3>Vektor S (Matriks Pangkat Bobot)</h3>
        <table>
            <tr><th>Alternatif</th><?php foreach ($kriteria as $k): ?><th><?= htmlspecialchars($k['nama_kriteria']) ?></th><?php endforeach; ?></tr>
            <?php foreach ($matrixR as $altName => $vals): ?>
                <tr>
                    <td><?= htmlspecialchars($altName) ?></td><?php foreach ($vals as $vv): ?><td><?= htmlspecialchars($vv) ?></td><?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <?php if (!empty($hasil)): ?>
        <h3>Hasil Akhir & Ranking (Vektor V)</h3>
        <table>
            <tr><th>Rank</th><th>Alternatif</th><th>Nilai Akhir</th></tr>
            <?php foreach ($hasil as $h): ?>
                <tr>
                    <td><?= htmlspecialchars($h['rank']) ?></td>
                    <td><?= htmlspecialchars($h['nama']) ?></td>
                    <td><?= htmlspecialchars($h['skor']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

<?php endif; // end if needStatus/isSubmitted ?>
<!-- Tombol kembali ke halaman utama -->
<div style="text-align:center; margin-top:20px;">
    <a href="HalamanUtama.php" class="btn">Kembali ke Halaman Utama</a>
</div>

    </div>
</body>
</html>