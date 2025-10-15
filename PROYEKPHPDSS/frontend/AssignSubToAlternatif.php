<?php
// AssignSubToAlternatif.php?id_judul=...
$host = "localhost";
$user = "root";
$pass = "";
$db   = "perangkingan";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die($conn->connect_error);

$id_judul = $_GET['id_judul'] ?? '';
if (!$id_judul) die("ID_Judul tidak ditemukan.");

$judulRow = $conn->prepare("SELECT namajudul FROM judul WHERE ID_Judul=?");
$judulRow->bind_param("s", $id_judul);
$judulRow->execute();
$judulRow->bind_result($nama_judul);
$judulRow->fetch();
$judulRow->close();

// Ambil semua kriteria
$qK = $conn->prepare("SELECT ID_Kriteria, nama_kriteria FROM kriteria WHERE ID_Judul=? ORDER BY ID_Kriteria");
$qK->bind_param("s", $id_judul);
$qK->execute();
$rK = $qK->get_result();
$kriteria = $rK->fetch_all(MYSQLI_ASSOC);
$qK->close();

// Ambil semua subkriteria
$qS = $conn->prepare("SELECT ID_Sub, ID_Kriteria, nama_sub FROM subkriteria WHERE ID_Judul=? ORDER BY ID_Kriteria, ID_Sub");
$qS->bind_param("s", $id_judul);
$qS->execute();
$rS = $qS->get_result();
$subs = $rS->fetch_all(MYSQLI_ASSOC);
$qS->close();

// Group sub by kriteria
$subByKriteria = [];
foreach ($subs as $s) {
    $subByKriteria[$s['ID_Kriteria']][] = $s;
}

// Ambil alternatif
$qA = $conn->prepare("SELECT ID_Alternatif, nama_alternatif FROM alternatif WHERE ID_Judul=? ORDER BY ID_Alternatif");
$qA->bind_param("s", $id_judul);
$qA->execute();
$rA = $qA->get_result();
$alternatif = $rA->fetch_all(MYSQLI_ASSOC);
$qA->close();

// Saat tombol simpan diklik
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("INSERT INTO nilai_subkriteria (id_judul, id_alternatif, id_sub, nilai)
        VALUES (?, ?, ?, 0)
        ON DUPLICATE KEY UPDATE id_sub=VALUES(id_sub)");
    
    foreach ($_POST['pilihan'] as $id_alt => $arrKriteria) {
        foreach ($arrKriteria as $id_kriteria => $id_sub) {
            if (!$id_sub) continue;
            $stmt->bind_param("sss", $id_judul, $id_alt, $id_sub);
            $stmt->execute();
        }
    }
    $stmt->close();
    header("Location: PairwiseSub.php?id_judul=" . $id_judul);
    exit;
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Assign Subkriteria ke Alternatif</title>
<style>
    body {
        font-family: "Segoe UI", Tahoma, sans-serif;
        margin: 40px;
        background: #fafafa;
        color: #333;
    }
    h2 {
        text-align: center;
        margin-bottom: 25px;
        color: #2c3e50;
    }
    table {
        border-collapse: collapse;
        width: 100%;
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 0 8px rgba(0,0,0,0.1);
    }
    th, td {
        border: 1px solid #e0e0e0;
        padding: 10px;
        text-align: center;
    }
    th {
        background: #f7f9fa;
        color: #333;
        font-weight: 600;
    }
    tr:nth-child(even) {
        background-color: #fbfbfb;
    }
    tr:hover td {
        background: #f1faff;
    }
    select {
        width: 100%;
        padding: 6px 8px;
        border-radius: 5px;
        border: 1px solid #ccc;
        background: #fff;
        font-size: 14px;
        transition: 0.2s;
    }
    select:focus {
        outline: none;
        border-color: #3498db;
        box-shadow: 0 0 4px #bfe3ff;
    }
    button {
        display: block;
        margin: 25px auto;
        padding: 10px 25px;
        background: #3498db;
        border: none;
        color: white;
        font-size: 15px;
        border-radius: 6px;
        cursor: pointer;
        transition: background 0.2s ease;
    }
    button:hover {
        background: #2980b9;
    }
    td b {
        color: #2c3e50;
    }
</style>
</head>
<body>
<h2>Assign Subkriteria ke Alternatif<br><small><?=htmlspecialchars($nama_judul)?></small></h2>
<form method="post">
<table>
<tr>
  <th>Alternatif</th>
  <?php foreach ($kriteria as $k): ?>
    <th><?=htmlspecialchars($k['nama_kriteria'])?></th>
  <?php endforeach; ?>
</tr>

<?php foreach ($alternatif as $alt): ?>
<tr>
  <td><b><?=htmlspecialchars($alt['nama_alternatif'])?></b></td>
  <?php foreach ($kriteria as $k): 
      $subsForK = $subByKriteria[$k['ID_Kriteria']] ?? [];
      $q = $conn->prepare("SELECT id_sub FROM nilai_subkriteria WHERE id_judul=? AND id_alternatif=? 
                          AND id_sub IN (SELECT ID_Sub FROM subkriteria WHERE ID_Kriteria=?) LIMIT 1");
      $q->bind_param("sss", $id_judul, $alt['ID_Alternatif'], $k['ID_Kriteria']);
      $q->execute(); 
      $q->bind_result($selected_sub); 
      $q->fetch(); 
      $q->close();
  ?>
    <td>
      <select name="pilihan[<?=htmlspecialchars($alt['ID_Alternatif'])?>][<?=htmlspecialchars($k['ID_Kriteria'])?>]">
        <option value="">-- Pilih Subkriteria --</option>
        <?php foreach ($subsForK as $sub): ?>
          <option value="<?=$sub['ID_Sub']?>" <?=($selected_sub == $sub['ID_Sub'] ? 'selected' : '')?>>
            <?=htmlspecialchars($sub['nama_sub'])?>
          </option>
        <?php endforeach; ?>
      </select>
    </td>
  <?php endforeach; ?>
</tr>
<?php endforeach; ?>
</table>

<button type="submit">💾 Simpan & Lanjut ke Pairwise</button>
</form>
</body>
</html>
