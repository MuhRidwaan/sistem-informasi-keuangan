<?php
include '../koneksi.php'; // Sesuaikan path ke file koneksi database Anda

// Memastikan 'jenis' ada dalam POST request sebelum digunakan
if (isset($_POST['jenis'])) {
    $jenis_input = $_POST['jenis'];
    $query = ""; // Inisialisasi variabel query
    $params = []; // Inisialisasi array untuk parameter prepared statement
    $types = ""; // Inisialisasi string untuk tipe parameter

    if ($jenis_input == "Pemasukan") {
        // Query untuk jenis "Pemasukan"
        // Memilih kategori yang jenisnya 'Pemasukan' ATAU yang jenisnya NULL/kosong (dianggap umum/bisa untuk pemasukan)
        // dan merupakan kategori sistem.
        $query = "SELECT kategori_id, kategori 
                  FROM kategori 
                  WHERE (jenis = ? OR COALESCE(jenis, '') = '') 
                  AND is_system = 1 
                  ORDER BY kategori ASC";
        $params = [$jenis_input];
        $types = "s"; // 's' untuk string
    } else if ($jenis_input == "Pengeluaran") { // Lebih eksplisit menggunakan else if
        // Query untuk jenis "Pengeluaran"
        // Memilih kategori dari rencana_bulanan yang jenisnya 'Pengeluaran'.
        // Pastikan untuk SELECT kategori_id juga agar bisa digunakan di <option value="">
        $query = "SELECT DISTINCT b.kategori_id, b.kategori 
                  FROM rencana_bulanan a
                  INNER JOIN kategori b ON a.kategori_id = b.kategori_id 
                  WHERE b.jenis = ?";
        $params = [$jenis_input];
        $types = "s"; // 's' untuk string
    }

    // Hanya jalankan query jika $query sudah di-set (artinya jenis_input valid)
    if (!empty($query)) {
        // Menggunakan prepared statements untuk keamanan (mencegah SQL Injection)
        $stmt = mysqli_prepare($koneksi, $query);

        if ($stmt) {
            // Bind parameter jika ada
            if (!empty($params) && !empty($types)) {
                mysqli_stmt_bind_param($stmt, $types, ...$params);
            }

            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            // Opsi default
            echo '<option value="">- Pilih Kategori -</option>';

            // Loop untuk menampilkan data kategori
            if ($result && mysqli_num_rows($result) > 0) {
                while ($k = mysqli_fetch_assoc($result)) { // Menggunakan mysqli_fetch_assoc untuk array asosiatif
                    // Pastikan 'kategori_id' dan 'kategori' ada dalam hasil query
                    $kategori_id = isset($k['kategori_id']) ? htmlspecialchars($k['kategori_id'], ENT_QUOTES, 'UTF-8') : '';
                    $kategori_nama = isset($k['kategori']) ? htmlspecialchars($k['kategori'], ENT_QUOTES, 'UTF-8') : 'Tidak Ada Nama';
                    echo '<option value="' . $kategori_id . '">' . $kategori_nama . '</option>';
                }
            } else {
                echo '<option value="">- Tidak ada kategori ditemukan -</option>';
            }
            mysqli_stmt_close($stmt);
        } else {
            // Gagal mempersiapkan statement
            // Anda bisa menambahkan logging error di sini
            echo '<option value="">- Error mengambil data -</option>';
            // Untuk debugging: echo "Error: " . mysqli_error($koneksi);
        }
    } else {
        // Jika jenis_input tidak dikenali (bukan "Pemasukan" atau "Pengeluaran")
        echo '<option value="">- Jenis tidak valid -</option>';
    }
} else {
    // Jika 'jenis' tidak ada dalam POST request
    echo '<option value="">- Pilih Jenis Transaksi Dahulu -</option>';
}

// Jangan lupa untuk menutup koneksi jika sudah tidak digunakan lagi di akhir script utama (jika ini adalah akhir dari script)
// mysqli_close($koneksi); // Biasanya diletakkan di file utama setelah semua operasi database selesai
?>