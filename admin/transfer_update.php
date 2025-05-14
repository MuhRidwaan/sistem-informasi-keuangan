<?php
include '../koneksi.php';
session_start();


$id = $_POST['id'];
$tanggal = $_POST['tanggal'];
$rekening_asal = $_POST['rekening_asal'];
$rekening_tujuan = $_POST['rekening_tujuan'];
$nominal_baru = floatval($_POST['nominal']);
$keterangan = $_POST['keterangan'];
$change_who = $_SESSION['id'];

// Ambil data transfer lama
$lama = mysqli_query($koneksi, "SELECT * FROM transfer WHERE transfer_id = '$id'");
$datalama = mysqli_fetch_assoc($lama);

$rekening_asal_lama = $datalama['rekening_asal'];
$rekening_tujuan_lama = $datalama['rekening_tujuan'];
$nominal_lama = floatval($datalama['nominal']);

// Kembalikan saldo rekening asal lama
mysqli_query($koneksi, "UPDATE bank SET bank_saldo = bank_saldo + $nominal_lama WHERE bank_id = '$rekening_asal_lama'");

// Kurangi saldo rekening tujuan lama
mysqli_query($koneksi, "UPDATE bank SET bank_saldo = bank_saldo - $nominal_lama WHERE bank_id = '$rekening_tujuan_lama'");

// Cek saldo rekening asal baru cukup atau tidak
$cek_asal = mysqli_query($koneksi, "SELECT bank_saldo FROM bank WHERE bank_id = '$rekening_asal'");
$data_asal = mysqli_fetch_assoc($cek_asal);
$saldo_asal = $data_asal['bank_saldo'];

if ($saldo_asal < $nominal_baru) {
    // Balikin ke kondisi sebelumnya
    mysqli_query($koneksi, "UPDATE bank SET bank_saldo = bank_saldo - $nominal_lama WHERE bank_id = '$rekening_asal_lama'");
    mysqli_query($koneksi, "UPDATE bank SET bank_saldo = bank_saldo + $nominal_lama WHERE bank_id = '$rekening_tujuan_lama'");

    echo "<script>alert('Saldo rekening asal tidak mencukupi untuk nominal baru!'); window.location.href='transfer.php';</script>";
    exit;
}

// Lakukan update saldo sesuai transfer baru
mysqli_query($koneksi, "UPDATE bank SET bank_saldo = bank_saldo - $nominal_baru WHERE bank_id = '$rekening_asal'");
mysqli_query($koneksi, "UPDATE bank SET bank_saldo = bank_saldo + $nominal_baru WHERE bank_id = '$rekening_tujuan'");

// Update data transfer
mysqli_query($koneksi, "UPDATE transfer SET 
    tanggal = '$tanggal', 
    rekening_asal = '$rekening_asal',
    rekening_tujuan = '$rekening_tujuan',
    nominal = '$nominal_baru',
    keterangan = '$keterangan',
    change_who = '$change_who',
    change_date = NOW()
    WHERE transfer_id = '$id'
");

echo "<script>alert('Data transfer berhasil diupdate!'); window.location.href='transfer.php';</script>";
?>
