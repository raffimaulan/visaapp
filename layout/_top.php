<?php
require_once '../helper/auth.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Dashboard &mdash; AKUVISA</title>

  <!-- Bootstrap -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css">

  <!-- CSS Libraries -->
  <link rel="stylesheet" href="../assets/modules/jqvmap/dist/jqvmap.min.css">
  <link rel="stylesheet" href="../assets/modules/summernote/summernote-bs4.css">
  <link rel="stylesheet" href="../assets/modules/owlcarousel2/dist/assets/owl.carousel.min.css">
  <link rel="stylesheet" href="../assets/modules/owlcarousel2/dist/assets/owl.theme.default.min.css">
  <link rel="stylesheet" href="../assets/modules/datatables/datatables.min.css">
  <link rel="stylesheet" href="../assets/modules/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/modules/datatables/Select-1.2.4/css/select.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/modules/izitoast/css/iziToast.min.css">

  <!-- Template CSS -->
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/components.css">

  <!-- Night Mode -->
  <link rel="stylesheet" href="../assets/css/night-mode.css">
  <script>
    // Apply night mode BEFORE body renders (prevent flash).
    // Langsung set background + tambah class ke <body> agar semua
    // elemen langsung gelap sejak render pertama, tanpa animasi.
    (function () {
      if (localStorage.getItem('akuvisa_night_mode') === '1') {
        document.documentElement.style.backgroundColor = '#0f1117';
        // Pasang class setelah <body> tersedia (document.write sudah selesai)
        document.addEventListener('DOMContentLoaded', function () {
          // applyMode() di night-mode.js akan menangani ini,
          // tapi kita set lebih awal via style agar tidak ada flash
        });
        // Inject style inline agar <body> langsung dark sebelum CSS eksternal siap
        var s = document.createElement('style');
        s.textContent = 'body{background:#0f1117!important;color:#e2e8f0!important;}';
        document.head.appendChild(s);
      }
    })();
  </script>
</head>

<body>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <?php
      require_once __DIR__ . '/_header.php';
      require_once __DIR__ . '/_sidenav.php';
      ?>
      <!-- Main Content -->
      <div class="main-content">