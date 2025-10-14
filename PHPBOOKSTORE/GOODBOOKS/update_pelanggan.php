<?php
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'goodbooks';
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$id = $_POST['ID_Pelanggan'];
$bank = $_POST['ID_Bank'];
$depan = $_POST['NamaDepan'];
$belakang = $_POST['NamaBelakang'];
$telp = $_POST['NoTelp'];
$email = $_POST['Email'];
$pass = $_POST['Password'];
$norek = $_POST['NoRek'];
$tgl = $_POST['TglRegis'];
$saldo = $_POST['Saldo'];

$stmt = $conn->prepare("UPDATE pelanggan SET ID_Bank=?, NamaDepan=?, NamaBelakang=?, NoTelp=?, Email=?, Password=?, NoRek=?, TglRegis=?, Saldo=? WHERE ID_Pelanggan=?");
$stmt->bind_param("issssssssi", $bank, $depan, $belakang, $telp, $email, $pass, $norek, $tgl, $saldo, $id);
$stmt->execute();

$conn->close();
header("Location: DataPelanggan.php");
exit;
