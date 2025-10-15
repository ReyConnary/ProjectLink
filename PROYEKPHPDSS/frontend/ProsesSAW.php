<?php
// ProsesSAW.php
// Menjalankan ProgramSAW.exe, parsing output [MATRIXX],[MATRIXR],[RESULT]
// Pastikan ProgramSAW.exe ada di folder yang sama

$host = "localhost";
$user = "root";
$pass = "";
$db   = "perangkingan";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

$id_judul = $_GET['id_judul'] ?? null;
if (!$id_judul) die("ID_Judul tidak ditemukan.");

$res = $conn->prepare("SELECT namajudul FROM judul WHERE ID_Judul=?");
$res->bind_param("s", $id_judul);
$res->execute();
$res->bind_result($nama_judul);
$res->fetch();
$res->close();

$isSubmitted = ($_SERVER['REQUEST_METHOD'] === 'POST');

// Ambil kriteria (termasuk status jika ada)
$kriteria = [];
$resKrt = $conn->query("SELECT ID_Kriteria, nama_kriteria, bobot, status FROM kriteria WHERE ID_Judul='$id_judul' ORDER BY ID_Kriteria");
while ($row = $resKrt->fetch_assoc()) $kriteria[] = $row;

// Ambil alternatif dan nilai sesuai urutan kriteria
$alternatif = [];
$resAlt = $conn->query("SELECT ID_Alternatif, nama_alternatif FROM alternatif WHERE ID_Judul='$id_judul' ORDER BY ID_Alternatif");
while ($alt = $resAlt->fetch_assoc()) {
    $id_alt = $alt['ID_Alternatif'];
    $alt['nilai'] = [];
    $resNilai = $conn->query("SELECT ID_Nilai, ID_Kriteria, nilai FROM nilai_alternatif WHERE ID_Alternatif='$id_alt' AND ID_Judul='$id_judul' ORDER BY ID_Kriteria");
    while ($n = $resNilai->fetch_assoc()) $alt['nilai'][] = $n['nilai'];
    $alternatif[] = $alt;
}

// HTML header / style (sesuai tema)
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Hasil SAW - <?= htmlspecialchars($nama_judul) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea, #764ba2);
            margin: 0;
            padding: 20px
        }

        .container {
            background: #fff;
            border-radius: 16px;
            padding: 30px;
            max-width: 1000px;
            margin: auto;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15)
        }

        h2 {
            text-align: center;
            margin-bottom: 12px
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 16px 0
        }

        th,
        td {
            border: 1px solid #eee;
            padding: 8px 10px;
            text-align: center
        }

        th {
            background: #f0f0f0
        }

        pre {
            background: #fafafa;
            padding: 12px;
            border-radius: 8px;
            white-space: pre-wrap;
            overflow: auto
        }

        .debug {
            background: #fff3cd;
            border: 1px solid #ffeeba;
            padding: 10px;
            margin-top: 18px
        }

        .btn {
            background: #27ae60;
            color: #fff;
            padding: 10px 16px;
            border-radius: 8px;
            border: none;
            cursor: pointer
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Hasil Perhitungan SAW<br><em><?= htmlspecialchars($nama_judul) ?></em></h2>

        <?php
        // Jika ada kriteria dengan status kosong/null, minta user set dulu.
        // Jika semua sudah ada status (atau InputSAWP sudah menaruh status), langsung proses.
        $needStatus = false;
        foreach ($kriteria as $k) if ($k['status'] === null || $k['status'] === '') $needStatus = true;

        if (!$isSubmitted && $needStatus):
            // Tampilkan form untuk set status
        ?>
            <form method="POST">
                <h3>Set Status Kriteria (Benefit / Cost)</h3>
                <table>
                    <tr>
                        <th>Nama Kriteria</th>
                        <th>Bobot</th>
                        <th>Status</th>
                    </tr>
                    <?php foreach ($kriteria as $k): ?>
                        <tr>
                            <td><?= htmlspecialchars($k['nama_kriteria']) ?></td>
                            <td><?= htmlspecialchars($k['bobot']) ?></td>
                            <td>
                                <select name="status[<?= htmlspecialchars($k['ID_Kriteria']) ?>]" required>
                                    <option value="B" <?= ($k['status'] == 'B') ? 'selected' : '' ?>>Benefit</option>
                                    <option value="C" <?= ($k['status'] == 'C') ? 'selected' : '' ?>>Cost</option>
                                </select>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                <button class="btn" type="submit">Simpan & Proses SAW</button>
            </form>
        <?php else:
            // Jika POST membawa status (dari form), simpan ke DB dulu
            if (!empty($_POST['status']) && is_array($_POST['status'])) {
                foreach ($_POST['status'] as $idk => $st) {
                    // Simpan status ke kolom status pada kriteria
                    $stmt = $conn->prepare("UPDATE kriteria SET status=? WHERE ID_Kriteria=? AND ID_Judul=?");
                    $stmt->bind_param("sss", $st, $idk, $id_judul);
                    $stmt->execute();
                    $stmt->close();
                }
                // reload kriteria
                $kriteria = [];
                $resKrt = $conn->query("SELECT ID_Kriteria, nama_kriteria, bobot, status FROM kriteria WHERE ID_Judul='$id_judul' ORDER BY ID_Kriteria");
                while ($row = $resKrt->fetch_assoc()) $kriteria[] = $row;
            }

            // Susun input persis urutan yang ProgramSAW.cpp harapkan:
            // jumlah_kriteria
            // nama_kriteria1
            // bobot1
            // status1 (B/C)
            // ...
            // jumlah_alternatif
            // nama_alt1
            // nilai_k1_for_alt1
            // nilai_k2_for_alt1
            // ...
            $input = "";
            $input .= count($kriteria) . PHP_EOL;
            foreach ($kriteria as $k) {
                $input .= trim($k['nama_kriteria']) . PHP_EOL;
                $input .= trim((string)$k['bobot']) . PHP_EOL;
                $input .= (($k['status'] !== null && $k['status'] !== '') ? $k['status'] : 'B') . PHP_EOL;
            }
            // jumlah alternatif
            $input .= count($alternatif) . PHP_EOL;

            // untuk setiap alternatif: tulis nama lalu nilai-nilai kriteria langsung
            foreach ($alternatif as $alt) {
                $input .= trim($alt['nama_alternatif']) . PHP_EOL;
                foreach ($alt['nilai'] as $v) {
                    $input .= trim((string)$v) . PHP_EOL;
                }
            }

            // Jalankan ProgramSAW.exe via proc_open
            $exePath = __DIR__ . DIRECTORY_SEPARATOR . "ProgramSAW.exe";
            $output = '';
            $stderr = '';
            $return_value = null;

            if (!file_exists($exePath)) {
                $stderr = "ProgramSAW.exe tidak ditemukan di: $exePath";
            } else {
                $descriptorspec = [
                    0 => ["pipe", "r"], // stdin
                    1 => ["pipe", "w"], // stdout
                    2 => ["pipe", "w"]  // stderr
                ];
                $process = proc_open($exePath, $descriptorspec, $pipes);
                if (is_resource($process)) {
                    fwrite($pipes[0], $input);
                    fclose($pipes[0]);
                    $output = stream_get_contents($pipes[1]);
                    fclose($pipes[1]);
                    $stderr = stream_get_contents($pipes[2]);
                    fclose($pipes[2]);
                    $return_value = proc_close($process);
                } else {
                    $stderr = "proc_open gagal dijalankan.";
                }
            }

            // Normalize newlines
            $output = str_replace(["\r\n", "\r"], "\n", $output);

            // Parsing tags: [RESULT],[MATRIXX],[MATRIXR]
            $hasil = [];
            $matrixX = [];
            $matrixR = [];
            foreach (explode("\n", trim($output)) as $line) {
                $line = trim($line);
                if ($line === '') continue;

                if (strpos($line, "[RESULT]") === 0) {
                    $p = array_map('trim', explode(",", $line));
                    if (count($p) >= 4) $hasil[] = ['rank' => $p[1], 'nama' => $p[2], 'skor' => $p[3]];
                    continue;
                }
                if (strpos($line, "[MATRIXX]") === 0) {
                    $p = explode(",", $line);
                    if (count($p) >= 3) $matrixX[trim($p[1])] = array_map('trim', array_slice($p, 2));
                    continue;
                }
                if (strpos($line, "[MATRIXR]") === 0) {
                    $p = explode(",", $line);
                    if (count($p) >= 3) $matrixR[trim($p[1])] = array_map('trim', array_slice($p, 2));
                    continue;
                }
            }

            // ---------- Simpan nilai_akhir ke tabel 'alternatif' ----------
            if (!empty($hasil)) {
                // mulai transaction agar atomic
                $conn->begin_transaction();

                try {
                    // prepared statement untuk cari ID_Alternatif berdasarkan nama & id_judul
                    $stmtFind = $conn->prepare("SELECT ID_Alternatif FROM alternatif WHERE nama_alternatif = ? AND ID_Judul = ? LIMIT 1");
                    // prepared statement untuk update nilai_akhir pada tabel alternatif
                    $stmtUpd  = $conn->prepare("UPDATE alternatif SET nilai_akhir = ? WHERE ID_Alternatif = ? AND ID_Judul = ?");

                    foreach ($hasil as $h) {
                        $nama_alt = $h['nama'];
                        $skor = (float)$h['skor'];

                        // cari ID_Alternatif
                        $stmtFind->bind_param("ss", $nama_alt, $id_judul);
                        $stmtFind->execute();
                        $res = $stmtFind->get_result();

                        if ($row = $res->fetch_assoc()) {
                            $idAlt = $row['ID_Alternatif'];
                            // update alternatif.nilai_akhir
                            $stmtUpd->bind_param("dss", $skor, $idAlt, $id_judul);
                            $stmtUpd->execute();
                        } else {
                            // opsional: log/handle bila nama alternatif tidak ditemukan
                            // error_log("Alternatif '$nama_alt' tidak ditemukan untuk ID_Judul $id_judul");
                        }
                    }

                    $stmtFind->close();
                    $stmtUpd->close();

                    $conn->commit();
                } catch (Exception $e) {
                    $conn->rollback();
                    // opsional: simpan ke log error
                    error_log("Gagal menyimpan nilai_akhir: " . $e->getMessage());
                }
            }

            // Tampilkan hasil ke user (tabel)
        ?>
            <h3>Kriteria & Bobot</h3>
            <table>
                <tr>
                    <th>Nama Kriteria</th>
                    <th>Bobot</th>
                    <th>Status</th>
                </tr>
                <?php foreach ($kriteria as $k): ?>
                    <tr>
                        <td><?= htmlspecialchars($k['nama_kriteria']) ?></td>
                        <td><?= htmlspecialchars($k['bobot']) ?></td>
                        <td><?= ($k['status'] == 'C') ? 'Cost' : 'Benefit' ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <h3>Matriks Keputusan</h3>
            <table>
                <tr>
                    <th>Alternatif</th><?php foreach ($kriteria as $k): ?><th><?= htmlspecialchars($k['nama_kriteria']) ?></th><?php endforeach; ?>
                </tr>
                <?php foreach ($alternatif as $alt): ?>
                    <tr>
                        <td><?= htmlspecialchars($alt['nama_alternatif']) ?></td><?php foreach ($alt['nilai'] as $v): ?><td><?= htmlspecialchars($v) ?></td><?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </table>

            <?php if (!empty($matrixR)): ?>
                <h3>Matriks Normalisasi (R)</h3>
                <table>
                    <tr>
                        <th>Alternatif</th><?php foreach ($kriteria as $k): ?><th><?= htmlspecialchars($k['nama_kriteria']) ?></th><?php endforeach; ?>
                    </tr>
                    <?php foreach ($matrixR as $altName => $vals): ?>
                        <tr>
                            <td><?= htmlspecialchars($altName) ?></td><?php foreach ($vals as $vv): ?><td><?= htmlspecialchars($vv) ?></td><?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>

            <?php if (!empty($hasil)): ?>
                <h3>Hasil Akhir & Ranking</h3>
                <table>
                    <tr>
                        <th>Rank</th>
                        <th>Alternatif</th>
                        <th>Nilai Akhir</th>
                    </tr>
                    <?php foreach ($hasil as $h): ?>
                        <tr>
                            <td><?= htmlspecialchars($h['rank']) ?></td>
                            <td><?= htmlspecialchars($h['nama']) ?></td>
                            <td><?= htmlspecialchars($h['skor']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>

        <?php endif; // end isSubmitted 
        ?>
        <!-- Tombol kembali ke halaman utama -->
<div style="text-align:center; margin-top:20px;">
    <a href="HalamanUtama.php" class="btn">Kembali ke Halaman Utama</a>
</div>


    </div>
</body>

</html>