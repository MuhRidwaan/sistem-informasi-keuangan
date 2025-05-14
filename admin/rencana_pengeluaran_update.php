<?php
include '../koneksi.php';
echo "<pre>";

session_start();

$id = $_POST['id'];
$budget = $_POST['budget'];
$user_id = $_SESSION['id'];

mysqli_query($koneksi, "UPDATE rencana_bulanan SET budget = '$budget', change_date = NOW(), change_who = '$user_id' WHERE rencana_id = '$id'");

header("location:rencana_pengeluaran.php");
