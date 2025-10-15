<?php
// koneksi.php
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'perangkingan';

$koneksi = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($koneksi->connect_errno) {
    error_log("Gagal koneksi DB: ".$koneksi->connect_error);
    // tetap biarkan $koneksi ada (tapi dengan connect_errno != 0)
}
$koneksi->set_charset('utf8mb4');