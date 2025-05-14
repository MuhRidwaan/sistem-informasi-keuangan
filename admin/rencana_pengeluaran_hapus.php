<?php
include '../koneksi.php';
session_start();

$id = $_GET['id'];

mysqli_query($koneksi, "DELETE FROM rencana_bulanan WHERE rencana_id = '$id'");

header("location:rencana_pengeluaran.php");
