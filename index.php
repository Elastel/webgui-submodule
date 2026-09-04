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
    <?php
      // Emit an import map that fingerprints each JS module with a
      // combined, aggregated filemtime of the whole app/js tree.
      // Browsers cache ES modules (import ... from "...") by bare URL
      // independent of the top-level script's query-string, so ANY change
      // inside app/js (or index.php itself) must bump the URL fingerprint
      // carried by every mapped specifier. Without this map, changes to
      // files like dct-rule.js would be permanently shadowed by the
      // previously cached (stale) module script in the browser.
      //
      // Strategy: assign every mapped import the SAME combined fingerprint.
      // This is intentionally simple/robust (no mismatches between
      // "latest fingerprint" and "per-file fingerprint" semantics), and
      // all URLs shift together whenever any JS asset changes.

      $_ts = max(
          intval(@filemtime(__FILE__) ?: 0),
          intval(@filemtime(__DIR__ . '/app/js/app.js') ?: 0)
      );
      foreach ((array)glob(__DIR__ . '/app/js/*.js') as $_f) {
          $_ts = max($_ts, intval(@filemtime($_f) ?: 0));
      }
      foreach ((array)glob(__DIR__ . '/app/js/modules/*.js') as $_f) {
          $_ts = max($_ts, intval(@filemtime($_f) ?: 0));
      }
      $_v = (string)($_ts > 0 ? $_ts : time());
      $_importMap = ['imports' => []];

      // 1) app/js root siblings imported from app.js via "./xxx.js".
      foreach ((array)glob(__DIR__ . '/app/js/*.js') as $_sf) {
          if (!is_file($_sf)) continue;
          $_bn = basename($_sf);
          // e.g. "./helpers.js" -> "/app/js/helpers.js?v=<bundleMtime>"
          $_importMap['imports']['./' . $_bn] = '/app/js/' . $_bn . '?v=' . $_v;
      }

      // 2) Modules imported from app.js via "./modules/xxx.js".
      foreach ((array)glob(__DIR__ . '/app/js/modules/*.js') as $_mf) {
          if (!is_file($_mf)) continue;
          $_bn = basename($_mf);
          $_importMap['imports']['./modules/' . $_bn] = '/app/js/modules/' . $_bn . '?v=' . $_v;
      }

      // 3) Cross-import inside modules: import "... from "../helpers.js"
      //    is resolved relative to the importing module's URL and results
      //    in "/app/js/helpers.js". Map it to the fingerprinted variant.
      $_importMap['imports']['../helpers.js'] = '/app/js/helpers.js?v=' . $_v;
    ?>
    <script type="importmap">
      <?= json_encode($_importMap, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>
    </script>
    <script type="module" src="app/js/app.js?v=<?= filemtime('app/js/app.js'); ?>"></script>

    <?php loadFooterScripts($extraFooterScripts); ?>
  </body>
</html>
