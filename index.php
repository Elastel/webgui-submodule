<?php
require_once 'includes/includes.php';
?><!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <?php echo \ElastPro\Tokens\CSRF::metaTag(); ?>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?php echo _("$hostname Configuration Portal"); ?></title>

    <!-- Bootstrap Core CSS -->
    <link href="dist/bootstrap/css/bootstrap.css" rel="stylesheet">

    <!-- SB-Admin-2 CSS -->
    <link href="dist/sb-admin-2/css/sb-admin-2.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link href="dist/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

    <!-- Huebee CSS -->
    <link href="dist/huebee/huebee.min.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="dist/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">

    <!-- Custom CSS -->
    <link href="<?php echo $theme_url; ?>" title="main" rel="stylesheet">

    <link rel="shortcut icon" type="image/png" href="<?php echo getFavicon($target, $hostname); ?>?ver=2.0">
    <link rel="apple-touch-icon" sizes="180x180" href="app/icons/apple-touch-icon.png">
    <link rel="icon" type="image/png" href="<?php echo getFavicon($target, $hostname); ?>" />
    <link rel="manifest" href="app/icons/site.webmanifest">
    <link rel="mask-icon" href="app/icons/safari-pinned-tab.svg" color="#b91d47">
    <meta name="msapplication-config" content="app/icons/browserconfig.xml">
    <meta name="msapplication-TileColor" content="#b91d47">
    <meta name="theme-color" content="#ffffff">
  </head>
  <body id="page-top" style="font-family:'Arial','Microsoft YaHei',sans-serif">
    <?php ob_start(); ?>
    <!-- Page Wrapper -->
    <div id="wrapper">
      <!-- Sidebar -->
      <?php require_once 'includes/sidebar.php'; ?>
      <!-- End of Sidebar -->

      <!-- Content Wrapper -->
      <div id="content-wrapper" class="d-flex flex-column">

      <!-- Main Content -->
      <div id="content">
        <!-- Begin Page Content -->
        <div class="container-fluid">
          <div class="load" id="loading" name="loading"></div>
          <?php
            $extraFooterScripts = array();
            handlePageActions($extraFooterScripts, $page);
          ?>
        </div><!-- /.container-fluid -->
      </div><!-- End of Main Content -->
    </div><!-- End of Page Wrapper -->
    <?php ob_end_flush(); ?>
    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top" style="display: inline;">
      <i class="fas fa-angle-up"></i>
    </a> 

    <!-- jQuery -->
    <script src="dist/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="dist/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript -->
    <script src="dist/jquery-easing/jquery.easing.min.js"></script>

    <!-- Chart.js JavaScript -->
    <script src="dist/chart.js/Chart.min.js"></script>

    <!-- SB-Admin-2 JavaScript -->
    <script src="dist/sb-admin-2/js/sb-admin-2.js"></script>

    <!-- Custom JS -->
    <script type="module" src="app/js/app.js?v=<?= filemtime('app/js/app.js'); ?>"></script>

    <?php loadFooterScripts($extraFooterScripts); ?>
  </body>
</html>
