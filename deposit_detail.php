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
  if(isset($_GET['id']) && $_GET['id'] != "")
  {
    $q_1 = dd_q('SELECT * FROM topup_db WHERE (t_id = ?)', [$_GET['id']]);
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
  <input type="hidden" id="t_id" value="<?=$row['t_id']?>">
  <div class="wrapper">
    <?php include ("partials/_navbar.php"); ?>
    <?php include ("partials/_sidebar.php"); ?>
    <div class="content-wrapper">
      <section class="content-header pt-4 pb-4">
        <h1 style="font-size: 30px;">รายละเอียดการฝากเงิน 
          <span style="font-size: 24px;" class="text-success">
            (รหัสรายการ <?=$row['t_id']?>)
          </span>
        </h1>
      </section>
      <section class="content">
        <div class="row justify-content-center">
          <div class="col-sm-6">
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="text-center mb-3">
                  <img class="img-fluid w-25" src="<?=base_url()?>/images/logov1.png">
                </div>
                <h3 class="text-center">
                  <a href="<?=base_url()?>/customer/detail/<?=$row['t_u_id']?>" target="_blank">
                    <?=$row['t_fname']?>
                  </a>
                </h3>
                <p class="text-center">
                  <code class="text-muted" style="font-size: 14px;">
                    <?php echo $_CONFIG['prefixfortest']."".$row['t_agent_id']; ?>
                  </code>
                </p>
                <ul class="list-group list-group-unbordered mb-3">
                  <li class="list-group-item">
                     <b>สร้างเมื่อ</b> <a class="float-right text-danger"><?=date('d/m/Y', strtotime($row['t_date_create']))?> <?=$row['t_time_create']?></a>
                  </li>
                  <li class="list-group-item">
                    <b>ฝากเข้า</b> <a class="float-right"><?=$row['t_sys_bank_name']?> : <?=$row['t_sys_bank_number']?></a>
                  </li>
                  <li class="list-group-item">
                    <b>รายละเอียดรายการฝาก</b> <a class="float-right"><?=$row['t_tx_id']?></a>
                  </li>
                  <li class="list-group-item">
                    <b>ชื่อผู้ฝาก</b> <a class="float-right"><?=$row['t_chanel']?></a>
                  </li>
                  <li class="list-group-item">
                    <b>ธนาคารที่ฝากเข้ามา</b> <a class="float-right"><?=$row['t_topup_bank']?></a>
                  </li>
                  <li class="list-group-item">
                    <b>ธนาคาร *ลูกค้า</b> <a class="float-right"><?=$row['t_bank_name']?></a>
                  </li>
                  <li class="list-group-item">
                    <b>เลขที่บัญชี *ลูกค้า</b> <a class="float-right text-bold text-primary"><?=$row['t_bank_number']?></a>
                  </li>
                  <li class="list-group-item">
                    <b>จำนวนที่ฝาก</b> <a class="float-right text-success"><?=$row['t_amount']?> ฿</a>
                  </li>
                  <li class="list-group-item">
                    <b>สร้างโดย</b> 
                      <a class="float-right text-bold">
                        <span class="text-primary">
                          <?php
                          if($row['t_type'] == "1")
                          {
                            echo "ระบบ";
                          }
                          elseif($row['t_type'] == "2")
                          {
                            echo "พนักงาน";
                          }
                          ?>
                        </span>
                      </a>
                  </li>
                  <li class="list-group-item">
                    <b>พนักงาน</b> <a class="float-right text-bold"><?=$row['t_action_by']?></a>
                  </li>
                  <li class="list-group-item">
                    <b>สถานะ</b> 
                    <a class="float-right text-bold">
                      <?php
                      if($row['t_status'] == "0")
                      {
                      ?>
                        <span class="text-warning">รอทำรายการ</span>
                      <?php
                      }
                      elseif($row['t_status'] == "1")
                      {
                      ?>
                        <span class="text-success">ทำรายการสำเร็จ</span>
                      <?php
                      }
                      elseif($row['t_status'] == "2")
                      {
                      ?>
                        <span class="text-danger">ทำรายการผิดพลาด</span>
                      <?php
                      }
                      elseif($row['t_status'] == "3")
                      {
                      ?>
                        <span class="text-danger">ยกเลิกรายการ</span>
                      <?php
                      }
                      ?>
                    </a>
                  </li>
                </ul>
                <div class="button_confirm">
                  <?php
                  if($row['t_status'] == "2" && $row['t_u_id'] == "")
                  {
                  ?>
                  <button type="button" id="btn_update_openmodal" class="btn-cut btn btn-success btn-block">
                    <b>อัพเดทรายการฝาก</b>
                  </button>
                  <button type="button" id="btn_cancel" class="btn btn-danger btn-block mt-2">
                    <b>พนักงานทำการยกเลิก รายการฝาก</b>
                  </button>
                  <?php
                  }

                  $q_conf = dd_q('SELECT * FROM topup_db WHERE (t_id = ?)', [$_GET['id']]);
                  if ($q_conf->rowCount() >= 1)
                  {
                    $row_conf = $q_conf->fetch(PDO::FETCH_ASSOC);
                    if($row_conf['t_status'] == "2" && $row_conf['t_u_id'] != "")
                    {
                    ?>
                    <button type="button" id="btn_approve_credit" class="btn-cut btn btn-info btn-block">
                      <b>ยืนยันรายการฝาก (ระบบเพิ่มกระเป๋าเงิน)</b>
                    </button>
                    <button type="button" id="btn_approve_no_credit" class="btn-cut btn btn-primary btn-block">
                      <b>ยืนยันรายการฝาก (ไม่เพิ่มกระเป๋าเงิน)</b>
                    </button>
                    <?php
                    }
                  }
                  ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">แก้ไขรายการฝาก</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-sm-12">
              <select class="form-control" id="ddl_t_u_id">
                <option value="">--- กรุณาเลือก ---</option>
                <?php
                if($row['t_type_system'] == "kbank")
                {
                  if($row['t_api_bank'] == "ธ.ออมสิน")
                  {
                    $q_2 = dd_q('SELECT * FROM user_tb WHERE (SUBSTRING(u_bank_number, -7, 4) like ?)', ['%'.$row['t_topup_bank']]);
                  }
                  else if($row['t_api_bank'] == "ธ.ก.ส.")
                  {
                    $q_2 = dd_q('SELECT * FROM user_tb WHERE (SUBSTRING(u_bank_number, -7, 4) like ?)', ['%'.$row['t_topup_bank']]);
                  }
                  else
                  {
                    $q_2 = dd_q('SELECT * FROM user_tb WHERE (SUBSTRING(u_bank_number, -5, 4) like ?)', ['%'.$row['t_topup_bank']]);
                  }
                }
                else
                {
                  $q_2 = dd_q('SELECT * FROM user_tb WHERE (u_bank_number like ?)', ['%'.substr($row['t_topup_bank'], -4)]);
                }
                while($row1 = $q_2->fetch(PDO::FETCH_ASSOC))
                {
                ?>
                <option value="<?=$row1['u_id']?>"><?=$row1['u_user']?> - <?=$row1['u_fname']?> <?=$row1['u_lname']?></option>
                <?php
                }
                ?>
              </select>
            </div>
          </div>
          <div class="row mt-2">
            <div class="col-sm-12">
              <ul class="form-group">
                <div class="font-weight-bold mb-2">ตรวจพบยูสที่ตรงกัน</div>
                <?php
                if($row['t_type_system'] == "kbank")
                {
                  if($row['t_api_bank'] == "ธ.ออมสิน")
                  {
                    $q_3 = dd_q('SELECT * FROM user_tb WHERE (SUBSTRING(u_bank_number, -7, 4) like ?)', ['%'.$row['t_topup_bank']]);
                  }
                  else
                  {
                    $q_3 = dd_q('SELECT * FROM user_tb WHERE (SUBSTRING(u_bank_number, -5, 4) like ?)', ['%'.$row['t_topup_bank']]);
                  }
                }
                else
                {
                  $q_3 = dd_q('SELECT * FROM user_tb WHERE (u_bank_number like ?)', ['%'.substr($row['t_topup_bank'], -4)]);
                }
                while($row1 = $q_3->fetch(PDO::FETCH_ASSOC))
                {
                ?>
                <li><?=$row1['u_user']?> : <?=$row1['u_fname']?> <?=$row1['u_lname']?> - <?=$row1['u_bank_number']?></li>
                <?php
                }
                ?>
              </ul>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <div class="text-right">
            <button type="button" id="btn_update_customer" class="btn btn-primary">
              บันทึก
            </button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
              ปิด
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Modal -->

</body>
<?php include('partials/_footer.php'); ?>
<script type="text/javascript">
  $("#btn_update_openmodal").click(function(e) {
    e.preventDefault();
    $('#ddl_t_u_id').val('');
    $('#exampleModalLong').modal('show');
  });

  $("#btn_update_customer").click(function(e) {
    e.preventDefault();
    var result = confirm("ยืนยันการดำเนินการต่อ ?");
    if (result)
    {
      var formData = new FormData();
      formData.append('t_id', $("#t_id").val());
      formData.append('t_u_id', $("#ddl_t_u_id").val());

      formData.append('type', "update_customer");
      formData.append('username', $("#username").val());
      formData.append('ip', $("#ip").val());

      $('#loading').show();

      $.ajax({
          type: 'POST',
          url: '<?=base_url()?>/system/api_deposit',
          data:formData,
          contentType: false,
          processData: false,
      }).done(function(res){
          result = res;
          alert(result.message);
          window.location = '<?=base_url()?>/deposit/<?=$_GET['id']?>';
          console.clear();
          $('#loading').hide();
      }).fail(function(jqXHR){
          res = jqXHR.responseJSON;
          alert(res.message);
          window.location = '<?=base_url()?>/deposit/<?=$_GET['id']?>';
          console.clear();
          $('#loading').hide();
      });
    }
  });

  $("#btn_approve_credit").click(function(e) {
    e.preventDefault();
    var result = confirm("ยืนยันการดำเนินการต่อ ?");
    if (result)
    {
      var formData = new FormData();
      formData.append('t_id', $("#t_id").val());

      formData.append('type', "approve_credit");
      formData.append('username', $("#username").val());
      formData.append('ip', $("#ip").val());

      $('#loading').show();

      $.ajax({
          type: 'POST',
          url: '<?=base_url()?>/system/api_deposit',
          data:formData,
          contentType: false,
          processData: false,
      }).done(function(res){
          result = res;
          alert(result.message);
          window.location = '<?=base_url()?>/deposit/<?=$_GET['id']?>';
          console.clear();
          $('#loading').hide();
      }).fail(function(jqXHR){
          res = jqXHR.responseJSON;
          alert(res.message);
          window.location = '<?=base_url()?>/deposit/<?=$_GET['id']?>';
          console.clear();
          $('#loading').hide();
      });
    }
  });

  $("#btn_approve_no_credit").click(function(e) {
    e.preventDefault();
    var result = confirm("ยืนยันการดำเนินการต่อ ?");
    if (result)
    {
      var formData = new FormData();
      formData.append('t_id', $("#t_id").val());

      formData.append('type', "approve_no_credit");
      formData.append('username', $("#username").val());
      formData.append('ip', $("#ip").val());

      $('#loading').show();

      $.ajax({
          type: 'POST',
          url: '<?=base_url()?>/system/api_deposit',
          data:formData,
          contentType: false,
          processData: false,
      }).done(function(res){
          result = res;
          alert(result.message);
          window.location = '<?=base_url()?>/deposit/<?=$_GET['id']?>';
          console.clear();
          $('#loading').hide();
      }).fail(function(jqXHR){
          res = jqXHR.responseJSON;
          alert(res.message);
          window.location = '<?=base_url()?>/deposit/<?=$_GET['id']?>';
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
      formData.append('t_id', $("#t_id").val());

      formData.append('type', "cancel");
      formData.append('username', $("#username").val());
      formData.append('ip', $("#ip").val());

      $('#loading').show();

      $.ajax({
          type: 'POST',
          url: '<?=base_url()?>/system/api_deposit',
          data:formData,
          contentType: false,
          processData: false,
      }).done(function(res){
          result = res;
          alert(result.message);
          window.location = '<?=base_url()?>/deposit/<?=$_GET['id']?>';
          console.clear();
          $('#loading').hide();
      }).fail(function(jqXHR){
          res = jqXHR.responseJSON;
          alert(res.message);
          window.location = '<?=base_url()?>/deposit/<?=$_GET['id']?>';
          console.clear();
          $('#loading').hide();
      });
    }
  });
</script>
</html>