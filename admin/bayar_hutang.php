<?php
include '../koneksi.php';

$id         = $_POST['id'];
$tanggal    = $_POST['tanggal'];
$nominal    = $_POST['nominal']; // Nominal pembayaran
$keterangan = $_POST['keterangan'];
$bank_id_pembayaran = $_POST['bank']; // Ini adalah ID bank yang digunakan untuk membayar
$user_id    = 1; // Sesuaikan dengan session login kalau ada

// Ambil data hutang lama
$q_hutang = mysqli_query($koneksi, "SELECT hutang_nominal FROM hutang WHERE hutang_id = '$id'");
if (!$q_hutang) {
    die("Error mengambil data hutang: " . mysqli_error($koneksi));
}
$hutang_data = mysqli_fetch_assoc($q_hutang);
if (!$hutang_data) {
    // Handle jika ID hutang tidak ditemukan
    echo "<script>alert('Data hutang tidak ditemukan!'); window.location='hutang.php';</script>";
    exit;
}
$hutang_sisa = $hutang_data['hutang_nominal'];

// Validasi nominal pembayaran
if ($nominal > $hutang_sisa) {
    echo "<script>alert('Jumlah pembayaran (" . number_format($nominal) . ") melebihi sisa hutang (" . number_format($hutang_sisa) . ")!'); window.location='hutang.php';</script>";
    exit;
}

// SEBELUM MELAKUKAN OPERASI APAPUN, MULAI TRANSAKSI DATABASE (jika memungkinkan dan didukung)
// mysqli_begin_transaction($koneksi); // Contoh jika menggunakan transaksi

// 1. Cek Saldo Bank dan Kurangi Saldo Bank
// Pastikan nominal adalah angka positif
$nominal_pembayaran_float = floatval($nominal);
if ($nominal_pembayaran_float <= 0) {
    echo "<script>alert('Nominal pembayaran tidak valid!'); window.location='hutang.php';</script>";
    // mysqli_rollback($koneksi); // Jika pakai transaksi
    exit;
}

// Ambil saldo bank saat ini
$q_cek_saldo = mysqli_query($koneksi, "SELECT bank_saldo FROM bank WHERE bank_id = '$bank_id_pembayaran'");
if (!$q_cek_saldo) {
    // mysqli_rollback($koneksi); // Jika pakai transaksi
    die("Error mengambil data saldo bank: " . mysqli_error($koneksi));
}

if (mysqli_num_rows($q_cek_saldo) > 0) {
    $data_bank = mysqli_fetch_assoc($q_cek_saldo);
    $saldo_bank_saat_ini = floatval($data_bank['bank_saldo']);

    if ($nominal_pembayaran_float > $saldo_bank_saat_ini) {
        echo "<script>alert('Saldo bank tidak mencukupi untuk melakukan pembayaran ini! Saldo saat ini: " . number_format($saldo_bank_saat_ini) . "'); window.location='hutang.php';</script>";
        // mysqli_rollback($koneksi); // Jika pakai transaksi
        exit;
    }

    // Kurangi saldo rekening asal
    $update_saldo_query = "UPDATE bank SET bank_saldo = bank_saldo - $nominal_pembayaran_float WHERE bank_id = '$bank_id_pembayaran'";
    if (!mysqli_query($koneksi, $update_saldo_query)) {
        // mysqli_rollback($koneksi); // Jika pakai transaksi
        die("Error mengurangi saldo bank: " . mysqli_error($koneksi));
    }
} else {
    // Bank tidak ditemukan
    echo "<script>alert('Data bank yang dipilih untuk pembayaran tidak ditemukan!'); window.location='hutang.php';</script>";
    // mysqli_rollback($koneksi); // Jika pakai transaksi
    exit;
}


// 2. Cek atau insert kategori
$q_kategori = mysqli_query($koneksi, "SELECT kategori_id FROM kategori WHERE kategori = 'Bayar Hutang' AND jenis = 'Pengeluaran'");
if (!$q_kategori) {
    // mysqli_rollback($koneksi); // Jika pakai transaksi
    die("Error mencari kategori: " . mysqli_error($koneksi));
}

$kategori_id = null;
if (mysqli_num_rows($q_kategori) == 0) {
    $insert_kategori_query = "INSERT INTO kategori (kategori, jenis, create_date, create_who, is_system) VALUES ('Bayar Hutang', 'Pengeluaran', now(), '$user_id', 1)";
    if (!mysqli_query($koneksi, $insert_kategori_query)) {
        // mysqli_rollback($koneksi); // Jika pakai transaksi
        die("Error menambah kategori: " . mysqli_error($koneksi));
    }
    $kategori_id = mysqli_insert_id($koneksi);
} else {
    $row_kategori = mysqli_fetch_assoc($q_kategori);
    $kategori_id = $row_kategori['kategori_id'];
}

// 3. Update hutang
$update_hutang_query = "";
if ($nominal_pembayaran_float == floatval($hutang_sisa)) {
    // Lunas
    $update_hutang_query = "
        UPDATE hutang
        SET
            hutang_nominal = 0,
            hutang_keterangan = '$keterangan',
            status = 1,
            tanggal_pembayaran = '$tanggal'
        WHERE hutang_id = '$id'
    ";
} else {
    // Bayar sebagian
    $sisa_baru = floatval($hutang_sisa) - $nominal_pembayaran_float;
    $update_hutang_query = "
        UPDATE hutang
        SET
            hutang_nominal = '$sisa_baru',
            hutang_keterangan = '$keterangan'
        WHERE hutang_id = '$id'
    ";
}

if (!mysqli_query($koneksi, $update_hutang_query)) {
    // mysqli_rollback($koneksi); // Jika pakai transaksi
    die("Error mengupdate data hutang: " . mysqli_error($koneksi));
}

// 4. Catat transaksi pengeluaran
$keterangan_transaksi = "Pembayaran hutang: " . $keterangan;
$insert_transaksi_query = "
    INSERT INTO transaksi (
        transaksi_tanggal,
        transaksi_jenis,
        transaksi_kategori,
        transaksi_nominal,
        transaksi_keterangan,
        transaksi_bank, /* Pastikan kolom ini sesuai untuk menyimpan ID bank */
        user_id,
        create_date
    ) VALUES (
        '$tanggal',
        'Pengeluaran',
        '$kategori_id',
        '$nominal_pembayaran_float',
        '$keterangan_transaksi',
        '$bank_id_pembayaran',
        '$user_id',
        NOW()
    )
";

if (!mysqli_query($koneksi, $insert_transaksi_query)) {
    // mysqli_rollback($koneksi); // Jika pakai transaksi
    die("Error mencatat transaksi pengeluaran: " . mysqli_error($koneksi));
}

// JIKA SEMUA OPERASI DATABASE BERHASIL, COMMIT TRANSAKSI
// mysqli_commit($koneksi); // Contoh jika menggunakan transaksi

header("location:hutang.php?status=pembayaran_sukses"); // Tambahkan parameter status untuk notifikasi
exit;

?>