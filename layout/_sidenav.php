<div class="main-sidebar sidebar-style-2">
  <aside id="sidebar-wrapper">

    <div class="sidebar-brand">
      <a href="../dashboard/index.php" style="display:flex; align-items:center; justify-content:center; height:60px; padding:10px 0; overflow:hidden;">
        <img src="../assets/img/avatar/Akuvisa_logo.png" alt="AKUVISA" style="max-width:120px; max-height:40px; width:auto; height:auto; object-fit:contain; display:block;">
      </a>
    </div>
    <div class="sidebar-brand sidebar-brand-sm">
      <a href="../dashboard/index.php">AV</a>
    </div>

    <ul class="sidebar-menu">

      <li class="menu-header">Menu Utama</li>

      <li <?= (strpos($_SERVER['REQUEST_URI'], '/dashboard') !== false) ? 'class="active"' : '' ?>>
        <a class="nav-link" href="../dashboard/index.php">
          <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
        </a>
      </li>

      <li class="menu-header">Pengajuan Visa</li>

      <li class="dropdown <?= (strpos($_SERVER['REQUEST_URI'], '/applications') !== false) ? 'active' : '' ?>">
        <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
          <i class="fas fa-file-alt"></i> <span>Pengajuan</span>
        </a>
        <ul class="dropdown-menu">
          <li><a class="nav-link" href="../applications/index.php">Semua Pengajuan</a></li>
        </ul>
      </li>

      <li class="dropdown <?= (strpos($_SERVER['REQUEST_URI'], '/applicants') !== false) ? 'active' : '' ?>">
        <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
          <i class="fas fa-users"></i> <span>Pemohon</span>
        </a>
        <ul class="dropdown-menu">
          <li><a class="nav-link" href="../applicants/index.php">Semua Pemohon</a></li>
        </ul>
      </li>

      <li class="dropdown <?= (strpos($_SERVER['REQUEST_URI'], '/payments') !== false) ? 'active' : '' ?>">
        <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
          <i class="fas fa-money-bill-wave"></i> <span>Pembayaran</span>
        </a>
        <ul class="dropdown-menu">
          <li><a class="nav-link" href="../payments/index.php">Semua Pembayaran</a></li>
        </ul>
      </li>

      <li class="dropdown <?= (strpos($_SERVER['REQUEST_URI'], '/documents') !== false) ? 'active' : '' ?>">
        <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
          <i class="fas fa-folder-open"></i> <span>Dokumen</span>
        </a>
        <ul class="dropdown-menu">
          <li><a class="nav-link" href="../documents/index.php">Semua Dokumen</a></li>
        </ul>
      </li>

      <li class="menu-header">Laporan</li>

      <li <?= (strpos($_SERVER['REQUEST_URI'], '/reports') !== false) ? 'class="active"' : '' ?>>
        <a class="nav-link" href="../reports/index.php">
          <i class="fas fa-chart-bar"></i> <span>Export Laporan</span>
        </a>
      </li>

      <li class="menu-header">Akun</li>

      <li>
        <a class="nav-link" href="../auth/logout.php">
          <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
        </a>
      </li>

    </ul>
  </aside>
</div>