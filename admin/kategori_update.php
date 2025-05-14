<?php 
include '../koneksi.php';
session_start();
$id  = $_POST['id'];
$kategori  = $_POST['kategori'];
$user_id = $_SESSION['id'];

mysqli_query($koneksi, "update kategori set kategori='$kategori', change_date=now(), change_who='$user_id' where kategori_id='$id'");
header("location:kategori.php");