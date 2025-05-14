<?php

$host     = "localhost";
$user     = "root";
$password = "";
$database = "project_keuangan";
$port     = 3307;

$koneksi = mysqli_connect($host, $user, $password, $database, $port);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

?>
