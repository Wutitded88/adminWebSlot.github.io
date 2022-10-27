<?php
require_once 'api/config.php';
if(empty(get_session()))
{
  echo "<SCRIPT LANGUAGE='JavaScript'>
          window.location.href = './login';
        </SCRIPT>";
  exit();
}

if(isset($_REQUEST['btn_save_data']))
{
  set_config_website("withdraw_auto", $_POST['ddl_withdraw_auto']);
  set_config_website("withdraw_auto_max", $_POST['txt_withdraw_auto_max']);
  set_config_website("withdraw_auto_user_all", $_POST['ddl_withdraw_auto_user_all']);

  echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";
  echo ("<script LANGUAGE='JavaScript'>
    window.alert('บันทึกข้อมูลสำเร็จ');
    window.location.href='".base_url()."/withdraw_auto';
    </script>");
  exit;
}
else if(isset($_REQUEST['btn_save_user']))
{
  $q_1 = dd_q('SELECT * FROM user_tb WHERE u_user = ?', [trim($_POST['txt_w_u_user'])]);
  if ($q_1->rowCount() > 0)
  {
    $row_1 = $q_1->fetch(PDO::FETCH_ASSOC);

    $q_2 = dd_q('SELECT * FROM withdraw_auto_tb WHERE w_u_user = ?', [trim($_POST['txt_w_u_user'])]);
    if ($q_2->rowCount() == 0)
    {
      dd_q('INSERT INTO withdraw_auto_tb (w_u_id, w_u_user) VALUES (?, ?)', [
        $row_1['u_id'],
        $row_1['u_user']
      ]);
      echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";
      echo ("<script LANGUAGE='JavaScript'>
        window.alert('บันทึกข้อมูลสำเร็จ');
        window.location.href='".base_url()."/withdraw_auto';
        </script>");
      exit;
    }
    else
    {
      echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";
      echo ("<script LANGUAGE='JavaScript'>
        window.alert('คุณเพิ่ม User นี้ไว้แล้ว');
        window.location.href='".base_url()."/withdraw_auto';
        </script>");
      exit;
    }
  }
  else
  {
    echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";
    echo ("<script LANGUAGE='JavaScript'>
      window.alert('ไม่พบข้อมูล User นี้ในฐานข้อมูล');
      window.location.href='".base_url()."/withdraw_auto';
      </script>");
    exit;
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <?php include("master/MasterPages.php"); ?>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed text-sm">
  <div class="wrapper">
    <?php include ("partials/_navbar.php"); ?>
    <?php include ("partials/_sidebar.php"); ?>
    <div class="content-wrapper">
      <section class="content-header pt-4 pb-4">
        <h1 style="font-size: 30px;">ตั้งค่าถอนเงินออโต้แบบไม่ต้องกดอนุมัติ
        </h1>
      </section>
      <section class="content">
        <div class="row">
          <div class="col-sm-12">
            <div class="card card-success card-outline">
              <div class="card-body box-profile">
                <form method="POST">
                  <div class="row">
                    <div class="col-sm-6 mb-3">
                      <label>เปิด-ปิด ถอนออโต้</label>
                      <select class="form-control" name="ddl_withdraw_auto" required>
                        <option value="1" <?php if(get_config_website("withdraw_auto") == "1"){echo "selected";}?> >เปิด</option>
                        <option value="0" <?php if(get_config_website("withdraw_auto") == "0"){echo "selected";}?> >ปิด</option>
                      </select>
                    </div>
                    <div class="col-sm-6 mb-3">
                      <label>ยอดเงินสูงสุดที่ให้ถอนออโต้ <span class="text-danger">(ใส่ 0 = ไม่จำกัดยอดเงิน)</span></label>
                      <input type="number" class="form-control" step="0.1" name="txt_withdraw_auto_max" value="<?=get_config_website("withdraw_auto_max")?>" required>
                    </div>
                    <div class="col-sm-6 mb-3">
                      <label>เปิด-ปิด ถอนออโต้ให้ผู้เล่นทุกคน</label>
                      <select class="form-control" name="ddl_withdraw_auto_user_all" required>
                        <option value="1" <?php if(get_config_website("withdraw_auto_user_all") == "1"){echo "selected";}?> >เปิด</option>
                        <option value="0" <?php if(get_config_website("withdraw_auto_user_all") == "0"){echo "selected";}?> >ปิด</option>
                      </select>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-sm-12">
                      <button type="submit" class="btn btn-success btn-block" name="btn_save_data">บันทึกข้อมูล</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </section>
      <section class="content">
        <div class="row">
          <div class="col-sm-8">
            <div class="card card-outline">
              <div class="card-body box-profile">
                <div class="table-responsive">
                  <table class="table" id="tb_data" style="width: 100%;">
                    <thead class="text-center">
                      <tr>
                        <th scope="col">ยูเซอร์</th>
                        <th scope="col">ชื่อ นามสกุล</th>
                        <th scope="col">จำนวนเงินถอน</th>
                        <th scope="col">ธนาคาร</th>
                        <th scope="col">สำเร็จโดย</th>
                        <th scope="col">วันสร้าง</th>
                        <th scope="col">เวลาสร้าง</th>
                        <th scope="col">สถานะ</th>
                      </tr>
                    </thead>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-4">
            <div class="card card-outline">
              <div class="card-body box-profile">
                <?php
                if(get_config_website("withdraw_auto_user_all") == "0")
                {
                ?>
                <div class="row">
                  <div class="col-sm-12 pb-3">
                    <form method="POST">
                      <div class="row">
                        <div class="col-sm-12 mb-3">
                          <label>User ผู้เล่นที่ต้องการถอนออโต้</label>
                          <input type="text" class="form-control" maxlength="10" name="txt_w_u_user" placeholder="กรอก User เช่น 0851234567" required>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-12">
                          <button type="submit" class="btn btn-success btn-block" name="btn_save_user">บันทึกข้อมูล</button>
                        </div>
                      </div>
                    </form>
                  </div>
                  <div class="col-sm-12">
                    <div class="table-responsive">
                      <table class="table" id="tb_user" style="width: 100%;">
                        <thead class="text-center">
                          <tr>
                            <th scope="col">ยูเซอร์</th>
                            <th scope="col">ชื่อ นามสกุล</th>
                            <th scope="col">#</th>
                          </tr>
                        </thead>
                      </table>
                    </div>
                  </div>
                </div>
                <?php
                }
                else
                {
                ?>
                <div class="row">
                  <div class="col-sm-12 text-center">
                    <h1 class="text-danger">ถอนออโต้ให้ทุกคน</h1>
                  </div>
                </div>
                <?php
                }
                ?>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</body>
<?php include('partials/_footer.php'); ?>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/data.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script type="text/javascript">
  $(function () {
    $('#tb_data').DataTable({
      "paging": true,
      "lengthChange": true,
      "searching": true,
      "ordering": false,
      "info": true,
      "autoWidth": false,
      "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "ทั้งหมด"]],
      "language": {
        "emptyTable":     "ไม่พบข้อมูล",
        "info":           "แสดงข้อมูลแถวที่ _START_ ถึง _END_ จากทั้งหมด _TOTAL_ แถว",
        "infoEmpty":      "ไม่พบข้อมูล",
        "infoFiltered":   "(ค้นหาจากข้อมูลทั้งหมด _MAX_ แถว)",
        "infoPostFix":    "",
        "thousands":      ",",
        "lengthMenu":     "การแสดงผล _MENU_ แถว",
        "search":         "ค้นหา:",
        "zeroRecords":    "ไม่พบข้อมูลที่ค้นหา",
        "paginate": {
            "first":      "หน้าแรก",
            "last":       "หน้าสุดท้าย",
            "next":       "ถัดไป",
            "previous":   "ก่อนหน้า"
        }
      },
      "processing": true,
      "serverSide": true,
      "ajax": "./server-side/server_withdraw_auto.php"
    });

    $('#tb_user').DataTable({
      "paging": true,
      "lengthChange": true,
      "searching": true,
      "ordering": false,
      "info": true,
      "autoWidth": false,
      "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "ทั้งหมด"]],
      "language": {
        "emptyTable":     "ไม่พบข้อมูล",
        "info":           "แสดงข้อมูลแถวที่ _START_ ถึง _END_ จากทั้งหมด _TOTAL_ แถว",
        "infoEmpty":      "ไม่พบข้อมูล",
        "infoFiltered":   "(ค้นหาจากข้อมูลทั้งหมด _MAX_ แถว)",
        "infoPostFix":    "",
        "thousands":      ",",
        "lengthMenu":     "การแสดงผล _MENU_ แถว",
        "search":         "ค้นหา:",
        "zeroRecords":    "ไม่พบข้อมูลที่ค้นหา",
        "paginate": {
            "first":      "หน้าแรก",
            "last":       "หน้าสุดท้าย",
            "next":       "ถัดไป",
            "previous":   "ก่อนหน้า"
        }
      },
      "processing": true,
      "serverSide": true,
      "ajax": "./server-side/server_withdraw_auto_user.php"
    });
  });

  function onDelete(w_id) {
    var formData = new FormData();
    formData.append('w_id', w_id);

    $.ajax({
        type: 'POST',
        url: '<?=base_url()?>/api/api_config_withdraw_auto.php',
        data:formData,
        contentType: false,
        processData: false,
    }).done(function(res){
      window.alert(res ? res.message : 'ระบบทำงานผิดพลาด กรุณาติดต่อแอดมิน');
      window.location.href='./withdraw_auto';
      console.clear();
      $('#loading').hide();
    }).fail(function(jqXHR){
        res = jqXHR.responseJSON;
        window.alert(res ? res.message : 'ระบบทำงานผิดพลาด กรุณาติดต่อแอดมิน');
        window.location.href='./withdraw_auto';
        console.clear();
        $('#loading').hide();
    });
}

</script>
</html>