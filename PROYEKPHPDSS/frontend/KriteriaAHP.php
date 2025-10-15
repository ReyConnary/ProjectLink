<?php
// Aktifkan error display untuk debug
ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = "localhost"; $user = "root"; $pass = ""; $db = "perangkingan";
$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_error) die("Koneksi DB gagal: " . $mysqli->connect_error);

$id_judul = $_GET['id_judul'] ?? '';
$judul = $_GET['judul'] ?? '';

if ($id_judul === '') {
    echo 'id_judul tidak ditemukan. <a href="HalamanUtama.php">Kembali ke HalamanUtama</a>.';
    exit;
}

$kriteria = [];
$st = $mysqli->prepare("SELECT ID_Kriteria, nama_kriteria FROM kriteria WHERE ID_Judul=? ORDER BY ID_Kriteria");
$st->bind_param("s", $id_judul);
$st->execute();
$res = $st->get_result();
while ($row = $res->fetch_assoc()) {
    $kriteria[] = $row;
}
$st->close();

$n = count($kriteria);
if ($n < 2) {
    echo "Minimal harus ada 2 kriteria pada judul ini. <a href='InputAHP.php?id_judul=" . urlencode($id_judul) . "&judul=" . urlencode($judul) . "'>Kembali input kriteria</a>";
    exit;
}

// Helper: parse angka/pecahan a/b
function parse_frac($s) {
    $s = trim($s);
    if ($s === '') return null;
    if (strpos($s, '/') !== false) {
        [$a, $b] = array_map('trim', explode('/', $s, 2));
        $a = (float)$a; $b = (float)$b;
        return ($b != 0) ? $a / $b : null;
    }
    return (float)$s;
}

$bobot = null;
$msg = "";
$M = array_fill(0, $n, array_fill(0, $n, 0.0)); // Inisialisasi matriks
$ok = false;
$errs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ok = true;
    for ($i = 0; $i < $n; $i++) {
        for ($j = 0; $j < $n; $j++) {
            if ($i === $j) {
                $M[$i][$j] = 1.0;
            } elseif ($i < $j) {
                $name = "val_{$i}_{$j}";
                $val = parse_frac($_POST[$name] ?? '');
                if ($val === null || $val <= 0) {
                    $errs[] = "Nilai invalid di [$i,$j]: {$_POST[$name]}";
                    $ok = false;
                } else {
                    $M[$i][$j] = $val;
                    $M[$j][$i] = 1 / $val;
                }
            }
        }
    }

    if ($ok) {
        // Simpan matriks ke ahp_pairwise_comparison
        for ($i = 0; $i < $n; $i++) {
            for ($j = $i + 1; $j < $n; $j++) {
                $stmt = $mysqli->prepare("INSERT INTO ahp_pairwise_comparison (id_judul, id_kriteria1, id_kriteria2, nilai) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE nilai = VALUES(nilai)");
                $stmt->bind_param("sssd", $id_judul, $kriteria[$i]['ID_Kriteria'], $kriteria[$j]['ID_Kriteria'], $M[$i][$j]);
                $stmt->execute();
                $stmt->close();
            }
        }

        // Hitung squared matrix (M^2)
        $M2 = array_fill(0, $n, array_fill(0, $n, 0.0));
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                for ($k = 0; $k < $n; $k++) {
                    $M2[$i][$j] += $M[$i][$k] * $M[$k][$j];
                }
            }
        }

        // Row sum dari M2
        $row_sums = array_fill(0, $n, 0.0);
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $row_sums[$i] += $M2[$i][$j];
            }
        }

        // Total sum
        $total_sum = array_sum($row_sums);

        // Normalize untuk eigenvector (bobot)
        $bobot = [];
        for ($i = 0; $i < $n; $i++) {
            $bobot[$i] = ($total_sum > 0) ? $row_sums[$i] / $total_sum : 0;
        }

        // Simpan bobot ke db
        foreach ($bobot as $i => $bob) {
            $id_krt = $kriteria[$i]['ID_Kriteria'];
            $stmt = $mysqli->prepare("INSERT INTO ahp_bobot_kriteria (id_judul, id_kriteria, bobot) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE bobot=?");
            $stmt->bind_param("ssdd", $id_judul, $id_krt, $bob, $bob);
            $stmt->execute();
            $stmt->close();
        }

        $msg = "<div class='success'><b>Bobot kriteria tersimpan.</b></div>";
    } else {
        $msg = "<div class='error'>Error: " . implode(', ', $errs) . "</div>";
    }
}
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>AHP – Pairwise Kriteria</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #667eea, #764ba2); margin: 0; padding: 20px; color: #333; }
        .container { background: #fff; border-radius: 20px; padding: 30px; max-width: 900px; margin: auto; box-shadow: 0px 8px 25px rgba(0,0,0,0.2); text-align: center; }
        h2 { margin-bottom: 20px; font-weight: 600; }
        p { margin-bottom: 15px; }
        table { border-collapse: collapse; margin: 20px auto; }
        th, td { border: 1px solid #ddd; padding: 8px 10px; text-align: center; }
        th:first-child, td:first-child { text-align: left; }
        input[type=text] { width: 90px; text-align: center; padding: 8px; border-radius: 8px; border: 1px solid #ddd; }
        .note { font-size: 12px; color: #666; }
        button { background: #f39c12; border: none; padding: 12px 20px; border-radius: 10px; color: white; font-size: 15px; cursor: pointer; transition: 0.3s; margin-top: 20px; }
        button:hover { background: #e67e22; }
        .success { background: #e0ffe5; color: #207a33; padding: 10px; border-radius: 8px; margin-bottom: 20px; }
        .error { background: #ffe0e0; color: #b00020; padding: 10px; border-radius: 8px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>AHP – Pairwise Comparison Kriteria</h2>
        <p><b>Judul:</b> <?= htmlspecialchars($judul) ?> (ID: <?= htmlspecialchars($id_judul) ?>)</p>

        <?= $msg ?>

        <form method="post">
            <table>
                <tr><th></th>
                    <?php for ($j = 0; $j < $n; $j++): ?>
                        <th><?= htmlspecialchars($kriteria[$j]['nama_kriteria']) ?></th>
                    <?php endfor; ?>
                </tr>
                <?php for ($i = 0; $i < $n; $i++): ?>
                    <tr>
                        <th><?= htmlspecialchars($kriteria[$i]['nama_kriteria']) ?></th>
                        <?php for ($j = 0; $j < $n; $j++): ?>
                            <td>
                                <?php if ($i === $j): ?>1
                                <?php elseif ($i < $j): 
                                    $name = "val_{$i}_{$j}";
                                    $val = isset($_POST[$name]) ? htmlspecialchars($_POST[$name]) : "";
                                ?>
                                    <input type="text" name="<?= $name ?>" value="<?= $val ?>" placeholder="3 atau 1/3" required>
                                <?php else: ?>
                                    <span class="note">= 1 / (<?= "val_{$j}_{$i}" ?>)</span>
                                <?php endif; ?>
                            </td>
                        <?php endfor; ?>
                    </tr>
                <?php endfor; ?>
            </table>

            <p class="note">Isi hanya bagian atas matriks (diagonal = 1). Nilai boleh desimal (mis. 2, 0.5) atau pecahan (mis. 1/3).</p>
            <button type="submit">Hitung &amp; Simpan Bobot</button>
        </form>

        <?php if (is_array($bobot)): ?>
            <h3>Matriks Eigenvector</h3>
            <table>
                <tr><th>Kriteria</th><th>Eigenvector (Bobot)</th></tr>
                <?php for ($i = 0; $i < $n; $i++): ?>
                    <tr>
                        <td><?= htmlspecialchars($kriteria[$i]['nama_kriteria']) ?></td>
                        <td><?= number_format($bobot[$i], 4) ?></td>
                    </tr>
                <?php endfor; ?>
            </table>
            <p><a href="HalamanUtama.php" style="color: #f39c12;">Kembali ke Halaman Utama</a></p>
            <button onclick="window.location.href='HasilBobotAHP.php?id_judul=<?= urlencode($id_judul) ?>&judul=<?= urlencode($judul) ?>'">Lihat Hasil Bobot & Pilih Metode Lanjut</button>
        <?php endif; ?>
    </div>
</body>
</html>