<?php 
include '../koneksi.php';
$tanggal  = $_POST['tanggal'];
$nominal  = $_POST['nominal'];
$keterangan  = $_POST['keterangan'];

mysqli_query($koneksi, "insert into hutang(hutang_tanggal, hutang_nominal, hutang_keterangan) values ('$tanggal','$nominal','$keterangan')")or die(mysqli_error($koneksi));
header("location:hutang.php");