<?php
require_once 'api/config.php';
if(empty(get_session()))
{
  echo "<SCRIPT LANGUAGE='JavaScript'>
          window.location.href = './login';
        </SCRIPT>";
  exit();
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
        <h1 style="font-size: 30px;">ตั้งค่าบัญชีธนาคาร</h1>
      </section>
      <section class="content">
        <div class="row">
<!------------------------------------------------------------------------------------>
          <div class="col-sm-4">
            <div class="card card-scb card-outline">
              <div class="card-body box-profile">
                <div class="row">
                  <div class="col-sm-12 mb-3 text-center">
                    <?php
                    $q_scb = dd_q('SELECT * FROM autobank_tb WHERE a_bank_code=?', ['scb']);
                    $row_scb = $q_scb->fetch(PDO::FETCH_ASSOC);
                    ?>
                    <img style="width: 50px;" src="./images/bank/<?=$row_scb['a_bank_code']?>.png">
                    <br>
                    <span>
                      <code class="text-dark" style="font-size: 14px;">
                        Status:
                        <?php
                        if($row_scb['a_bank_run'] == "1")
                        {
                        ?>
                        <span class="bg-success"> Online</span>
                        <?php
                        }
                        else
                        {
                        ?>
                        <span class="bg-danger"> Offline</span>
                        <?php
                        }
                        ?>
                      </code>
                    </span>
                    <br>
                    <span>
                      <code class="text-dark" style="font-size: 14px;">
                        Last update: <?=date('d/m/Y H:i:s', strtotime($row_scb['a_bank_update']))?>
                      </code>
                    </span>
                  </div>
                  <div class="col-sm-12 mb-3">
                    <strong>ชื่อบัญชี</strong>
                    <input type="text" class="form-control" id="txt_scb_name" value="<?=$row_scb['a_bank_acc_name']?>">
                  </div>
                  <div class="col-sm-12 mb-3">
                    <strong>เลขบัญชี</strong>
                    <input type="text" class="form-control" id="txt_scb_number" value="<?=$row_scb['a_bank_acc_number']?>">
                    <span class="text-danger">Format การใส่คือ 000-0-00000-0</span>
                  </div>
                  <div class="col-sm-12 mb-1">
                    <?php
                    if($row_scb['a_bank_status'] == "1")
                    {
                    ?>
                    <button type="button" id="btn_close_scb" class="btn btn-danger btn-block">
                      <i class="fas fa-times"></i> ปิดใช้งาน
                    </button>
                    <?php
                    }
                    else if($row_scb['a_bank_status'] == "0")
                    {
                    ?>
                    <button type="button" id="btn_open_scb" class="btn btn-success btn-block">
                      <i class="fas fa-check"></i> เปิดใช้งาน
                    </button>
                    <?php
                    }
                    ?>
                  </div>
                  <div class="col-sm-12">
                    <button type="button" id="btn_save_scb" class="btn btn-scb btn-block">
                      <i class="fas fa-save"></i> บันทึกการตั้งค่า
                    </button>
 
                  </div>
                </div>
              </div>
            </div>
          </div>
<!------------------------------------------------------------------------------------>
          <div class="col-sm-4">
            <div class="card card-bay card-outline">
              <div class="card-body box-profile">
                <div class="row">
                  <div class="col-sm-12 mb-3 text-center">
                    <?php
                    $q_bay = dd_q('SELECT * FROM autobank_tb WHERE a_bank_code=?', ['noauto']);
                    $row_bay = $q_bay->fetch(PDO::FETCH_ASSOC);
                    ?>
                    ธนาคารแบบไม่ออโต้
                    <br>
                    <span>
                      <code class="text-dark" style="font-size: 14px;">
                        Status:
                        <?php
                        if($row_bay['a_bank_status'] == "1")
                        {
                        ?>
                        <span class="bg-success"> Online</span>
                        <?php
                        }
                        else
                        {
                        ?>
                        <span class="bg-danger"> Offline</span>
                        <?php
                        }
                        ?>
                      </code>
                    </span>
                    <br>
                    <span>
                      <code class="text-dark" style="font-size: 14px;">
                        Last update: <?=date('d/m/Y H:i:s', strtotime($row_bay['a_bank_update']))?>
                      </code>
                    </span>
                  </div>
                  <div class="col-sm-12 mb-3">
                    <strong>ชื่อบัญชี</strong>
                    <input type="text" class="form-control" id="txt_bay_name" value="<?=$row_bay['a_bank_acc_name']?>">
                  </div>
                  <div class="col-sm-12 mb-3">
                    <strong>เลขบัญชี</strong>
                    <input type="text" class="form-control" id="txt_bay_number" value="<?=$row_bay['a_bank_acc_number']?>">
                    <span class="text-danger">Format การใส่คือ 000-0-00000-0</span>
                  </div>
                  <div class="col-sm-12 mb-3">
                    <strong>เลือกธนาคาร</strong>
                    <select id="txt_bay_nameeng" class="form-control">
                      <option value="kbank" <?php if($row_bay['a_bank_acc_name_eng'] == "kbank"){echo "selected";} ?>>ธนาคารกสิกรไทย</option>
                      <option value="scb" <?php if($row_bay['a_bank_acc_name_eng'] == "scb"){echo "selected";} ?>>ธนาคารไทยพาณิชย์</option>
                      <option value="ktb" <?php if($row_bay['a_bank_acc_name_eng'] == "ktb"){echo "selected";} ?>>ธนาคารกรุงไทย</option>
                      <option value="bay" <?php if($row_bay['a_bank_acc_name_eng'] == "bay"){echo "selected";} ?>>ธนาคารกรุงศรีอยุธยา</option>
                      <option value="ttb" <?php if($row_bay['a_bank_acc_name_eng'] == "ttb"){echo "selected";} ?>>ธนาคารทหารไทยธนชาต</option>
                      <option value="gsb" <?php if($row_bay['a_bank_acc_name_eng'] == "gsb"){echo "selected";} ?>>ธนาคารออมสิน</option>
                      <option value="bbl" <?php if($row_bay['a_bank_acc_name_eng'] == "bbl"){echo "selected";} ?>>ธนาคารกรุงเทพ</option>
                  </select>
                  </div>
                  <div class="col-sm-12 mb-1">
                    <?php
                    if($row_bay['a_bank_status'] == "1")
                    {
                    ?>
                    <button type="button" id="btn_close_bay" class="btn btn-danger btn-block">
                      <i class="fas fa-times"></i> ปิดใช้งาน
                    </button>
                    <?php
                    }
                    else if($row_bay['a_bank_status'] == "0")
                    {
                    ?>
                    <button type="button" id="btn_open_bay" class="btn btn-success btn-block">
                      <i class="fas fa-check"></i> เปิดใช้งาน
                    </button>
                    <?php
                    }
                    ?>
                  </div>
                  <div class="col-sm-12">
                    <button type="button" id="btn_save_bay" class="btn btn-primary btn-block">
                      <i class="fas fa-save"></i> บันทึกการตั้งค่า
                    </button>
 
                  </div>
                </div>
              </div>
            </div>
          </div>
<!------------------------------------------------------------------------------------>
<div class="col-sm-4">
            <div class="card card-scb card-outline" style="border-top: 3px solid #138f2d;">
              <div class="card-body box-profile">
                <div class="row">
                  <div class="col-sm-12 mb-3 text-center">
                    <?php
                    $q_kbank = dd_q('SELECT * FROM autobank_tb WHERE a_bank_code=?', ['kbank']);
                    $row_kbank = $q_kbank->fetch(PDO::FETCH_ASSOC);
                    ?>
                    <img style="width: 50px;" src="./images/bank/<?=$row_kbank['a_bank_code']?>.png">
                    <br>
                    <span>
                      <code class="text-dark" style="font-size: 14px;">
                        Status:
                        <?php
                        if($row_kbank['a_bank_run'] == "1")
                        {
                        ?>
                        <span class="bg-success"> Online</span>
                        <?php
                        }
                        else
                        {
                        ?>
                        <span class="bg-danger"> Offline</span>
                        <?php
                        }
                        ?>
                      </code>
                    </span>
                    <br>
                    <span>
                      <code class="text-dark" style="font-size: 14px;">
                        Last update: <?=date('d/m/Y H:i:s', strtotime($row_kbank['a_bank_update']))?>
                      </code>
                    </span>
                  </div>
                  <div class="col-sm-12 mb-3">
                    <strong>ชื่อบัญชี</strong>
                    <input type="text" class="form-control" id="txt_kbank_name" value="<?=$row_kbank['a_bank_acc_name']?>">
                  </div>
                  <div class="col-sm-12 mb-3">
                    <strong>เลขบัญชี</strong>
                    <input type="text" class="form-control" id="txt_kbank_number" value="<?=$row_kbank['a_bank_acc_number']?>">
                    <span class="text-danger">Format การใส่คือ 000-0-00000-0</span>
                  </div>

                  <div id="accordion">
                    <div class="card">
                      <div class="card-header" id="heading1">
                        <h5 class="mb-0">
                          <button class="btn btn-link" data-toggle="collapse" data-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                            ตั้งค่าขั้นสูง
                          </button>
                        </h5>
                      </div>
                  
                      <div id="collapse1" class="collapse" aria-labelledby="heading1" data-parent="#accordion">
                        <div class="card-body">
                          <div class="col-sm-12 mb-3">
                              <strong>USER (Kcyber,Kbiz)</strong>
                              <input type="text" class="form-control" id="txt_kbank_username" value="<?=$row_kbank['a_bank_username']?>">
                            </div>
                            <div class="col-sm-12 mb-3">
                              <strong>PASS (Kcyber,Kbiz)</strong>
                              <input type="text" class="form-control" id="txt_kbank_password" value="<?=$row_kbank['a_bank_password']?>">
 
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="col-sm-12 mb-1">
                    <?php
                    if($row_kbank['a_bank_status'] == "1")
                    {
                    ?>
                    <button type="button" id="btn_close_kbank" class="btn btn-danger btn-block">
                      <i class="fas fa-times"></i> ปิดใช้งาน
                    </button>
                    <?php
                    }
                    else if($row_kbank['a_bank_status'] == "0")
                    {
                    ?>
                    <button type="button" id="btn_open_kbank" class="btn btn-success btn-block">
                      <i class="fas fa-check"></i> เปิดใช้งาน
                    </button>
                    <?php
                    }
                    ?>
                  </div>

                  <div class="col-sm-12">
                    <button type="button" id="btn_save_kbank" class="btn btn-scb btn-block" style="background-color: #138f2d;">
                      <i class="fas fa-save"></i> บันทึกการตั้งค่า
                    </button>
                     
                  </div>
                </div>
              </div>
            </div>
          </div>
<!------------------------------------------------------------------------------------>
       <!--   <div class="col-sm-4">
            <div class="card card-tmw card-outline">
              <div class="card-body box-profile">
                <div class="row">
                  <div class="col-sm-12 mb-3 text-center">
                    <?php
                    $q_tmw = dd_q('SELECT * FROM autobank_tb WHERE a_bank_code=?', ['tmw']);
                    $row_tmw = $q_tmw->fetch(PDO::FETCH_ASSOC);
                    ?>
                    <img style="width: 50px;" src="./images/bank/<?=$row_tmw['a_bank_code']?>.png">
                    <br>
                    <span>
                      <code class="text-dark" style="font-size: 14px;">
                        Status:
                        <?php
                        if($row_tmw['a_bank_run'] == "1")
                        {
                        ?>
                        <span class="bg-success"> Online</span>
                        <?php
                        }
                        else
                        {
                        ?>
                        <span class="bg-danger"> Offline</span>
                        <?php
                        }
                        ?>
                      </code>
                    </span>
                    <br>
                    <span>
                      <code class="text-dark" style="font-size: 14px;">
                        Last update: <?=date('d/m/Y H:i:s', strtotime($row_tmw['a_bank_update']))?>
                      </code>
                    </span>
                    <br>
                    <span>
                      <code style="font-size: 14px;">
                        <a href="https://tmw.push888.co" target="_blank">### ตั้งค่าบัญชี wallet ###</a>
                      </code>
                    </span>
                  </div>
                  <div class="col-sm-12 mb-3">
                    <strong>ชื่อบัญชี</strong>
                    <input type="text" class="form-control" id="txt_tmw_name" value="<?=$row_tmw['a_bank_acc_name']?>">
                  </div>
                  <div class="col-sm-12 mb-3">
                    <strong>เลขบัญชี</strong>
                    <input type="text" class="form-control" id="txt_tmw_number" value="<?=$row_tmw['a_bank_acc_number']?>">
                    <span class="text-danger">Format การใส่คือ 000-0000000</span>
                  </div>
                  <div class="col-sm-12 mb-3">
                    <strong>wallet_acc</strong>
                    <input type="text" class="form-control" id="txt_tmw_username" value="<?=$row_tmw['a_bank_username']?>">
                  </div>
                  <div class="col-sm-12 mb-1">
                    <?php
                    if($row_tmw['a_bank_status'] == "1")
                    {
                    ?>
                    <button type="button" id="btn_close_tmw" class="btn btn-danger btn-block">
                      <i class="fas fa-times"></i> ปิดใช้งาน
                    </button>
                    <?php
                    }
                    else if($row_tmw['a_bank_status'] == "0")
                    {
                    ?>
                    <button type="button" id="btn_open_tmw" class="btn btn-success btn-block">
                      <i class="fas fa-check"></i> เปิดใช้งาน
                    </button>
                    <?php
                    }
                    ?>
                  </div>
                  <div class="col-sm-12">
                    <button type="button" id="btn_save_tmw" class="btn btn-tmw btn-block">
                      <i class="fas fa-save"></i> บันทึกการตั้งค่า
                    </button>
			<input type="hidden" class="form-control" id="txt_tmw_password" value="<?=$row_tmw['a_bank_password']?>">
                  </div>
                </div>
              </div>
            </div>
          </div> -->

        </div>
      </section>
    </div>
  </div>
</body>
<?php include('partials/_footer.php'); ?>
<script type="text/javascript">

  $("#btn_close_scb").click(function(e) {
    e.preventDefault();
    var formData = new FormData();
    formData.append('type', 'btn_close_scb');
    formData.append('username', $("#username").val());
    formData.append('ip', $("#ip").val());

    $.ajax({
      type: 'POST',
      url: 'system/api_bank_config',
      data:formData,
      contentType: false,
      processData: false,
    }).done(function(res){
      result = res;
      alert(result.message);
      window.location = './bank_config';
      console.clear();
    }).fail(function(jqXHR){
      res = jqXHR.responseJSON;
      alert(res.message);
      console.clear();
    });
  });
  $("#btn_open_scb").click(function(e) {
    e.preventDefault();
    var formData = new FormData();
    formData.append('type', 'btn_open_scb');
    formData.append('username', $("#username").val());
    formData.append('ip', $("#ip").val());

    $.ajax({
      type: 'POST',
      url: 'system/api_bank_config',
      data:formData,
      contentType: false,
      processData: false,
    }).done(function(res){
      result = res;
      alert(result.message);
      window.location = './bank_config';
      console.clear();
    }).fail(function(jqXHR){
      res = jqXHR.responseJSON;
      alert(res.message);
      console.clear();
    });
  });
  $("#btn_save_scb").click(function(e) {
    e.preventDefault();
    var formData = new FormData();
    formData.append('a_bank_acc_name', $("#txt_scb_name").val());
    formData.append('a_bank_acc_number', $("#txt_scb_number").val());

    formData.append('type', 'btn_save_scb');
    formData.append('username', $("#username").val());
    formData.append('ip', $("#ip").val());

    $.ajax({
      type: 'POST',
      url: 'system/api_bank_config',
      data:formData,
      contentType: false,
      processData: false,
    }).done(function(res){
      result = res;
      alert(result.message);
      window.location = './bank_config';
      console.clear();
    }).fail(function(jqXHR){
      res = jqXHR.responseJSON;
      alert(res.message);
      console.clear();
    });
  });

  $("#btn_close_bay").click(function(e) {
    e.preventDefault();
    var formData = new FormData();
    formData.append('type', 'btn_close_bay');
    formData.append('username', $("#username").val());
    formData.append('ip', $("#ip").val());

    $.ajax({
      type: 'POST',
      url: 'system/api_bank_config',
      data:formData,
      contentType: false,
      processData: false,
    }).done(function(res){
      result = res;
      alert(result.message);
      window.location = './bank_config';
      console.clear();
    }).fail(function(jqXHR){
      res = jqXHR.responseJSON;
      alert(res.message);
      console.clear();
    });
  });
  $("#btn_open_bay").click(function(e) {
    e.preventDefault();
    var formData = new FormData();
    formData.append('type', 'btn_open_bay');
    formData.append('username', $("#username").val());
    formData.append('ip', $("#ip").val());

    $.ajax({
      type: 'POST',
      url: 'system/api_bank_config',
      data:formData,
      contentType: false,
      processData: false,
    }).done(function(res){
      result = res;
      alert(result.message);
      window.location = './bank_config';
      console.clear();
    }).fail(function(jqXHR){
      res = jqXHR.responseJSON;
      alert(res.message);
      console.clear();
    });
  });
  $("#btn_save_bay").click(function(e) {
    e.preventDefault();
    var formData = new FormData();
    formData.append('a_bank_acc_name', $("#txt_bay_name").val());
    formData.append('a_bank_acc_number', $("#txt_bay_number").val());
    formData.append('a_bank_acc_name_eng', $("#txt_bay_nameeng").val());
 
 
    formData.append('type', 'btn_save_bay');
    formData.append('username', $("#username").val());
    formData.append('ip', $("#ip").val());

    $.ajax({
      type: 'POST',
      url: 'system/api_bank_config',
      data:formData,
      contentType: false,
      processData: false,
    }).done(function(res){
      result = res;
      alert(result.message);
      window.location = './bank_config';
      console.clear();
    }).fail(function(jqXHR){
      res = jqXHR.responseJSON;
      alert(res.message);
      console.clear();
    });
  });

  $("#btn_close_tmw").click(function(e) {
    e.preventDefault();
    var formData = new FormData();
    formData.append('type', 'btn_close_tmw');
    formData.append('username', $("#username").val());
    formData.append('ip', $("#ip").val());

    $.ajax({
      type: 'POST',
      url: 'system/api_bank_config',
      data:formData,
      contentType: false,
      processData: false,
    }).done(function(res){
      result = res;
      alert(result.message);
      window.location = './bank_config';
      console.clear();
    }).fail(function(jqXHR){
      res = jqXHR.responseJSON;
      alert(res.message);
      console.clear();
    });
  });
  $("#btn_open_tmw").click(function(e) {
    e.preventDefault();
    var formData = new FormData();
    formData.append('type', 'btn_open_tmw');
    formData.append('username', $("#username").val());
    formData.append('ip', $("#ip").val());

    $.ajax({
      type: 'POST',
      url: 'system/api_bank_config',
      data:formData,
      contentType: false,
      processData: false,
    }).done(function(res){
      result = res;
      alert(result.message);
      window.location = './bank_config';
      console.clear();
    }).fail(function(jqXHR){
      res = jqXHR.responseJSON;
      alert(res.message);
      console.clear();
    });
  });
  $("#btn_save_tmw").click(function(e) {
    e.preventDefault();
    var formData = new FormData();
    formData.append('a_bank_acc_name', $("#txt_tmw_name").val());
    formData.append('a_bank_acc_number', $("#txt_tmw_number").val());
    formData.append('a_bank_username', $("#txt_tmw_username").val());
    formData.append('a_bank_password', $("#txt_tmw_password").val());

    formData.append('type', 'btn_save_tmw');
    formData.append('username', $("#username").val());
    formData.append('ip', $("#ip").val());

    $.ajax({
      type: 'POST',
      url: 'system/api_bank_config',
      data:formData,
      contentType: false,
      processData: false,
    }).done(function(res){
      result = res;
      alert(result.message);
      window.location = './bank_config';
      console.clear();
    }).fail(function(jqXHR){
      res = jqXHR.responseJSON;
      alert(res.message);
      console.clear();
    });
  });

  $("#btn_close_kbank").click(function(e) {
    e.preventDefault();
    var formData = new FormData();
    formData.append('type', 'btn_close_kbank');
    formData.append('username', $("#username").val());
    formData.append('ip', $("#ip").val());

    $.ajax({
      type: 'POST',
      url: 'system/api_bank_config',
      data:formData,
      contentType: false,
      processData: false,
    }).done(function(res){
      result = res;
      alert(result.message);
      window.location = './bank_config';
      console.clear();
    }).fail(function(jqXHR){
      res = jqXHR.responseJSON;
      alert(res.message);
      console.clear();
    });
  });
  $("#btn_open_kbank").click(function(e) {
    e.preventDefault();
    var formData = new FormData();
    formData.append('type', 'btn_open_kbank');
    formData.append('username', $("#username").val());
    formData.append('ip', $("#ip").val());

    $.ajax({
      type: 'POST',
      url: 'system/api_bank_config',
      data:formData,
      contentType: false,
      processData: false,
    }).done(function(res){
      result = res;
      alert(result.message);
      window.location = './bank_config';
      console.clear();
    }).fail(function(jqXHR){
      res = jqXHR.responseJSON;
      alert(res.message);
      console.clear();
    });
  });
  $("#btn_save_kbank").click(function(e) {
    e.preventDefault();
    var formData = new FormData();
    formData.append('a_bank_acc_name', $("#txt_kbank_name").val());
    formData.append('a_bank_acc_number', $("#txt_kbank_number").val());
    formData.append('a_bank_username', $("#txt_kbank_username").val());
    formData.append('a_bank_password', $("#txt_kbank_password").val());

    formData.append('type', 'btn_save_kbank');
    formData.append('username', $("#username").val());
    formData.append('ip', $("#ip").val());

    $.ajax({
      type: 'POST',
      url: 'system/api_bank_config',
      data:formData,
      contentType: false,
      processData: false,
    }).done(function(res){
      result = res;
      alert(result.message);
      window.location = './bank_config';
      console.clear();
    }).fail(function(jqXHR){
      res = jqXHR.responseJSON;
      alert(res.message);
      console.clear();
    });
  });

</script>
</html>