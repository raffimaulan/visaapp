</div><!-- end main-content -->

      <footer class="main-footer">
        <div class="footer-left">
          Copyright &copy; <?= date('Y') ?> <strong>AKUVISA</strong>. All Rights Reserved.
        </div>
        <div class="footer-right">
          AKUVISA
        </div>
      </footer>

    </div><!-- end main-wrapper -->
  </div><!-- end app -->

<!-- General JS -->
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.nicescroll/3.7.6/jquery.nicescroll.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>

<!-- JS Libraries -->
<script src="../assets/modules/jquery.sparkline.min.js"></script>
<script src="../assets/modules/owlcarousel2/dist/owl.carousel.min.js"></script>
<script src="../assets/modules/summernote/summernote-bs4.js"></script>
<script src="../assets/modules/chocolat/dist/js/jquery.chocolat.min.js"></script>
<script src="../assets/modules/datatables/datatables.min.js"></script>
<script src="../assets/modules/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js"></script>
<script src="../assets/modules/datatables/Select-1.2.4/js/dataTables.select.min.js"></script>
<script src="../assets/modules/jquery-ui/jquery-ui.min.js"></script>
<script src="../assets/modules/izitoast/js/iziToast.min.js"></script>

<!-- Template JS -->
<script src="../assets/js/stisla.js"></script>
<script src="../assets/js/scripts.js"></script>
<script src="../assets/js/custom.js"></script>

<!-- Page-specific JS — loaded after jQuery & DataTables -->
<?php
$page_js = [
    'applications' => 'application-index.js',
    'applicants'   => 'applicant-index.js',
    'payments'     => 'payment-index.js',
    'documents'    => 'document-index.js',
];
$folder = basename(dirname($_SERVER['SCRIPT_FILENAME']));
if (isset($page_js[$folder]) && basename($_SERVER['SCRIPT_FILENAME']) === 'index.php') {
    echo '<script src="../assets/js/' . $page_js[$folder] . '"></script>' . "\n";
}
?>

<!-- Night Mode JS -->
<script src="../assets/js/night-mode.js"></script>

</body>
</html>