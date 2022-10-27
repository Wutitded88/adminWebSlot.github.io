<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
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
  if(isset($_GET['id']) && $_GET['id'] != "")
  {
    $q_1 = dd_q('SELECT * FROM withdraw_tb WHERE (w_id = ?)', [$_GET['id']]);
    if ($q_1->rowCount() >= 1)
    {
      $row = $q_1->fetch(PDO::FETCH_ASSOC);
    }
    else
    {
      echo "<SCRIPT LANGUAGE='JavaScript'>
            window.location.href = '".base_url()."/dashboard';
          </SCRIPT>";
      exit();
    }
  }
  else
  {
    echo "<SCRIPT LANGUAGE='JavaScript'>
          window.location.href = '".base_url()."/dashboard';
        </SCRIPT>";
    exit();
  }
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
  <input type="hidden" id="w_id" value="<?=$row['w_id']?>">
  <div class="wrapper">
    <?php include ("partials/_navbar.php"); ?>
    <?php include ("partials/_sidebar.php"); ?>
    <div class="content-wrapper">
      <section class="content-header pt-4 pb-4">
        <h1 style="font-size: 30px;">รายละเอียดการถอนเงิน 
          <span style="font-size: 24px;" class="text-success">
            (รหัสรายการ <?=$row['w_id']?>)
          </span>
        </h1>
      </section>
      <section class="content">
        <div class="row">
          <div class="col-sm-5">
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="text-center mb-3">
                  <img class="img-fluid w-25" src="<?=base_url()?>/images/logov1.png">
                </div>
                <h3 class="text-center">
                  <a href="<?=base_url()?>/customer/detail/<?=$row['w_u_id']?>" target="_blank">
                    <?=$row['w_fname']?>
                  </a>
                </h3>
                <p class="text-center">
                  <code class="text-muted" style="font-size: 14px;">
                    <?php echo $_CONFIG['prefixfortest']."".$row['w_agent_id']; ?>
                  </code>
                </p>
                <ul class="list-group list-group-unbordered mb-3">
                  <li class="list-group-item">
                     <b>สร้างเมื่อ</b> <a class="float-right text-danger"><?=date('d/m/Y', strtotime($row['w_date_create']))?> <?=$row['w_time_create']?></a>
                  </li>
                  <li class="list-group-item">
                    <b>ธนาคาร *ลูกค้า</b> <a class="float-right"><?=$row['w_bank_name']?></a>
                  </li>
                  <li class="list-group-item">
                    <b>เลขที่บัญชี *ลูกค้า</b> <a class="float-right text-bold text-primary"><?=$row['w_bank_number']?></a>
                  </li>
                  <li class="list-group-item">
                    <b>จำนวนต้องการถอน</b> <a class="float-right text-success"><?=$row['w_amount']?> ฿</a>
                  </li>
                  <li class="list-group-item">
                    <b>สร้างโดย</b> 
                      <a class="float-right text-bold">
                        <span class="text-primary">
                          <?php
                          if($row['w_type'] == "1")
                          {
                            echo "ระบบ";
                          }
                          elseif($row['w_type'] == "2")
                          {
                            echo "พนักงาน";
                          }
                          ?>
                        </span>
                      </a>
                  </li>
                  <li class="list-group-item">
                    <b>พนักงาน</b> <a class="float-right text-bold"><?=$row['w_action_by']?></a>
                  </li>
                  <li class="list-group-item">
                    <b>สถานะ</b> 
                    <a class="float-right text-bold">
                      <?php
                      if($row['w_status'] == "0")
                      {
                      ?>
                        <span class="text-warning">รอโอนเงิน</span>
                      <?php
                      }
                      elseif($row['w_status'] == "1")
                      {
                      ?>
                        <span class="text-success">โอนเงินสำเร็จ</span>
                      <?php
                      }
                      elseif($row['w_status'] == "2")
                      {
                      ?>
                        <span class="text-danger">ยกเลิกรายการ</span>
                      <?php
                      }
                      elseif($row['w_status'] == "3")
                      {
                      ?>
                        <span class="text-primary">คืนเครดิต</span>
                      <?php
                      }
                      elseif($row['w_status'] == "99")
                      {
                      ?>
                        <span class="text-warning">รอแอดมินตรวจสอบ</span>
                      <?php
                      }
                      ?>
                    </a>
                  </li>
                </ul>
                <div class="button_confirm">
                  <?php
                  if($row['w_status'] == "0" || $row['w_status'] == "99")
                  {
                    $q_scb_w = dd_q('SELECT * FROM autobank_tb WHERE a_bank_code=?', ['scb']);
                    $row_scb_w = $q_scb_w->fetch(PDO::FETCH_ASSOC);
                    if($row_scb_w['a_bank_status'] == "1")
                    {
                      $bank_balance = 0.00;
                      require_once 'api/api_scb_withdraw.php';
                      $api = new SCBAPI();
                      $data_balance = $api->balance("");
                      if ($data_balance->success == true)
                      {
                        $bank_balance = number_format($data_balance->balance, 2, '.', ',');
                      } else
                      {
                        echo json_encode($data_balance);
                      }
                    ?>
                    <li class="list-group-item">
                      <div class="text-center">
                        <h5 class="text-success">ยอดเงินคงเหลือในบัญชีที่สามารถโอนได้ <b><?=$bank_balance?> บาท</b></h5>
                      </div>
                      <input type="text" class="form-control mb-3 mt-3" id="txt_wcode" placeholder="** กรอกรหัสสำหรับโอนเงินแบบ Auto **" maxlength="50">
                      <button type="button" id="btn_approve_auto" class="btn-cut btn btn-success btn-block btn-lg mb-3 mt-3">
                        <b>อนมุติการถอนเงินและโอนเงิน (Auto)</b>
                      </button>
                    </li>
                    <hr>
                    <?php
                    }
                  ?>
                  <li class="list-group-item">
                    <button type="button" id="btn_approve" class="btn-cut btn btn-success btn-block">
                      <b>อนมุติการถอนเงิน (Manual)</b>
                    </button>
                    <button type="button" id="btn_refund" class="btn btn-warning btn-block mt-2">
                      <b>ยกเลิกรายการถอนและคืนเครดิต (Refund)</b>
                    </button>
                    <button type="button" id="btn_cancel" class="btn btn-danger btn-block mt-2">
                      <b>ยกเลิกรายการถอน (Cancel)</b>
                    </button>
                  </li>
                  <?php
                  }
                  ?>
                </div>
              </div>
            </div>
          </div>

          <div class="col-sm-7">
            <div class="card card-outline">
              <div class="card-body box-profile p-3">
                <h3>
                  รายการฝากเงิน
                  <?php
                  $sum_topup = 0.00;
                  $q_1_1 = dd_q('SELECT SUM(t_amount) AS sum_amount FROM topup_db WHERE t_user = ?', [$row['w_user']]);
                  $row_1_1 = $q_1_1->fetch(PDO::FETCH_ASSOC);
                  $sum_topup = $row_1_1['sum_amount'];
                  ?>
                  <small class="text-success">(<?=number_format($sum_topup, 2)?>)</small>
                </h3>
                <hr class="border-success">
                <div class="table-responsive">
                  <table class="table" id="tb_d" style="width: 100%;">
                    <thead class="text-center">
                      <tr>
                        <th scope="col">วันสร้าง</th>
                        <th scope="col">เวลาสร้าง</th>
                        <th scope="col">จำนวนเงินฝาก</th>
                        <th scope="col">เงินก่อนทำรายการ</th>
                        <th scope="col">เงินหลังทำรายการ</th>
                        <th scope="col">สถานะ</th>
                        <th scope="col">สร้างโดย</th>
                      </tr>
                    </thead>
                  </table>
                </div>
              </div>

              <div class="card-body box-profile p-3">
                <h3>
                  รายการย้ายเงิน
                  <?php
                  $sum_amount = 0.00;
                  $q_2_1 = dd_q('SELECT SUM(t_amount) AS sum_amount FROM transfergame_tb WHERE t_user = ?', [$row['w_user']]);
                  $row_2_1 = $q_2_1->fetch(PDO::FETCH_ASSOC);
                  $sum_amount = $row_2_1['sum_amount'];
                  ?>
                  <small class="text-primary">(<?=number_format($sum_amount, 2)?>)</small>
                </h3>
                <hr class="border-primary">
                <div class="table-responsive">
                  <table class="table" id="tb_t" style="width: 100%;">
                    <thead class="text-center">
                      <tr>
                        <th scope="col">วันสร้าง</th>
                        <th scope="col">เวลาสร้าง</th>
                        <th scope="col">จำนวนเงิน</th>
                        <th scope="col">โบนัสที่รับ</th>
                        <th scope="col">จำนวนโบนัส</th>
                        <th scope="col">เครดิตที่ได้</th>
                        <th scope="col">ยอดเทิร์น</th>
                      </tr>
                    </thead>
                  </table>
                </div>
              </div>

              <div class="card-body box-profile p-3">
                <h3>
                  รายการถอนเงิน
                  <?php
                  $sum_withdraw = 0.00;
                  $q_3_1 = dd_q('SELECT SUM(w_amount) AS sum_amount FROM withdraw_tb WHERE w_status=? AND w_user=?', [
                    '1',
                    $row['w_user']
                  ]);
                  $row_3_1 = $q_3_1->fetch(PDO::FETCH_ASSOC);
                  $sum_withdraw = $row_3_1['sum_amount'];
                  ?>
                  <small class="text-danger">(<?=number_format($sum_withdraw, 2)?>)</small>
                </h3>
                <hr class="border-danger">
                <div class="table-responsive">
                  <table class="table" id="tb_w" style="width: 100%;">
                    <thead class="text-center">
                      <tr>
                        <th scope="col">วันสร้าง</th>
                        <th scope="col">เวลาสร้าง</th>
                        <th scope="col">ถอนเงิน</th>
                        <th scope="col">คืนเครดิต</th>
                        <th scope="col">สถานะ</th>
                        <th scope="col">สร้างโดย</th>
                        <th scope="col">พนักงาน</th>
                      </tr>
                    </thead>
                  </table>
                </div>
              </div>

            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</body>
<?php include('partials/_footer.php'); ?>
<script type="text/javascript">

$(function () {
  $('#tb_d').DataTable({
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
      "ajax": "<?=base_url()?>/server-side/server_user_deposit.php?userid=<?=$row['w_user']?>"
  });
  
  $('#tb_t').DataTable({
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
      "ajax": "<?=base_url()?>/server-side/server_user_transfer.php?userid=<?=$row['w_user']?>"
  });
    
  $('#tb_w').DataTable({
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
      "ajax": "<?=base_url()?>/server-side/server_user_withdraw.php?userid=<?=$row['w_user']?>"
  });
});

  $("#btn_approve_auto").click(function(e) {
    e.preventDefault();
    var result = confirm("ยืนยันการดำเนินการต่อ ?");
    if (result)
    {
      var formData = new FormData();
      formData.append('w_id', $("#w_id").val());
      formData.append('wcode', $("#txt_wcode").val());

      formData.append('type', "approve_auto");
      formData.append('username', $("#username").val());
      formData.append('ip', $("#ip").val());

      $('#loading').show();

      $.ajax({
          type: 'POST',
          url: '<?=base_url()?>/system/api_withdraw',
          data:formData,
          contentType: false,
          processData: false,
      }).done(function(res){
          result = res;
          alert(result.message);
          window.location = '<?=base_url()?>/withdraw/detail/<?=$_GET['id']?>';
          console.clear();
          $('#loading').hide();
      }).fail(function(jqXHR){
          res = jqXHR.responseJSON;
          alert(res.message);
          console.clear();
          $('#loading').hide();
      });
    }
  });

  $("#btn_approve").click(function(e) {
    e.preventDefault();
    var result = confirm("ยืนยันการดำเนินการต่อ ?");
    if (result)
    {
      var formData = new FormData();
      formData.append('w_id', $("#w_id").val());

      formData.append('type', "approve");
      formData.append('username', $("#username").val());
      formData.append('ip', $("#ip").val());

      $('#loading').show();

      $.ajax({
          type: 'POST',
          url: '<?=base_url()?>/system/api_withdraw',
          data:formData,
          contentType: false,
          processData: false,
      }).done(function(res){
          result = res;
          alert(result.message);
          window.location = '<?=base_url()?>/withdraw/detail/<?=$_GET['id']?>';
          console.clear();
          $('#loading').hide();
      }).fail(function(jqXHR){
          res = jqXHR.responseJSON;
          alert(res.message);
          console.clear();
          $('#loading').hide();
      });
    }
  });

  $("#btn_cancel").click(function(e) {
    e.preventDefault();
    var result = confirm("ยืนยันการดำเนินการต่อ ?");
    if (result)
    {
      var formData = new FormData();
      formData.append('w_id', $("#w_id").val());

      formData.append('type', "cancel");
      formData.append('username', $("#username").val());
      formData.append('ip', $("#ip").val());

      $('#loading').show();

      $.ajax({
          type: 'POST',
          url: '<?=base_url()?>/system/api_withdraw',
          data:formData,
          contentType: false,
          processData: false,
      }).done(function(res){
          result = res;
          alert(result.message);
          window.location = '<?=base_url()?>/withdraw/detail/<?=$_GET['id']?>';
          console.clear();
          $('#loading').hide();
      }).fail(function(jqXHR){
          res = jqXHR.responseJSON;
          alert(res.message);
          console.clear();
          $('#loading').hide();
      });
    }
  });

  $("#btn_refund").click(function(e) {
    e.preventDefault();
    var result = confirm("ยืนยันการดำเนินการต่อ ?");
    if (result)
    {
      var formData = new FormData();
      formData.append('w_id', $("#w_id").val());

      formData.append('type', "refund");
      formData.append('username', $("#username").val());
      formData.append('ip', $("#ip").val());

      $('#loading').show();

      $.ajax({
          type: 'POST',
          url: '<?=base_url()?>/system/api_withdraw',
          data:formData,
          contentType: false,
          processData: false,
      }).done(function(res){
          result = res;
          alert(result.message);
          window.location = '<?=base_url()?>/withdraw/detail/<?=$_GET['id']?>';
          console.clear();
          $('#loading').hide();
      }).fail(function(jqXHR){
          res = jqXHR.responseJSON;
          alert(res.message);
          console.clear();
          $('#loading').hide();
      });
    }
  });
</script>
</html>