<?php
require_once 'api/config.php';
if(empty(get_session()))
{
  echo "<SCRIPT LANGUAGE='JavaScript'>
          window.location.href = './login';
        </SCRIPT>";
  exit();
}
else
{
  // ===== get permission =====
  if(get_admin("a_role") != "1")
  {
    echo "<SCRIPT LANGUAGE='JavaScript'>
    window.location.href = './unauthorized';
    </SCRIPT>";
    exit;
  }
  // ===== get permission =====
}
?>
<!DOCTYPE html>
<html>
<head>
  <?php include("master/MasterPages.php"); ?>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed text-sm">
  <input type="hidden" id="username" value="<?=get_session()?>">
  <input type="hidden" id="ip" value="<?=get_client_ip()?>">
  <div class="wrapper">
    <?php include ("partials/_navbar.php"); ?>
    <?php include ("partials/_sidebar.php"); ?>
    <div class="content-wrapper">
      <section class="content-header pt-4 pb-4">
        <h1 style="font-size: 30px;">ตั้งค่า Affiliate <small>(Admin only)</small></h1>
      </section>
      <section class="content">
        <form method="POST">
          <div class="row">
            <div class="col-sm-6">
              <input name="title" class="form-control" type="text" value="<?=$row_u['aff_step']?>">
            </div>
            <div class="col-sm-6">
              
            </div>
          </div>
        </form>
      </section>
    </div>
  </div>

</body>
<?php include('partials/_footer.php'); ?>
</html>