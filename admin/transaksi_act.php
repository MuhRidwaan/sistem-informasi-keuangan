<?php 
include '../koneksi.php';
session_start();
$tanggal  = $_POST['tanggal'];
$jenis  = $_POST['jenis'];
$kategori  = $_POST['kategori'];
$nominal  = $_POST['nominal'];
$keterangan  = $_POST['keterangan'];
$bank  = $_POST['bank'];
$user_id  = $_SESSION['id'];


$rekening = mysqli_query($koneksi,"select * from bank where bank_id='$bank'");
$r = mysqli_fetch_assoc($rekening);

if($jenis == "Pemasukan"){

	$saldo_sekarang = $r['bank_saldo'];
	$total = $saldo_sekarang+$nominal;
	mysqli_query($koneksi,"update bank set bank_saldo='$total' where bank_id='$bank'");

}elseif($jenis == "Pengeluaran"){

	if($r['bank_saldo'] < $nominal){
		header("location:transaksi.php?pesan=gagal");
		exit;
	}else{		
		$saldo_sekarang = $r['bank_saldo'];
		$total = $saldo_sekarang-$nominal;
		mysqli_query($koneksi,"update bank set bank_saldo='$total' where bank_id='$bank'");
	}


}


mysqli_query($koneksi, "insert into transaksi(transaksi_tanggal, transaksi_jenis, transaksi_kategori, transaksi_nominal, transaksi_keterangan, transaksi_bank, user_id,create_date) values ('$tanggal','$jenis','$kategori','$nominal','$keterangan','$bank', '$user_id',now())")or die(mysqli_error($koneksi));
header("location:transaksi.php");