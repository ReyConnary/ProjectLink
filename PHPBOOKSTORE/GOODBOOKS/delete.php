<?php
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'goodbooks';

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$table = $_POST['table'];
$id = $_POST['id'];
$column = $_POST['column'];

$stmt = $conn->prepare("DELETE FROM $table WHERE $column = ?");
$stmt->bind_param("s", $id);
$stmt->execute();
$stmt->close();
$conn->close();

$redirect = ($table === 'databuku') ? 'DataBuku.php' : 'admin.php';
header("Location: $redirect");
exit;
