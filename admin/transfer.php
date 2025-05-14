<?php include 'header.php'; ?>

<div class="content-wrapper">

  <section class="content-header">
    <h1>
      Transfer
      <small>Data Transfer</small>
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
            <h3 class="box-title">Transfer Bank</h3>
            <div class="btn-group pull-right">            

              <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#exampleModal">
                <i class="fa fa-plus"></i> &nbsp Tambah Transfer
              </button>
            </div>
          </div>
          <div class="box-body">

            <!-- Modal -->
           <!-- Modal Tambah Transfer -->
<form action="transfer_act.php" method="post">
  <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Tambah Transfer</h4>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">

          <div class="form-group">
            <label>Tanggal</label>
            <input type="text" name="tanggal" required class="form-control datepicker2">
          </div>

          <div class="form-group">
            <label>Rekening Asal</label>
            <select name="rekening_asal" class="form-control" required>
              <option value="">- Pilih -</option>
              <?php 
              $bank = mysqli_query($koneksi,"SELECT * FROM bank");
              while($b = mysqli_fetch_array($bank)){
                echo "<option value='$b[bank_id]'>$b[bank_nama]</option>";
              }
              ?>
            </select>
          </div>

          <div class="form-group">
            <label>Rekening Tujuan</label>
            <select name="rekening_tujuan" class="form-control" required>
              <option value="">- Pilih -</option>
              <?php 
              $bank = mysqli_query($koneksi,"SELECT * FROM bank");
              while($b = mysqli_fetch_array($bank)){
                echo "<option value='$b[bank_id]'>$b[bank_nama]</option>";
              }
              ?>
            </select>
          </div>

          <div class="form-group">
            <label>Nominal</label>
            <input type="number" name="nominal" required class="form-control" placeholder="Masukkan Nominal ..">
          </div>

          <div class="form-group">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control" rows="3"></textarea>
          </div>

          <input type="hidden" name="create_who" value="<?php echo $_SESSION['id']; ?>">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </div>
    </div>
  </div>
</form>



<div class="table-responsive">
  <table class="table table-bordered table-striped" id="table-datatable">
    <thead>
      <tr>
        <th width="1%">NO</th>
        <th class="text-center">TANGGAL</th>
        <th class="text-center">REKENING ASAL</th>
        <th class="text-center">REKENING TUJUAN</th>
        <th class="text-center">NOMINAL</th>
        <th class="text-center">KETERANGAN</th>
        <th class="text-center" width="10%">OPSI</th>
      </tr>
    </thead>
    <tbody>
      <?php 
      include '../koneksi.php';
      $no=1;
      $data = mysqli_query($koneksi,"
        SELECT 
          t.transfer_id,
          t.tanggal,
          t.nominal,
          t.keterangan,
          t.rekening_asal,
          t.rekening_tujuan,
          b1.bank_nama AS bank_asal,
          b2.bank_nama AS bank_tujuan
        FROM transfer t 
        LEFT JOIN bank b1 ON t.rekening_asal = b1.bank_id 
        LEFT JOIN bank b2 ON t.rekening_tujuan = b2.bank_id 
        ORDER BY t.tanggal DESC
      ");
      while($d = mysqli_fetch_array($data)){
      ?>
      <tr>
        <td class="text-center"><?php echo $no++; ?></td>
        <td class="text-center"><?php echo date('d-m-Y', strtotime($d['tanggal'])); ?></td>
        <td><?php echo $d['bank_asal']; ?></td>
        <td><?php echo $d['bank_tujuan']; ?></td>
        <td class="text-right">Rp. <?php echo number_format($d['nominal'],0,',','.'); ?> ,-</td>
        <td><?php echo $d['keterangan']; ?></td>
        <td class="text-center">
          <!-- Tombol Edit -->
          <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#edit_<?php echo $d['transfer_id'] ?>">
            <i class="fa fa-cog"></i>
          </button>

          <!-- Tombol Hapus -->
          <!-- <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#hapus_<?php echo $d['transfer_id'] ?>">
            <i class="fa fa-trash"></i>
          </button> -->
        </td>
      </tr>

      <!-- Modal Edit Transfer -->

<!-- Modal -->
<div class="modal fade" id="edit_<?php echo $d['transfer_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="editLabel_<?php echo $d['transfer_id'] ?>" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <!-- FORM DITARUH DI SINI YA -->
      <form action="transfer_update.php" method="post">
        <div class="modal-header">
          <h4 class="modal-title" id="editLabel_<?php echo $d['transfer_id'] ?>">Edit Transfer</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span>&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="id" value="<?php echo $d['transfer_id'] ?>">

          <div class="form-group">
            <label>Tanggal</label>
            <input type="text" name="tanggal" class="form-control datepicker2" value="<?php echo $d['tanggal'] ?>" required>
          </div>

          <div class="form-group">
            <label>Rekening Asal</label>
            <select name="rekening_asal" class="form-control" required>
              <option value="">- Pilih -</option>
              <?php 
              $bank = mysqli_query($koneksi, "SELECT * FROM bank");
              while($b = mysqli_fetch_array($bank)){
                $selected = ($b['bank_id'] == $d['rekening_asal']) ? "selected" : "";
                echo "<option value='$b[bank_id]' $selected>$b[bank_nama]</option>";
              }
              ?>
            </select>
          </div>

          <div class="form-group">
            <label>Rekening Tujuan</label>
            <select name="rekening_tujuan" class="form-control" required>
              <option value="">- Pilih -</option>
              <?php 
              $bank = mysqli_query($koneksi, "SELECT * FROM bank");
              while($b = mysqli_fetch_array($bank)){
                $selected = ($b['bank_id'] == $d['rekening_tujuan']) ? "selected" : "";
                echo "<option value='$b[bank_id]' $selected>$b[bank_nama]</option>";
              }
              ?>
            </select>
          </div>

          <div class="form-group">
            <label>Nominal</label>
            <input type="number" name="nominal" class="form-control" value="<?php echo $d['nominal'] ?>" required min="1">
          </div>

          <div class="form-group">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control" rows="3"><?php echo $d['keterangan'] ?></textarea>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>

    </div>
  </div>
</div>


      <!-- Modal Hapus Transfer -->
      <div class="modal fade" id="hapus_<?php echo $d['transfer_id'] ?>" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Konfirmasi</h4>
              <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
              <p>Yakin ingin menghapus data transfer ini?</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
              <a href="transfer_hapus.php?id=<?php echo $d['transfer_id'] ?>" class="btn btn-danger">Hapus</a>
            </div>
          </div>
        </div>
      </div>

      <?php } ?>
    </tbody>
  </table>
</div>


        </div>
      </section>
    </div>
  </section>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){
  // Untuk kategori dinamis
  $(document).on('change', '.jenis', function(){
    var jenis = $(this).val();
    var el = $(this);
    var parent = el.closest('.modal-body');
    var kategoriDropdown = parent.find('.kategori');
    $.post('get_kategori.php', {jenis: jenis}, function(data){
      kategoriDropdown.html(data);
    });
  });
});
</script>
<?php include 'footer.php'; ?>