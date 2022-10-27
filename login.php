<?php
require_once 'api/config.php';
?>
<!DOCTYPE html>
<html>
<head>
  <?php include("master/MasterPages.php"); ?>
</head>
<style>
body {
  background: #202020;
}

</style>
<body>
  <input type="hidden" id="ip" value="<?=get_client_ip()?>">
  <section class="container">
    <div class="row justify-content-center">
      <div class="col-md-12 text-center">
        <img class=" " width="300px" src="./images/logov1.png">
      </div>
    </div>
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card card-primary shadow">
          <div class="card-header">
            <h3 class="card-title text-center p-2" style="float: none;">ระบบจัดการข้อมูล</h3>
          </div>
          <div class="card-body">
            <div class="form-group row">
              <div class="col-sm-12">
                <label class="col-form-label">ชื่อผู้ใช้งาน</label>
                <input type="text" class="form-control" id="username" required>
              </div>
            </div>
            <div class="form-group row">
              <div class="col-sm-12">
                <label class="col-form-label">รหัสผ่าน</label>
                <input type="password" class="form-control" id="password" required>
              </div>
            </div>
            <div class="form-group row">
              <div class="col-sm-12">
                <button type="button" class="btn btn-primary btn-block" id="btn_login">เข้าสู่ระบบ</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <script src="assets/plugins/jquery/jquery.min.js"></script>
  <script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/plugins/datatables/jquery.dataTables.js"></script>
  <script src="assets/plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
  <script src="assets/plugins/bootstrap-tagsinput/tagsinput.js?v=1"></script>
  <script src="assets/plugins/select2/js/select2.full.min.js"></script>
  <script src="assets/dist/js/adminlte.min.js"></script>
  <script type="text/javascript" src="assets/plugins/bootstrap-datepicker-thai/js/bootstrap-datepicker.js"></script>
  <script type="text/javascript" src="assets/plugins/bootstrap-datepicker-thai/js/bootstrap-datepicker-thai.js"></script>
  <script type="text/javascript" src="assets/plugins/bootstrap-datepicker-thai/js/locales/bootstrap-datepicker.th.js"></script>
  <script src="js/script.inc.51yua5qtehpwryzsgxoh.js" type="text/javascript"></script>
  <script src="js/js.js" type="text/javascript"></script>
</body>
</html>
