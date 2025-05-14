<?php 
include '../koneksi.php';

$id         = $_POST['id'];
$tanggal    = $_POST['tanggal'];
$nominal    = $_POST['nominal'];
$keterangan = $_POST['keterangan'];
$bank = $_POST['bank'];
$user_id    = 1; // Sesuaikan dengan session login kalau ada

// Ambil data hutang lama
$q_hutang = mysqli_query($koneksi, "SELECT hutang_nominal FROM hutang WHERE hutang_id = '$id'");
$hutang = mysqli_fetch_assoc($q_hutang);
$hutang_sisa = $hutang['hutang_nominal'];

if($nominal > $hutang_sisa){
    // Error: bayar lebih besar dari hutang
    echo "<script>alert('Jumlah pembayaran melebihi sisa hutang!'); window.location='hutang.php';</script>";
    exit;
}

// Cek atau insert kategori
$q_kategori = mysqli_query($koneksi, "SELECT kategori_id FROM kategori WHERE kategori = 'Bayar Hutang' AND jenis = 'Pengeluaran'");
if(mysqli_num_rows($q_kategori) == 0){
    mysqli_query($koneksi, "INSERT INTO kategori (kategori, jenis, create_date, create_who, is_system) VALUES ('Bayar Hutang', 'Pengeluaran', now(), '$user_id', 1)") or die(mysqli_error($koneksi));
    $kategori_id = mysqli_insert_id($koneksi);
} else {
    $row = mysqli_fetch_assoc($q_kategori);
    $kategori_id = $row['kategori_id'];
}

// Update hutang
if($nominal == $hutang_sisa){
    // Lunas
    mysqli_query($koneksi, "
        UPDATE hutang 
        SET 
            hutang_nominal = 0,
            hutang_keterangan = '$keterangan',
            status = 1,
            tanggal_pembayaran = '$tanggal'
        WHERE hutang_id = '$id'
    ") or die(mysqli_error($koneksi));
} else {
    // Bayar sebagian
    $sisa = $hutang_sisa - $nominal;
    mysqli_query($koneksi, "
        UPDATE hutang 
        SET 
            hutang_nominal = '$sisa',
            hutang_keterangan = '$keterangan'
        WHERE hutang_id = '$id'
    ") or die(mysqli_error($koneksi));
}

// Catat transaksi pengeluaran
mysqli_query($koneksi, "
    INSERT INTO transaksi (
        transaksi_tanggal, 
        transaksi_jenis, 
        transaksi_kategori, 
        transaksi_nominal, 
        transaksi_keterangan,
        transaksi_bank,
        user_id, 
        create_date
    ) VALUES (
        '$tanggal', 
        'Pengeluaran', 
        '$kategori_id', 
        '$nominal', 
        'Pembayaran hutang: $keterangan', 
        '$bank',
        '$user_id',
        NOW()
    )
") or die(mysqli_error($koneksi));

header("location:hutang.php");
?>
