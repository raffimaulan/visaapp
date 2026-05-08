<?php
session_start();
require_once __DIR__ . '/../helper/csrf.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1" name="viewport">
  <title>Login — AKUVISA</title>

  <!-- Bootstrap -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">

  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css">

  <!-- Template -->
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/components.css">
</head>

<body>
<div id="app">
  <section class="section">
    <div class="container mt-5">
      <div class="row">
        <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-5 offset-lg-3">

          <!-- LOGO -->
          <div class="login-brand text-center mb-4">
            <img src="../assets/img/avatar/Akuvisa_logo.png" width="120">
          </div>

          <div class="card card-primary">
            <div class="card-header">
              <h4>Login Admin</h4>
            </div>

            <div class="card-body">

              <!-- ERROR -->
              <?php if(isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                  <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
              <?php endif; ?>

              <form method="POST" action="proses_login.php" class="needs-validation" novalidate>
          <?= csrf_field() ?>

                <div class="form-group">
                  <label>Email</label>
                  <input type="email" name="email" class="form-control" required autofocus>
                  <div class="invalid-feedback">Email wajib diisi</div>
                </div>

                <div class="form-group">
                  <label>Password</label>
                  <input type="password" name="password" class="form-control" required>
                  <div class="invalid-feedback">Password wajib diisi</div>
                </div>

                <div class="form-group">
                  <button type="submit" name="submit" class="btn btn-primary btn-lg btn-block">
                    Login
                  </button>
                </div>

              </form>

            </div>
          </div>

          <div class="text-center mt-3">
            <small>© AKUVISA</small>
          </div>

        </div>
      </div>
    </div>
  </section>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

<!-- VALIDATION SCRIPT -->
<script>
  (function () {
    'use strict';
    window.addEventListener('load', function () {
      var forms = document.getElementsByClassName('needs-validation');
      Array.prototype.filter.call(forms, function (form) {
        form.addEventListener('submit', function (event) {
          if (form.checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
          }
          form.classList.add('was-validated');
        }, false);
      });
    }, false);
  })();
</script>

</body>
</html>