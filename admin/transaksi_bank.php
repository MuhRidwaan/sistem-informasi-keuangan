<?php
include 'header.php';
include '../koneksi.php';

$bank_id = isset($_GET['bank_id']) ? intval($_GET['bank_id']) : 0;

// Ambil data bank
$sql = "SELECT * FROM bank WHERE bank_id = '$bank_id'";
$result = mysqli_query($koneksi, $sql);
$bank = mysqli_fetch_assoc($result);
?>

<div class="content-wrapper">

  <section class="content-header">
    <h1>
      Transaksi
      <small>Data Transaksi</small>
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
            <h3 class="box-title">Transaksi Bank <?php echo $bank['bank_nama']; ?></h3>
            <div class="btn-group pull-right">
              <p><b>Total Saldo:</b> 
                <?php
                echo "Rp. " . number_format($bank['bank_saldo']) . " ,-";
                ?>
              </p>
            </div>
          </div>

          <div class="box-body">
    

            <!-- Tabel Transaksi -->
            <div class="table-responsive mt-3">
              <table class="table table-bordered table-striped" id="table-datatable">
                <thead>
                  <tr>
                    <th width="1%">NO</th>
                    <th class="text-center">TANGGAL</th>
                    <th class="text-center">KATEGORI</th>
                    <th class="text-center">PEMASUKAN</th>
                    <th class="text-center">PENGELUARAN</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $no = 1;
                  $data = mysqli_query($koneksi, "SELECT * FROM transaksi, kategori WHERE kategori_id = transaksi_kategori AND transaksi_bank = '$bank_id' ORDER BY transaksi_id DESC");
                  while ($d = mysqli_fetch_array($data)) {
                  ?>
                    <tr>
                      <td class="text-center"><?php echo $no++; ?></td>
                      <td class="text-center"><?php echo date('d-m-Y', strtotime($d['transaksi_tanggal'])); ?></td>
                      <td><?php echo $d['kategori']; ?></td>
                      <td class="text-center">
                        <?php echo $d['transaksi_jenis'] == "Pemasukan" ? "Rp. " . number_format($d['transaksi_nominal']) . " ,-": "-"; ?>
                      </td>
                      <td class="text-center">
                        <?php echo $d['transaksi_jenis'] == "Pengeluaran" ? "Rp. " . number_format($d['transaksi_nominal']) . " ,-": "-"; ?>
                      </td>
                    </tr>
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
