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
        <h1 style="font-size: 30px;">ตั้งค่ารางวัลเช็คอิน <small>(Admin only)</small></h1>
      </section>
      <section class="content">
        <div class="row">
          <?php
          $q_1 = dd_q('SELECT * FROM checkin_tb');
          while($row = $q_1->fetch(PDO::FETCH_ASSOC))
          {
          ?>
          <div class="col-sm-2">
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="row">
                  <div class="col-sm-12 mb-1">
                    <span style="font-size: 16px;"><strong>รางวัลเช็คอินวันที่ <?=$row['c_day']?></strong></span>
                  </div>
                  <?php
                  if($row['c_status'] == "1" && $row['c_reward'] > 0)
                  {
                  ?>
                  <div class="col-sm-12">
                    <span><strong>จำนวนรางวัล : </strong><?=$row['c_reward']?> เครดิต</span>
                  </div>
                  <div class="col-sm-12">
                    <span><strong>ยอดเทิร์นกดรับ : </strong><?=$row['c_turnover1']?> เท่า</span>
                  </div>
                  <div class="col-sm-12">
                    <span><strong>ยอดเทิร์น : </strong><?=$row['c_turnover']?> เท่า</span>
                  </div>
                  <div class="col-sm-12">
                    <span><strong>ถอนได้สูงสุด : </strong><?php if($row['c_withdraw_max'] == 0.00){echo "ไม่จำกัด";}else{echo $row['c_withdraw_max']." บาท";}?></span>
                  </div>
                  <div class="col-sm-12">
                    <span><strong>เงื่อนไขรับรางวัล : </strong>เติมครบ <?=$row['c_target_topup']?> บาท</span>
                  </div>
                  <div class="col-sm-12 mb-3">
                    <span><strong>สถานะ : </strong><span class="text-success text-bold">เปิดใช้งาน</span></span>
                  </div>
                  <?php
                  }
                  else
                  {
                  ?>
                  <div class="col-sm-12 mb-3">
                    <span><strong>สถานะ : </strong><span class="text-danger text-bold">ไม่แจกรางวัล</span></span>
                  </div>
                  <?php
                  }
                  ?>
                  <div class="col-sm-12">
                    <button type="button" onclick="open_modal_edit('<?=$row['c_id']?>', '<?=$row['c_reward']?>', '<?=$row['c_turnover']?>', '<?=$row['c_withdraw_max']?>', '<?=$row['c_target_topup']?>', '<?=$row['c_status']?>', '<?=$row['c_day']?>', '<?=$row['c_turnover1']?>')" class="btn btn-success btn-block">
                      <i class="fas fa-edit"></i> แก้ไขรางวัล
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
          <h5 class="modal-title" id="modal_dataLabel">ข้อมูลรางวัลเช็คอินวันที่ <span id="sp_c_day"></span></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-sm-12 mb-2">
              <strong>จำนวนรางวัล <span class="text-danger">**ไม่แจกรางวัล ใส่ 0 หรือเปลี่ยนสถานะเป็นไม่แจกรางวัล</span></strong>
              <input type="number" min="0" step="1" class="form-control" maxlength="10" id="c_reward">
            </div>
            <div class="col-sm-12 mb-2">
              <strong>ยอดเทิร์นกดรับ (เท่า) <span class="text-danger">** ยอดเทิร์นกดรับจะคูณจากยอด เติมเงินเพื่อรับรางวัล เช่น ต้องเติม 100 ถึงรับได้ ยอดเทิร์นกดรับ 2 ก็ต้องมียอดเล่น 200 ขึ้นไปถึงจะรับได้</span></strong>
              <input type="number" min="1" step="1" class="form-control" maxlength="10" id="c_turnover1">
            </div>
            <div class="col-sm-12 mb-2">
              <strong>ยอดเทิร์น (เท่า)</strong>
              <input type="number" min="1" step="1" class="form-control" maxlength="10" id="c_turnover">
            </div>
            <div class="col-sm-12 mb-2">
              <strong>ถอนได้สูงสุด <span class="text-danger">**ถอนได้ไม่จำกัด ใส่ 0</span></strong>
              <input type="number" min="0" step="1" class="form-control" maxlength="10" id="c_withdraw_max">
            </div>
            <div class="col-sm-12 mb-2">
              <strong>ยอดเติมเงินเพื่อรับรางวัล </strong>
              <input type="number" min="0" step="1" class="form-control" maxlength="10" id="c_target_topup">
            </div>
            <div class="col-sm-12 mb-2">
              <strong>สถานะ</strong>
              <select class="form-control" id="c_status">
                <option value="">--- กรุณาเลือก ---</option>
                <option value="1">เปิดใช้งาน</option>
                <option value="0">ไม่แจกรางวัล</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
          <button type="button" onclick="onsave()" class="btn btn-primary">บันทึก</button>
          <input type="hidden" class="form-control" id="c_id">
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
  function open_modal_edit(c_id, c_reward, c_turnover, c_withdraw_max, c_target_topup, c_status, c_day, c_turnover1)
  {
    $("#sp_c_day").html(c_day);
    $("#c_reward").val(c_reward);
    $("#c_turnover").val(c_turnover);
    $("#c_turnover1").val(c_turnover1);
    $("#c_withdraw_max").val(c_withdraw_max);
    $("#c_target_topup").val(c_target_topup);
    $("#c_status").val(c_status);

    $("#c_id").val(c_id);
    $('#modal_data').modal('show');
  }

  function onsave()
  {
    var formData = new FormData();
    formData.append('c_reward', $("#c_reward").val());
    formData.append('c_turnover', $("#c_turnover").val());
    formData.append('c_turnover1', $("#c_turnover1").val());
    formData.append('c_withdraw_max', $("#c_withdraw_max").val());
    formData.append('c_target_topup', $("#c_target_topup").val());
    formData.append('c_status', $("#c_status").val());
    formData.append('c_id', $("#c_id").val());

    formData.append('username', $("#username").val());
    formData.append('ip', $("#ip").val());

      $.ajax({
          type: 'POST',
          url: 'system/api_config_checkin',
          data:formData,
          contentType: false,
          processData: false,
      }).done(function(res){
          result = res;
          alert(result.message);
          window.location = './config_checkin';
          console.clear();
      }).fail(function(jqXHR){
          res = jqXHR.responseJSON;
          alert(res.message);
          console.clear();
      });
  }
</script>
</html>