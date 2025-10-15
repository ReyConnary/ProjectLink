<?php
// =================== KONEKSI DB ===================
$host = "localhost";
$user = "root";
$pass = "";
$db   = "perangkingan";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);
$conn->set_charset('utf8mb4');

// =================== PARAMETER ===================
$id_judul = $_GET['id_judul'] ?? '';
$judul    = $_GET['judul'] ?? '';

if ($id_judul === '') {
    echo 'ID_Judul tidak ditemukan. <a href="HalamanUtama.php">Kembali ke Halaman Utama</a>.';
    exit;
}

// Ambil nama judul
$res = $conn->prepare("SELECT namajudul FROM judul WHERE ID_Judul=?");
$res->bind_param("s", $id_judul);
$res->execute();
$res->bind_result($nama_judul);
$res->fetch();
$res->close();

$error_message   = "";
$success_message = "";

// =================== TAHAP 1: SET JUMLAH ===================
if (isset($_POST['setJumlah'])) {
    $jumlah_kriteria   = intval($_POST['jumlah_kriteria']);
    $jumlah_alternatif = intval($_POST['jumlah_alternatif']);
}

// =================== TAHAP 2: SIMPAN DATA ===================
if (isset($_POST['simpanData'])) {
    $jumlah_kriteria   = intval($_POST['jumlah_kriteria']);
    $jumlah_alternatif = intval($_POST['jumlah_alternatif']);
    $mode_ahp_full     = $_POST['mode_ahp_full'] ?? 'tidak';

    // Simpan kriteria (bobot default 0)
    $kriteria_ids = [];
    for ($i = 0; $i < $jumlah_kriteria; $i++) {
        $nama = trim($_POST['nama_kriteria'][$i] ?? '');
        if ($nama !== '') {
            $id_krt = "KRT-" . uniqid();
            $kriteria_ids[$i] = $id_krt;

            $stmt = $conn->prepare("INSERT INTO kriteria (ID_Kriteria, ID_Judul, nama_kriteria, bobot) VALUES (?, ?, ?, 0)");
            $stmt->bind_param("sss", $id_krt, $id_judul, $nama);
            $stmt->execute();
            $stmt->close();
        }
    }

    // Simpan alternatif
    for ($a = 0; $a < $jumlah_alternatif; $a++) {
        $nama_alt = trim($_POST['nama_alternatif'][$a] ?? '');
        if ($nama_alt !== '') {
            $id_alt = "ALT-" . uniqid();
            $stmt = $conn->prepare("INSERT INTO alternatif (ID_Alternatif, ID_Judul, nama_alternatif) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $id_alt, $id_judul, $nama_alt);
            $stmt->execute();
            $stmt->close();

            // Jika bukan mode AHP Full, simpan nilai manual
            if ($mode_ahp_full !== 'ya') {
                for ($k = 0; $k < $jumlah_kriteria; $k++) {
                    $nilai = floatval(str_replace(',', '.', $_POST['nilai'][$a][$k] ?? 0));
                    $id_nilai = "NIL-" . uniqid();
                    $stmt = $conn->prepare("INSERT INTO nilai_alternatif (ID_Nilai, ID_Judul, ID_Alternatif, ID_Kriteria, nilai) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssd", $id_nilai, $id_judul, $id_alt, $kriteria_ids[$k], $nilai);
                    $stmt->execute();
                    $stmt->close();
                }
            }
        }
    }

    // Pesan sukses (selalu menampilkan tombol lanjut)
    $success_message = "
        <div class='success'>
            Mode " . ($mode_ahp_full === 'ya' ? "AHP Full" : "Manual") . " diaktifkan.<br>
            <a href='KriteriaAHP.php?id_judul=" . urlencode($id_judul) . "&judul=" . urlencode($judul) . "' class='next-btn'>
                Lanjut ke Pairwise Comparison
            </a>
        </div>
    ";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Input Data AHP - <?php echo htmlspecialchars($nama_judul); ?></title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
    body{font-family:'Poppins',sans-serif;background:linear-gradient(135deg,#667eea,#764ba2);margin:0;padding:20px}
    .container{background:#fff;border-radius:16px;padding:30px;max-width:900px;margin:auto;box-shadow:0 8px 25px rgba(0,0,0,.2)}
    h2,h3{text-align:center;margin-bottom:20px}
    input,select{padding:8px;border:1px solid #ddd;border-radius:8px;width:100%;box-sizing:border-box;margin-bottom:10px}
    table{width:100%;border-collapse:collapse;margin-bottom:25px}
    td,th{border:1px solid #eee;padding:10px;text-align:center}
    button{background:#f39c12;border:none;padding:12px 20px;border-radius:10px;color:#fff;font-size:15px;cursor:pointer;transition:.3s}
    button:hover{background:#e67e22}
    .success{background:#e0ffe5;color:#207a33;padding:15px;border-radius:8px;margin-bottom:20px;text-align:center;font-weight:500}
    .error{background:#ffe0e0;color:#b00020;padding:10px;border-radius:8px;margin-bottom:20px}
    .next-btn{display:inline-block;background:#3498db;color:#fff;padding:10px 16px;margin-top:10px;border-radius:8px;text-decoration:none;transition:0.3s}
    .next-btn:hover{background:#2980b9}
</style>
</head>
<body>
<div class="container">
    <h2>Input Data AHP untuk Judul: <em><?php echo htmlspecialchars($nama_judul); ?></em></h2>

    <?php if ($error_message) echo $error_message; ?>
    <?php if ($success_message) echo $success_message; ?>

    <?php if (!isset($jumlah_kriteria) && !isset($jumlah_alternatif)) : ?>
        <!-- TAHAP 1: JUMLAH -->
        <form method="POST">
            <label>Jumlah Kriteria:</label>
            <input type="number" name="jumlah_kriteria" min="2" required>
            <label>Jumlah Alternatif:</label>
            <input type="number" name="jumlah_alternatif" min="1" required>
            <button type="submit" name="setJumlah">Lanjutkan</button>
        </form>

    <?php elseif (isset($jumlah_kriteria) && isset($jumlah_alternatif) && !isset($_POST['simpanData'])) : ?>
        <!-- TAHAP 2: DETAIL -->
        <form method="POST">
            <input type="hidden" name="jumlah_kriteria" value="<?php echo $jumlah_kriteria; ?>">
            <input type="hidden" name="jumlah_alternatif" value="<?php echo $jumlah_alternatif; ?>">

            <h3>Kriteria (Bobot dihitung otomatis di Pairwise)</h3>
            <table>
                <tr><th>Nama Kriteria</th></tr>
                <?php for ($i = 0; $i < $jumlah_kriteria; $i++): ?>
                    <tr>
                        <td><input type="text" name="nama_kriteria[]" placeholder="Nama Kriteria <?php echo ($i+1); ?>" required></td>
                    </tr>
                <?php endfor; ?>
            </table>

            <h3>Alternatif dan Bobot (Manual)</h3>
            <label>Pilih Mode Input:</label>
            <select name="mode_ahp_full" id="mode_ahp_full">
                <option value="tidak">Isi Manual (Untuk SAW dan WP)</option>
                <option value="ya">Isi Kosongan (Untuk AHP Full)</option>
            </select>

            <table id="tabelBobot">
                <tr>
                    <th>Nama Alternatif</th>
                    <?php for ($k = 0; $k < $jumlah_kriteria; $k++): ?>
                        <th>Bobot untuk Kriteria <?php echo ($k+1); ?></th>
                    <?php endfor; ?>
                </tr>
                <?php for ($a = 0; $a < $jumlah_alternatif; $a++): ?>
                    <tr>
                        <td><input type="text" name="nama_alternatif[]" placeholder="Nama Alternatif <?php echo ($a+1); ?>" required></td>
                        <?php for ($k = 0; $k < $jumlah_kriteria; $k++): ?>
                            <td><input type="number" name="nilai[<?php echo $a; ?>][<?php echo $k; ?>]" step="0.0001" min="0" inputmode="decimal" placeholder="cth 0.1564"></td>
                        <?php endfor; ?>
                    </tr>
                <?php endfor; ?>
            </table>

            <button type="submit" name="simpanData">Simpan Data</button>
        </form>

        <script>
        const modeSelect = document.getElementById('mode_ahp_full');
        const inputs = document.querySelectorAll('#tabelBobot input[type="number"]');

        function toggleInputs() {
            const isFull = modeSelect.value === 'ya';
            inputs.forEach(inp => {
                inp.disabled = isFull;
                inp.required = !isFull;
                if (isFull) inp.value = '';
            });
        }
        modeSelect.addEventListener('change', toggleInputs);
        toggleInputs();
        </script>
    <?php endif; ?>
</div>
</body>
</html>