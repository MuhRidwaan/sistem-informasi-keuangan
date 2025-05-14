<?php 
include '../koneksi.php';
session_start();

$kategori  = $_POST['kategori'];
$budget = $_POST['budget'];
$user_id = $_SESSION['id'];
$bulan = date('Y-m'); // ambil format tahun-bulan aja

// validasi input
$sql = mysqli_query($koneksi, "
  SELECT * FROM rencana_bulanan 
  WHERE kategori_id = '$kategori' 
  AND DATE_FORMAT(bulan, '%Y-%m') = '$bulan'
");

// DEBUG HASIL QUERY
if (!$sql) {
    die("Query Error: " . mysqli_error($koneksi));
}

if (mysqli_num_rows($sql) > 0) {
    // Duplikat ditemukan
    echo "Data duplikat ditemukan:<br>";
    while ($row = mysqli_fetch_assoc($sql)) {
        echo "Kategori ID: " . $row['kategori_id'] . " | Bulan: " . $row['bulan'] . "<br>";
    }
    // Kalo mau langsung redirect, tinggal uncomment ini:
    header("location:rencana_pengeluaran.php?status=duplikat");
    exit();
}

// Insert jika tidak duplikat
$bulan_lengkap = date('Y-m-01'); // format lengkap Y-m-01 untuk insert
$insert = mysqli_query($koneksi, "
  INSERT INTO rencana_bulanan (bulan, kategori_id, budget, create_date, create_who) 
  VALUES ('$bulan_lengkap', '$kategori', '$budget', NOW(), '$user_id')
");

if (!$insert) {
    die("Insert Error: " . mysqli_error($koneksi));
}

header("location:rencana_pengeluaran.php");
?>
