<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Administrator - Sistem Informasi Keuangan</title>

  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="../assets/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="../assets/bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="../assets/dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="../assets/dist/css/custom-skin.css">

  <link rel="stylesheet" href="../assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">

  <link rel="stylesheet" href="../assets/dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="../assets/bower_components/morris.js/morris.css">
  <link rel="stylesheet" href="../assets/bower_components/jvectormap/jquery-jvectormap.css">
  <link rel="stylesheet" href="../assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
  <link rel="stylesheet" href="../assets/bower_components/bootstrap-daterangepicker/daterangepicker.css">
  <link rel="stylesheet" href="../assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

  <?php
  include '../koneksi.php';
  session_start();
  if ($_SESSION['status'] != "administrator_logedin") {
    header("location:../index.php?alert=belum_login");
  }
  ?>

</head>

<?php
function isActiveMenu($files = [], $type = 'li') {
    $current = basename($_SERVER['PHP_SELF']);
    if (!is_array($files)) $files = [$files];

    if (in_array($current, $files)) {
        if ($type === 'tree') return 'active menu-open';
        if ($type === 'ul') return 'display:block;';
        return 'active';
    }
    return '';
}
?>


<body class="hold-transition skin-blue sidebar-mini">

  <style>
    #table-datatable {
      width: 100% !important;
    }

    #table-datatable .sorting_disabled {
      border: 1px solid #f4f4f4;
    }
  </style>
  <div class="wrapper">

    <header class="main-header">
      <a href="index.php" class="logo">
        <span class="logo-mini"><b><i class="fa fa-money"></i></b> </span>
        <span class="logo-lg"><b>Keuangan</b></span>
      </a>
      <!-- navbar -->
      <nav class="navbar navbar-static-top"> 
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
          <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">
          <ul class="nav navbar-nav">

            <li class="dropdown user user-menu">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <?php
                $id_user = $_SESSION['id'];
                $profil = mysqli_query($koneksi, "select * from user where user_id='$id_user'");
                $profil = mysqli_fetch_assoc($profil);
                if ($profil['user_foto'] == "") {
                ?>
                  <img src="../gambar/sistem/user.png" class="user-image">
                <?php } else { ?>
                  <img src="../gambar/user/<?php echo $profil['user_foto'] ?>" class="user-image">
                <?php } ?>
                <span class="hidden-xs"><?php echo $_SESSION['nama']; ?> - <?php echo $_SESSION['level']; ?></span>
              </a>
            </li>
            <li>
              <a href="logout.php" onclick="return confirm('Apakah Anda yakin untuk logout?')">
                <i class="fa fa-sign-out"></i> LOGOUT
              </a>
            </li>
          </ul>
        </div>
      </nav>
      <!-- end Nav -->
    </header>

    <aside class="main-sidebar">
  <section class="sidebar">
    <div class="user-panel">
      <div class="pull-left image">
        <?php
        $id_user = $_SESSION['id'];
        $profil = mysqli_query($koneksi, "SELECT * FROM user WHERE user_id='$id_user'");
        $profil = mysqli_fetch_assoc($profil);
        ?>
        <img src="<?php echo $profil['user_foto'] ? '../gambar/user/' . $profil['user_foto'] : '../gambar/sistem/user.png'; ?>" class="img-circle" style="max-height:45px">
      </div>
      <div class="pull-left info">
        <p><?php echo $_SESSION['nama']; ?></p>
        <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
      </div>
    </div>

    <ul class="sidebar-menu" data-widget="tree">
      <li class="header">MAIN NAVIGATION</li>

      <li class="<?php echo isActiveMenu('index.php'); ?>">
        <a href="index.php"><i class="fa fa-home"></i> <span>DASHBOARD</span></a>
      </li>

      <li class="<?php echo isActiveMenu('kategori.php'); ?>">
        <a href="kategori.php"><i class="fa fa-tags"></i> <span>DATA KATEGORI</span></a>
      </li>

      <!-- <li class="<?php echo isActiveMenu('kategori.php'); ?>">
        <a href="tabungan.php"><i class="fa fa-tags"></i> <span>TABUNGAN</span></a>
      </li> -->

      <li class="<?php echo isActiveMenu('rencana_pengeluaran.php'); ?>">
        <a href="rencana_pengeluaran.php"><i class="fa fa-pencil-square-o"></i> <span>RENCANA PENGELUARAN</span></a>
      </li>

      <li class="<?php echo isActiveMenu('transaksi.php'); ?>">
        <a href="transaksi.php"><i class="fa fa-exchange"></i> <span>DATA TRANSAKSI</span></a>
      </li>

      <li class="<?php echo isActiveMenu('transfer.php'); ?>">
        <a href="transfer.php"><i class="fa fa-random"></i> <span>DATA TRANSFER</span></a>
      </li>

      <li class="treeview <?php echo isActiveMenu(['hutang.php', 'piutang.php'], 'tree'); ?>">
        <a href="#">
          <i class="fa fa-hand-paper-o"></i> <span>HUTANG PIUTANG</span>
          <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
        </a>
        <ul class="treeview-menu" style="<?php echo isActiveMenu(['hutang.php', 'piutang.php'], 'ul'); ?>">
          <li class="<?php echo isActiveMenu('hutang.php'); ?>"><a href="hutang.php"><i class="fa fa-circle-o"></i> Catatan Hutang</a></li>
          <li class="<?php echo isActiveMenu('piutang.php'); ?>"><a href="piutang.php"><i class="fa fa-circle-o"></i> Catatan Piutang</a></li>
        </ul>
      </li>

      <li class="treeview <?php echo isActiveMenu('transaksi_bank.php', 'tree'); ?>">
        <a href="#"><i class="fa fa-dollar"></i> <span>RINCIAN TRANSAKSI</span>
          <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
        </a>
        <ul class="treeview-menu" style="<?php echo isActiveMenu('transaksi_bank.php', 'ul'); ?>">
          <?php
          $sql = mysqli_query($koneksi, "SELECT * FROM bank");
          while ($data = mysqli_fetch_assoc($sql)) {
            $id_bank = $data['bank_id'];
            $nama_bank = strtoupper($data['bank_nama']);
            $active = (basename($_SERVER['PHP_SELF']) == 'transaksi_bank.php' && @$_GET['bank_id'] == $id_bank) ? 'active' : '';
            echo "<li class='$active'><a href='transaksi_bank.php?bank_id=$id_bank'><i class='fa fa-circle-o'></i> $nama_bank</a></li>";
          }
          ?>
        </ul>
      </li>

      <li class="<?php echo isActiveMenu('bank.php'); ?>">
        <a href="bank.php"><i class="fa fa-university"></i> <span>REKENING BANK</span></a>
      </li>

      <li class="treeview <?php echo isActiveMenu(['user.php', 'user_tambah.php'], 'tree'); ?>">
        <a href="#"><i class="fa fa-users"></i> <span>DATA PENGGUNA</span>
          <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
        </a>
        <ul class="treeview-menu" style="<?php echo isActiveMenu(['user.php', 'user_tambah.php'], 'ul'); ?>">
          <li class="<?php echo isActiveMenu('user.php'); ?>"><a href="user.php"><i class="fa fa-circle-o"></i> Data Pengguna</a></li>
          <li class="<?php echo isActiveMenu('user_tambah.php'); ?>"><a href="user_tambah.php"><i class="fa fa-circle-o"></i> Tambah Pengguna</a></li>
        </ul>
      </li>

      <li class="<?php echo isActiveMenu('laporan.php'); ?>">
        <a href="laporan.php"><i class="fa fa-file"></i> <span>LAPORAN</span></a>
      </li>

      <li class="<?php echo isActiveMenu('gantipassword.php'); ?>">
        <a href="gantipassword.php"><i class="fa fa-lock"></i> <span>GANTI PASSWORD</span></a>
      </li>

      <li>
        <a href="logout.php" onclick="return confirm('Apakah Anda yakin untuk logout?')">
          <i class="fa fa-sign-out"></i> <span>LOGOUT</span>
        </a>
      </li>
    </ul>
  </section>
</aside>
