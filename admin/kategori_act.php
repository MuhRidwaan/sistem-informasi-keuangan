<?php 
include '../koneksi.php';

session_start();
$kategori  = $_POST['kategori'];
$jenis = $_POST['jenis'];
$user_id = $_SESSION['id'];
mysqli_query($koneksi, "insert into kategori( kategori, user_id ,jenis, create_date,create_who) values ('$kategori' , '$user_id' , '$jenis', now(), '$user_id')");
header("location:kategori.php");