<?php
include '../koneksi.php';
session_start();

$tanggal = $_POST['tanggal'];
$rekening_asal = $_POST['rekening_asal'];
$rekening_tujuan = $_POST['rekening_tujuan'];
$nominal = floatval($_POST['nominal']);
$keterangan = $_POST['keterangan'];
$create_who = $_SESSION['id'];

// Ambil saldo rekening asal
$asal = mysqli_query($koneksi, "SELECT * FROM bank WHERE bank_id = '$rekening_asal'");
$data_asal = mysqli_fetch_assoc($asal);
$saldo_asal = $data_asal['bank_saldo'];

// Cek saldo cukup
if ($saldo_asal < $nominal) {
    echo "<script>alert('Saldo rekening asal tidak mencukupi!'); window.location.href='transfer.php';</script>";
    exit;
}

// Kurangi saldo rekening asal
mysqli_query($koneksi, "UPDATE bank SET bank_saldo = bank_saldo - $nominal WHERE bank_id = '$rekening_asal'");

// Tambah saldo rekening tujuan
mysqli_query($koneksi, "UPDATE bank SET bank_saldo = bank_saldo + $nominal WHERE bank_id = '$rekening_tujuan'");

// Simpan data transfer
mysqli_query($koneksi, "INSERT INTO transfer (tanggal, rekening_asal, rekening_tujuan, nominal, keterangan, create_who) 
VALUES ('$tanggal', '$rekening_asal', '$rekening_tujuan', '$nominal', '$keterangan', '$create_who')");

echo "<script>alert('Transfer berhasil!'); window.location.href='transfer.php';</script>";
?>
