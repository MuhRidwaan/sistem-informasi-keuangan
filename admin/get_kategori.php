<?php
include '../koneksi.php'; // atau sesuaikan path koneksi lu

if(isset($_POST['jenis'])){
  $jenis = $_POST['jenis'];
  $data = mysqli_query($koneksi, "SELECT * FROM kategori 
                                  WHERE (jenis='$jenis' OR COALESCE(jenis, '') = '')
                                  AND is_system = 0
                                  ORDER BY kategori ASC");

  echo '<option value="">- Pilih -</option>';
  while($k = mysqli_fetch_array($data)){
    echo '<option value="'.$k['kategori_id'].'">'.$k['kategori'].'</option>';
  }
}
?>
