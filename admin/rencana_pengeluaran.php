<?php include 'header.php'; ?>
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Pengeluaran
      <small>Data Rencana Pengeluaran</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Dashboard</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <section class="col-lg-12">
        <div class="box box-info">
          <div class="box-header">
            <h3 class="box-title">
     <?php
$bulan_ini = date('Y-m');

// Ambil total budget rencana bulan ini
$sql_budget = mysqli_query($koneksi, "
    SELECT SUM(budget) AS total_rencana
    FROM rencana_bulanan
    WHERE DATE_FORMAT(bulan, '%Y-%m') = '$bulan_ini'
");
$data_budget = mysqli_fetch_array($sql_budget);
$total_rencana = $data_budget['total_rencana'] ?? 0;

// Ambil total pengeluaran real bulan ini dari tabel transaksi
$sql_pengeluaran = mysqli_query($koneksi, "
    SELECT SUM(transaksi_nominal) AS total_pengeluaran
    FROM transaksi
    WHERE transaksi_jenis = 'Pengeluaran'
    AND DATE_FORMAT(transaksi_tanggal, '%Y-%m') = '$bulan_ini'
");
$data_pengeluaran = mysqli_fetch_array($sql_pengeluaran);
$total_pengeluaran = $data_pengeluaran['total_pengeluaran'] ?? 0;

// Hitung sisa budget
$sisa_budget = $total_rencana - $total_pengeluaran;

// Tampilkan hasil
echo "Total Rencana Budget Bulan ini : Rp. " . number_format($total_rencana, 0, ',', '.') . " ,-<br>";
echo "<span style='color:red'><b>Total Pengeluaran Real Bulan ini : Rp. " . number_format($total_pengeluaran, 0, ',', '.') . " ,-</b></span><br>";

$sisa_color = ($sisa_budget < 0) ? 'red' : 'green';
echo "<span style='color:$sisa_color'><b>Sisa Budget Bulan ini : Rp. " . number_format($sisa_budget, 0, ',', '.') . " ,-</b></span>";

?>


            </h3>
            <div class="btn-group pull-right">
              <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#modalTambahKategori">
                <i class="fa fa-plus"></i> &nbsp Tambah Kategori
              </button>
            </div>
          </div>

          <!-- Modal Tambah -->
          <form action="rencana_pengeluaran_act.php" method="post">
            <div class="modal fade" id="modalTambahKategori" tabindex="-1" role="dialog">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Tambah Kategori</h5>
                    <button type="button" class="close" data-dismiss="modal">
                      <span>&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <div class="form-group">
                      <label>Kategori Pengeluaran</label>
                      <select name="kategori" class="form-control" required>
                        <option value="">- Pilih -</option>
                        <?php
                        $kategori = mysqli_query($koneksi, "SELECT * FROM kategori WHERE jenis = 'Pengeluaran'");
                        while ($data = mysqli_fetch_array($kategori)) {
                          echo '<option value="' . $data['kategori_id'] . '">' . $data['kategori'] . '</option>';
                        }
                        ?>
                      </select>
                    </div>
                    <div class="form-group">
                      <label>Budget</label>
                      <input type="number" name="budget" class="form-control" required placeholder="Budget ..">
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                  </div>
                </div>
              </div>
            </div>
          </form>

          <div class="box-body">
            <div class="table-responsive">
              <table class="table table-bordered table-striped" id="table-datatable">
               
                <thead>
  <tr>
    <th width="1%">NO</th>
    <th>KATEGORI</th>
    <th>BUDGET</th>
    <th>REALISASI</th>
    <th>SISA</th>
    <th width="10%">OPSI</th>
  </tr>
</thead>
<tbody>
<?php
  $no = 1;
  $bulan_ini = date('Y-m');
  $awal_bulan = $bulan_ini . '-01';
  $akhir_bulan = $bulan_ini . '-31';

  $data = mysqli_query($koneksi, "
  SELECT 
    rb.rencana_id,
    rb.bulan, 
    k.kategori,
    k.kategori_id,
    rb.budget,
    IFNULL(SUM(t.transaksi_nominal), 0) AS realisasi,
    rb.budget - IFNULL(SUM(t.transaksi_nominal), 0) AS sisa_budget
  FROM rencana_bulanan rb
  JOIN kategori k ON rb.kategori_id = k.kategori_id
  LEFT JOIN transaksi t 
    ON t.transaksi_kategori = rb.kategori_id 
    AND MONTH(t.transaksi_tanggal) = MONTH(rb.bulan)
    AND YEAR(t.transaksi_tanggal) = YEAR(rb.bulan)
    AND LOWER(t.transaksi_jenis) = 'pengeluaran'
  WHERE DATE_FORMAT(rb.bulan, '%Y-%m') = '$bulan_ini'
  GROUP BY rb.kategori_id, rb.rencana_id, rb.bulan, k.kategori, rb.budget
  ORDER BY rb.rencana_id ASC
");



  while ($d = mysqli_fetch_array($data)) {
    ?>
    <tr>
      <td><?= $no++ ?></td>
      <td><?= $d['kategori'] ?></td>
      <td class="text-right">Rp. <?= number_format($d['budget']) ?> ,-</td>
      <td class="text-right">Rp. <?= number_format($d['realisasi']) ?> ,-</td>
      <td class="text-right">
        <?php if ($d['sisa_budget'] < 0): ?>
          <span class="text-danger">-Rp. <?= number_format(abs($d['sisa_budget'])) ?> ,-</span>
        <?php else: ?>
          Rp. <?= number_format($d['sisa_budget']) ?> ,-
        <?php endif; ?>
      </td>
      <td>
        <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#edit_kategori_<?= $d['rencana_id'] ?>">
          <i class="fa fa-cog"></i>
        </button>
        <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#hapus_kategori_<?= $d['rencana_id'] ?>">
          <i class="fa fa-trash"></i>
        </button>
      </td>
    </tr>

    <!-- Modal Edit -->
    <div class="modal fade" id="edit_kategori_<?= $d['rencana_id'] ?>">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="rencana_pengeluaran_update.php" method="post"> <!-- pindah ke sini -->
        <div class="modal-header">...</div>
        <div class="modal-body">
          <input type="hidden" name="id" value="<?= $d['rencana_id'] ?>">
          <div class="form-group">
            <label>Budget</label>
            <input type="number" name="budget" class="form-control" required value="<?= $d['budget'] ?>">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form> <!-- tutup form di sini -->
    </div>
  </div>
</div>


    <!-- Modal Hapus -->
    <div class="modal fade" id="hapus_kategori_<?= $d['rencana_id'] ?>" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Peringatan!</h5>
            <button type="button" class="close" data-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p>Yakin ingin menghapus data ini?</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            <a href="rencana_pengeluaran_hapus.php?id=<?= $d['rencana_id'] ?>" class="btn btn-primary">Hapus</a>
          </div>
        </div>
      </div>
    </div>

  <?php } ?>
</tbody>

              </table>
            </div>
          </div>
        </div>
      </section>
    </div>
  </section>
</div>
<?php include 'footer.php'; ?>
