<?php
// PairwiseSub.php?id_judul=...
$host="localhost";$user="root";$pass="";$db="perangkingan";
$conn=new mysqli($host,$user,$pass,$db);
if($conn->connect_error) die($conn->connect_error);

$id_judul = $_GET['id_judul'] ?? '';
if (!$id_judul) die("ID_Judul tidak ditemukan.");

$judulRow = $conn->prepare("SELECT namajudul FROM judul WHERE ID_Judul=?");
$judulRow->bind_param("s",$id_judul);
$judulRow->execute();
$judulRow->bind_result($nama_judul);
$judulRow->fetch();
$judulRow->close();

// Ambil semua kriteria
$krts = $conn->prepare("SELECT ID_Kriteria,nama_kriteria FROM kriteria WHERE ID_Judul=? ORDER BY ID_Kriteria");
$krts->bind_param("s",$id_judul);
$krts->execute();
$rkr=$krts->get_result();
$kriteria = $rkr->fetch_all(MYSQLI_ASSOC);
$krts->close();

// Ambil semua sub per kriteria
$subByK = [];
foreach($kriteria as $k) {
    $stm = $conn->prepare("SELECT ID_Sub,nama_sub FROM subkriteria WHERE ID_Judul=? AND ID_Kriteria=? ORDER BY ID_Sub");
    $stm->bind_param("ss",$id_judul,$k['ID_Kriteria']);
    $stm->execute();
    $rs=$stm->get_result();
    $subByK[$k['ID_Kriteria']] = $rs->fetch_all(MYSQLI_ASSOC);
    $stm->close();
}

if ($_SERVER['REQUEST_METHOD']==='POST') {
    // Expect POST pair[id_kriteria][subA__subB] = nilai
    $ins = $conn->prepare("
        INSERT INTO ahp_pairwise_sub (id_judul, id_kriteria, id_sub1, id_sub2, nilai)
        VALUES (?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE nilai = VALUES(nilai)
    ");

    foreach ($_POST['pair'] as $idk => $pairs) {
        foreach ($pairs as $key => $val) {
            [$s1,$s2] = explode('__',$key);
            $v = (float)$val;
            $ins->bind_param("ssssd",$id_judul,$idk,$s1,$s2,$v);
            $ins->execute();
        }
    }
    $ins->close();

    // Hitung bobot subkriteria per kriteria (metode Geometric Mean)
    $calcStmt = $conn->prepare("SELECT ID_Sub FROM subkriteria WHERE ID_Judul=? AND ID_Kriteria=? ORDER BY ID_Sub");
    $getPair = $conn->prepare("SELECT nilai FROM ahp_pairwise_sub WHERE id_judul=? AND id_kriteria=? AND id_sub1=? AND id_sub2=?");
    $insBobot = $conn->prepare("
        INSERT INTO ahp_bobot_sub (id_judul, id_kriteria, id_sub, bobot)
        VALUES (?,?,?,?)
        ON DUPLICATE KEY UPDATE bobot=VALUES(bobot)
    ");

    foreach($kriteria as $k) {
        $calcStmt->bind_param("ss",$id_judul,$k['ID_Kriteria']);
        $calcStmt->execute();
        $rs = $calcStmt->get_result();
        $subs = $rs->fetch_all(MYSQLI_ASSOC);
        $n = count($subs);
        if ($n==0) continue;

        // Bangun matriks pairwise lengkap
        $A = array_fill(0,$n, array_fill(0,$n,1.0));
        for($i=0;$i<$n;$i++){
            for($j=$i+1;$j<$n;$j++){
                $s1=$subs[$i]['ID_Sub'];
                $s2=$subs[$j]['ID_Sub'];

                // Cek nilai tersimpan
                $getPair->bind_param("ssss",$id_judul,$k['ID_Kriteria'],$s1,$s2);
                $getPair->execute();
                $getPair->bind_result($val);
                $found=false;
                if ($getPair->fetch()) { $found=true; $v=(float)$val; }
                $getPair->free_result();

                if (!$found) {
                    // Jika terbalik
                    $getPair->bind_param("ssss",$id_judul,$k['ID_Kriteria'],$s2,$s1);
                    $getPair->execute();
                    $getPair->bind_result($val2);
                    if ($getPair->fetch()) { $v = 1.0/((float)$val2); }
                    else { $v = 1.0; }
                    $getPair->free_result();
                }

                $A[$i][$j] = $v;
                $A[$j][$i] = 1.0 / max($v, 1e-12);
            }
        }

        // Geometric mean
        $gm = [];
        for($i=0;$i<$n;$i++){
            $prod = 1.0;
            for($j=0;$j<$n;$j++) $prod *= $A[$i][$j];
            $gm[$i] = pow($prod, 1.0/$n);
        }

        $sumgm = array_sum($gm);
        for($i=0;$i<$n;$i++){
            $w = ($sumgm>0) ? ($gm[$i]/$sumgm) : 0;
            $insBobot->bind_param("sssd",$id_judul,$k['ID_Kriteria'],$subs[$i]['ID_Sub'],$w);
            $insBobot->execute();
        }
    }

    $calcStmt->close();
    $getPair->close();
    $insBobot->close();

    header("Location: ProcessAHPFull.php?id_judul=".$id_judul);
    exit;
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Pairwise Subkriteria</title>
<style>
body {
    font-family: "Segoe UI", sans-serif;
    background: #fafafa;
    color: #333;
    margin: 20px auto;
    max-width: 1000px;
}
h2 {
    text-align: center;
    color: #2c3e50;
    margin-bottom: 30px;
}
h3 {
    color: #34495e;
    margin-top: 40px;
}
table {
    border-collapse: collapse;
    width: 100%;
    margin-bottom: 25px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}
th, td {
    border: 1px solid #ccc;
    padding: 8px;
    text-align: center;
}
th {
    background: #f0f0f0;
    font-weight: bold;
}
input[type="text"] {
    width: 80%;
    padding: 4px;
    text-align: center;
}
button {
    display: block;
    margin: 40px auto;
    padding: 10px 20px;
    font-size: 16px;
    background: #3498db;
    color: #fff;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background 0.3s;
}
button:hover {
    background: #2980b9;
}
</style>
</head>
<body>
<h2>Pairwise Comparison Subkriteria — <?=htmlspecialchars($nama_judul)?></h2>

<form method="post">
<?php foreach($kriteria as $k): 
    $subs = $conn->query("SELECT ID_Sub,nama_sub FROM subkriteria WHERE ID_Judul='".$id_judul."' AND ID_Kriteria='".$k['ID_Kriteria']."' ORDER BY ID_Sub")->fetch_all(MYSQLI_ASSOC);
    if(count($subs) < 2) { 
        echo "<h3>".htmlspecialchars($k['nama_kriteria'])." — <span style='color:red'>kurang subkriteria (min 2)</span></h3>"; 
        continue; 
    }
?>
  <h3><?=htmlspecialchars($k['nama_kriteria'])?></h3>
  <table>
    <tr>
      <th></th>
      <?php foreach($subs as $s) echo "<th>".htmlspecialchars($s['nama_sub'])."</th>"; ?>
    </tr>

    <?php for($i=0;$i<count($subs);$i++): ?>
      <tr>
        <th><?=htmlspecialchars($subs[$i]['nama_sub'])?></th>
        <?php for($j=0;$j<count($subs);$j++): 
            if($i==$j) { echo "<td>1</td>"; continue; }
            if ($i<$j) {
                $name = "pair[".$k['ID_Kriteria']."][".$subs[$i]['ID_Sub']."__".$subs[$j]['ID_Sub']."]";
                echo "<td><input step='any' type='text' name='".htmlspecialchars($name)."' placeholder='e.g. 3 atau 1/3' required></td>";
            } else {
                echo "<td style='color:#888;'>1 / (atas)</td>";
            }
        endfor; ?>
      </tr>
    <?php endfor; ?>
  </table>
<?php endforeach; ?>

<button type="submit">💾 Simpan Pairwise & Hitung Bobot Subkriteria</button>
</form>

</body>
</html>
