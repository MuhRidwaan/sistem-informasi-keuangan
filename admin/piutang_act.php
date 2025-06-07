<?php 
include '../koneksi.php';
$tanggal  = $_POST['tanggal'];
$nominal  = $_POST['nominal'];
$keterangan  = $_POST['keterangan'];
$user_id = $_POST['create_who'];

mysqli_query($koneksi, "insert into piutang values (NULL,'$tanggal','$nominal','$keterangan', '$user_id')")or die(mysqli_error($koneksi));
header("location:piutang.php");