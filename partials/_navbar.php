<nav class="main-header  navbar navbar-expand navbar-primary navbar-dark">
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''?>"  href="<?=base_url()?>/dashboard"><i class="fas fa-tachometer-alt"></i> Paylegacy</a>
    </li>
  </ul>
  <ul class="navbar-nav ml-auto">
    <li class="nav-item">
      <div id="lbl_datetime" class="nav-link"></div>
    </li>
  </ul>
</nav>