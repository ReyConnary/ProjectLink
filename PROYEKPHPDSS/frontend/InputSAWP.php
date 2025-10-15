<?php
// Koneksi database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "perangkingan";

$id_judul = $_GET['id_judul'] ?? null;
$judul = $_GET['judul'] ?? null;

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil ID_Judul dari parameter GET (dibawa dari HalamanUtama.php)
if (!isset($_GET['id_judul'])) {
    die("ID_Judul tidak ditemukan. Silakan kembali ke Halaman Utama.");
}
$id_judul = $_GET['id_judul'];

// Ambil nama judul untuk ditampilkan
$res = $conn->prepare("SELECT namajudul FROM judul WHERE ID_Judul=?");
$res->bind_param("s", $id_judul);
$res->execute();
$res->bind_result($nama_judul);
$res->fetch();
$res->close();

$error_message = "";
$success_message = "";

// Tahap 1: Input jumlah kriteria & alternatif
if (isset($_POST['setJumlah'])) {
    $jumlah_kriteria = intval($_POST['jumlah_kriteria']);
    $jumlah_alternatif = intval($_POST['jumlah_alternatif']);
}

// Tahap 2: Simpan data input kriteria, alternatif, nilai
if (isset($_POST['simpanData'])) {
    $jumlah_kriteria = intval($_POST['jumlah_kriteria']);
    $jumlah_alternatif = intval($_POST['jumlah_alternatif']);

    // Simpan kriteria
    $kriteria_ids = [];
    for ($i = 0; $i < $jumlah_kriteria; $i++) {
        $nama = $_POST['nama_kriteria'][$i];
        $bobot = $_POST['bobot_kriteria'][$i];
        // ambil status baru — default 'B' kalau tidak dikirim
        $status = $_POST['status_kriteria'][$i] ?? 'B';
        $id_krt = "KRT-" . uniqid();
        $kriteria_ids[$i] = $id_krt;

        $stmt = $conn->prepare("INSERT INTO kriteria (ID_Kriteria, ID_Judul, nama_kriteria, bobot, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $id_krt, $id_judul, $nama, $bobot, $status);
        $stmt->execute();
        $stmt->close();
    }

    // Simpan alternatif dan nilai (tetap sama)
    for ($a = 0; $a < $jumlah_alternatif; $a++) {
        $nama_alt = $_POST['nama_alternatif'][$a];
        $id_alt = "ALT-" . uniqid();

        $stmt = $conn->prepare("INSERT INTO alternatif (ID_Alternatif, ID_Judul, nama_alternatif) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $id_alt, $id_judul, $nama_alt);
        $stmt->execute();
        $stmt->close();

        for ($k = 0; $k < $jumlah_kriteria; $k++) {
            $nilai = $_POST['nilai'][$a][$k];
            $id_nilai = "NIL-" . uniqid();

            $stmt = $conn->prepare("INSERT INTO nilai_alternatif (ID_Nilai, ID_Judul, ID_Alternatif, ID_Kriteria, nilai) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssd", $id_nilai, $id_judul, $id_alt, $kriteria_ids[$k], $nilai);
            $stmt->execute();
            $stmt->close();
        }
    }

    $success_message = "Data berhasil disimpan untuk judul: <b>$nama_judul</b>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Input SAW/WP - <?php echo $nama_judul; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea, #764ba2);
            margin: 0;
            padding: 20px;
        }
        .container {
            background: #fff;
            border-radius: 16px;
            padding: 30px;
            max-width: 900px;
            margin: auto;
            box-shadow: 0px 8px 25px rgba(0,0,0,0.2);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        input, select {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 8px;
            width: 100%;
            box-sizing: border-box;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        table td, table th {
            border: 1px solid #eee;
            padding: 10px;
            text-align: center;
        }
        button {
            background: #27ae60;
            border: none;
            padding: 12px 20px;
            border-radius: 10px;
            color: white;
            font-size: 15px;
            cursor: pointer;
            transition: 0.3s;
        }
        button:hover {
            background: #219150;
        }
        .success {
            background: #e0ffe5;
            color: #207a33;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .error {
            background: #ffe0e0;
            color: #b00020;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Input Data SAW/WP untuk Judul: <em><?php echo $nama_judul; ?></em></h2>
    <?php if ($error_message) echo "<div class='error'>$error_message</div>"; ?>

    <?php if (!empty($success_message)): ?>
    <div style="padding:10px; margin:10px 0; background:#d4edda; border:1px solid #c3e6cb; border-radius:5px; color:#155724;">
        <?= $success_message; ?>
    </div>

    <div style="margin-top:15px;">
        <form action="ProsesWP.php" method="get" style="display:inline-block; margin-right:10px;">
            <input type="hidden" name="id_judul" value="<?= htmlspecialchars($id_judul) ?>">
            <input type="hidden" name="judul" value="<?= htmlspecialchars($nama_judul) ?>">
            <button type="submit" style="padding:8px 16px; background:#007bff; color:white; border:none; border-radius:5px; cursor:pointer;">
                Lanjut dengan WP
            </button>
        </form>

        <form action="ProsesSAW.php" method="get" style="display:inline-block;">
            <input type="hidden" name="id_judul" value="<?= htmlspecialchars($id_judul) ?>">
            <input type="hidden" name="judul" value="<?= htmlspecialchars($nama_judul) ?>">
            <button type="submit" style="padding:8px 16px; background:#28a745; color:white; border:none; border-radius:5px; cursor:pointer;">
                Lanjut dengan SAW
            </button>
        </form>
    </div>
<?php endif; ?>


    <?php if (!isset($jumlah_kriteria) && !isset($jumlah_alternatif)) : ?>
        <!-- Form jumlah kriteria dan alternatif -->
        <form method="POST">
            <label>Jumlah Kriteria:</label>
            <input type="number" name="jumlah_kriteria" min="1" required>
            <br><br>
            <label>Jumlah Alternatif:</label>
            <input type="number" name="jumlah_alternatif" min="1" required>
            <br><br>
            <button type="submit" name="setJumlah">Lanjutkan</button>
        </form>

    <?php elseif (isset($jumlah_kriteria) && isset($jumlah_alternatif) && !isset($_POST['simpanData'])) : ?>
        <!-- Form input detail -->
        <form method="POST">
            <input type="hidden" name="jumlah_kriteria" value="<?php echo $jumlah_kriteria; ?>">
            <input type="hidden" name="jumlah_alternatif" value="<?php echo $jumlah_alternatif; ?>">

            <h3>Kriteria</h3>
            <table>
                <tr>
                    <th>Nama Kriteria</th>
                    <th>Bobot</th>
                    <th>Status</th>
                </tr>
                <?php for ($i = 0; $i < $jumlah_kriteria; $i++): ?>
                <tr>
                    <td><input type="text" name="nama_kriteria[]" required></td>
                    <td><input type="number" step="0.01" name="bobot_kriteria[]" required></td>
                    <td>
                        <select name="status_kriteria[]" required>
                            <option value="B">Benefit</option>
                            <option value="C">Cost</option>
                        </select>
                    </td>
                </tr>
                <?php endfor; ?>
            </table>

            <h3>Alternatif</h3>
            <table>
                <tr>
                    <th>Nama Alternatif</th>
                    <?php for ($k = 0; $k < $jumlah_kriteria; $k++): ?>
                        <th>Nilai untuk Kriteria <?php echo ($k+1); ?></th>
                    <?php endfor; ?>
                </tr>
                <?php for ($a = 0; $a < $jumlah_alternatif; $a++): ?>
                <tr>
                    <td><input type="text" name="nama_alternatif[]" required></td>
                    <?php for ($k = 0; $k < $jumlah_kriteria; $k++): ?>
                        <td><input type="number" step="0.01" name="nilai[<?php echo $a; ?>][<?php echo $k; ?>]" required></td>
                    <?php endfor; ?>
                </tr>
                <?php endfor; ?>
            </table>

            <button type="submit" name="simpanData">Simpan Data</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
