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
  if(get_admin("a_role") > 2)
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
        <h1 style="font-size: 30px;">จัดการพนักงาน <small>(Admin only)</small></h1>
      </section>
      <section class="content mb-3">
        <div class="row">
          <div class="col-sm-12">
            <button type="button" onClick="open_modal_add()" class="btn btn-success btn-block">
              <i class="fas fa-plus"></i> เพิ่มพนักงาน
            </button>
          </div>
        </div>
      </section>
      <section class="content">
        <div class="row">
          <?php
          $q_1 = dd_q('SELECT * FROM admin_tb ORDER BY a_status DESC, a_role ASC');
          while($row = $q_1->fetch(PDO::FETCH_ASSOC))
          {
          ?>
          <div class="col-sm-3">
            <div class="card <?php if($row['a_status'] == "0"){echo "card-danger";}elseif($row['a_status'] == "1"){echo "card-success";}?> card-outline">
              <div class="card-body box-profile">
                <div class="row">
                  <div class="col-sm-12">
                    <span><strong>ชื่อผู้ใช้ : </strong><?=$row['a_user']?></span>
                  </div>
 
                  <div class="col-sm-12">
                    <span><strong>สิทธิ์ : </strong><?php if($row['a_role'] == "1"){echo "แอดมิน 👨‍💼";}elseif($row['a_role'] == "2"){echo "พนักงาน 👮‍♂️";}elseif($row['a_role'] == "0"){echo "ผู้ดูแลสูงสุด 👑";}?></span>
                  </div>
                  <div class="col-sm-12">
                    <span><strong>สถานะ : </strong><?php if($row['a_status'] == "0"){echo "ปิดใช้งาน";}elseif($row['a_status'] == "1"){echo "เปิดใช้งาน";}?></span>
                  </div>
                  <div class="col-sm-12 mb-1">
                    <span><strong>ใช้งานล่าสุด : </strong><?=$row['a_last_login']?></span>
                  </div>
                  <div class="col-sm-12 mb-1">
                    <button type="button" onClick="open_modal_edit('<?=$row['a_user']?>','<?=password_decode($row['a_password'])?>','<?=$row['a_role']?>','<?=$row['a_status']?>')" class="btn btn-success btn-block">
                      <i class="fas fa-edit"></i> แก้ไขข้อมูล
                    </button>
                  </div>
                  <div class="col-sm-12">
                    <button type="button" onClick="ondelete('<?=$row['a_user']?>')" class="btn btn-danger btn-block">
                      <i class="fas fa-trash-alt"></i> ลบข้อมูล
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php
          }
          ?>
        </div>
      </section>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="modal_data" tabindex="-1" role="dialog" aria-labelledby="modal_dataLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modal_dataLabel">ข้อมูลพนักงาน</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-sm-12 mb-2">
              <strong>ชื่อผู้ใช้</strong>
              <input type="text" class="form-control" id="txt_username">
            </div>
             <div class="col-sm-12 mb-2">
              <strong>รหัสผ่าน</strong>
              <input type="text" class="form-control" id="txt_password">
            </div>
            <div class="col-sm-12 mb-2">
              <strong>สิทธิ์</strong>
              <select class="form-control" id="ddl_role">
                <option value="">--- กรุณาเลือก ---</option>
                <option value="0">ผู้ดูแลสูงสุด 👑</option>
                <option value="1">แอดมิน 👨‍💼</option>
                <option value="2">พนักงาน 👮‍♂️</option>
                
              </select>
            </div>
            <div class="col-sm-12 mb-2">
              <strong>สถานะ</strong>
              <select class="form-control" id="ddl_status">
                <option value="">--- กรุณาเลือก ---</option>
                <option value="1">เปิดใช้งาน</option>
                <option value="0">ปิดใช้งาน</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
          <button type="button" onClick="onsave()" class="btn btn-primary">บันทึก</button>
          <input type="hidden" id="txt_type">
          <input type="hidden" class="form-control" id="hdf_username">
        </div>
      </div>
    </div>
  </div>
  <!-- Modal -->

</body>
<?php include('partials/_footer.php'); ?>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/data.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script type="text/javascript">
  function open_modal_add()
  {
    $("#txt_username").val('');
    $("#txt_username").prop( "disabled", false);
    $("#hdf_username").val('');
    $("#txt_password").val('');
    $("#ddl_role").val('');
    $("#ddl_status").val('');
    $("#txt_type").val('insertnaja');
    $('#modal_data').modal('show');
  }
  function open_modal_edit(user,pass,role,status)
  {
    $("#txt_username").val(user);
    $("#txt_username").prop( "disabled", true);
    $("#hdf_username").val(user);
    //$("#txt_password").val(pass);
    $("#ddl_role").val(role);
    $("#ddl_status").val(status);
    $("#txt_type").val('edit');
    $('#modal_data').modal('show');
  }

  function onsave()
  {
    var formData = new FormData();
    formData.append('txt_username', $("#txt_username").val());
    formData.append('hdf_username', $("#hdf_username").val());
    formData.append('txt_password', $("#txt_password").val());
    formData.append('ddl_role', $("#ddl_role").val());
    formData.append('ddl_status', $("#ddl_status").val());
    formData.append('type', $("#txt_type").val());
    formData.append('username', $("#username").val());
    formData.append('ip', $("#ip").val());
      $.ajax({
          type: 'POST',
          url: 'system/api_config_staff',
          data:formData,
          contentType: false,
          processData: false,
      }).done(function(res){
          result = res;
          alert(result.message);
          window.location = './config_staff';
          console.clear();
      }).fail(function(jqXHR){
          res = jqXHR.responseJSON;
          alert(res.message);
          console.clear();
      });
  }
  function ondelete(a_user)
  {
    var result = confirm("คุณต้องการลบข้อมูลนี้ ?");
    if (result) {
      var formData = new FormData();
      formData.append('a_user', a_user);
      formData.append('username', $("#username").val());
      formData.append('ip', $("#ip").val());
      formData.append('type', 'delete');

      $.ajax({
          type: 'POST',
          url: 'system/api_config_staff',
          data:formData,
          contentType: false,
          processData: false,
      }).done(function(res){
          result = res;
          alert(result.message);
          window.location = './config_staff';
          console.clear();
      }).fail(function(jqXHR){
          res = jqXHR.responseJSON;
          alert(res.message);
          console.clear();
      });
    }
  }
</script>
</html>