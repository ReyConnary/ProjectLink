<?php
// Koneksi database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "perangkingan";

$conn = new mysqli($host, $user, $pass, $db);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$error_message = "";

// Jika form dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $judul = trim($_POST['judul']);
    $metode = $_POST['metode'];

    if (!empty($judul) && !empty($metode)) {
        // Cek apakah judul sudah ada
        $check = $conn->prepare("SELECT COUNT(*) FROM judul WHERE namajudul = ?");
        $check->bind_param("s", $judul);
        $check->execute();
        $check->bind_result($count);
        $check->fetch();
        $check->close();

        if ($count > 0) {
            // Jika sudah ada
            $error_message = "⚠️ Nama Judul / Topik sudah ada, silakan pilih nama lain.";
        } else {
            // Buat ID unik
            $id_judul = "JDL-" . date("YmdHis") . "-" . uniqid();

            // Simpan ke database
            $stmt = $conn->prepare("INSERT INTO judul (ID_Judul, namajudul) VALUES (?, ?)");
            $stmt->bind_param("ss", $id_judul, $judul);
            $stmt->execute();

            // Redirect
            if ($metode == "AHP") {
                header("Location: InputAHP.php?id_judul=" . urlencode($id_judul) . "&judul=" . urlencode($judul));
            }  elseif ($metode == "SAW/WP") {
                header("Location: InputSAWP.php?id_judul=$id_judul&judul=" . urlencode($judul));
            }
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Halaman Utama - Perangkingan</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea, #764ba2);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            background: white;
            padding: 40px 50px;
            border-radius: 20px;
            box-shadow: 0px 10px 30px rgba(0,0,0,0.2);
            text-align: center;
            width: 420px;
        }
        h2 {
            margin-bottom: 20px;
            font-weight: 600;
            color: #333;
        }
        .error {
            background: #ffe0e0;
            color: #b00020;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 14px;
            font-weight: 500;
        }
        input[type="text"] {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 12px;
            margin-bottom: 25px;
            font-size: 16px;
            transition: 0.3s;
        }
        input[type="text"]:focus {
            border-color: #667eea;
            outline: none;
            box-shadow: 0px 0px 8px rgba(102,126,234,0.5);
        }
        .buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
        }
        button {
            flex: 1;
            padding: 12px 0;
            font-size: 16px;
            font-weight: 600;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            color: white;
        }
        button[value="AHP"] { background: #f39c12; }
        button[value="SAW/WP"] { background: #27ae60; }
        button:hover {
            transform: translateY(-3px);
            box-shadow: 0px 6px 15px rgba(0,0,0,0.2);
        }
        button:active {
            transform: scale(0.97);
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Masukkan Judul Proyek</h2>

        <?php if (!empty($error_message)) : ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="text" size="20px" name="judul" placeholder="Contoh: Sistem Pendukung Keputusan" required>
            <h5>Pilih metode perhitungan</h5>
            <div class="buttons">
                <button type="submit" name="metode" value="AHP">AHP</button>
                <button type="submit" name="metode" value="SAW/WP">SAW/WP</button>
            </div>
        </form>
    </div>
</body>
</html>
