<?php 
include '../koneksi.php';
session_start();
$nama  = $_POST['nama'];
$pemilik  = $_POST['pemilik'];
$nomor  = $_POST['nomor'];
$saldo  = $_POST['saldo'];
$user_id = $_SESSION['id'];

mysqli_query($koneksi, "insert into bank values (NULL,'$nama','$pemilik','$nomor','$saldo','$user_id')");
header("location:bank.php");