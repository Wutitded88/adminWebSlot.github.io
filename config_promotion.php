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
        <h1 style="font-size: 30px;">จัดการโปรโมชั่น <small>(Admin only)</small></h1>
      </section>
      <section class="content mb-3">
        <div class="row">
          <div class="col-sm-12">
            <button type="button" onclick="open_modal_add()" class="btn btn-success btn-block">
              <i class="fas fa-plus"></i> เพิ่มโปรโมชั่น
            </button>
          </div>
        </div>
      </section>
      <section class="content">
        <div class="row">
          <?php
          $q_1 = dd_q('SELECT * FROM promotion_tb');
          while($row = $q_1->fetch(PDO::FETCH_ASSOC))
          {
          ?>
          <div class="col-sm-4">
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="row">
                  <div class="col-sm-12 mb-3">
                    <span style="font-size: 16px;"><strong><?=$row['p_title']?></strong></span>
                  </div>
                  <div class="col-sm-12">
                    <img style="width: 100%;" src="<?=$row['p_img']?>">
                  </div>
                  <div class="col-sm-12">
                    <span><strong>รายละเอียด : </strong><?=$row['p_detail']?></span>
                  </div>
                  <?php
                  if($row['p_transfer_type'] != "5")
                  {
                  ?>
                  <div class="col-sm-12">
                    <span><strong>การคำนวนโบนัส : </strong><?php if($row['p_type'] == "p"){echo "เปอร์เซ็น";}elseif($row['p_type'] == "c"){echo "เครดิต";}?></span>
                  </div>
                  <div class="col-sm-12">
                    <span><strong>จำนวนรางวัล : </strong><?php if($row['p_type'] == "p"){echo $row['p_reward']."%";}elseif($row['p_type'] == "c"){echo $row['p_reward']." เครดิต";}?></span>
                  </div>
                  <div class="col-sm-12">
                    <span><strong>รับโบนัสสูงสุด : </strong><?php if($row['p_reward_max'] == 0){echo "ไม่จำกัด";}else{echo $row['p_reward_max']." เครดิต";}?></span>
                  </div>
                  <div class="col-sm-12">
                    <span><strong>ย้ายเงินขั้นต่ำ : </strong><?php if($row['p_transfer_type'] == "4"){echo "ไม่ต้องย้ายเงิน";}else{echo $row['p_transfer_min']." เครดิต";}?></span>
                  </div>
                  <?php
                  }
                  else
                  {
                  ?>
                  <div class="col-sm-12">
                    <span><strong>การคำนวนโบนัส : </strong>เป็นไปตามที่กำหนดในตั้งค่าเว็บไซต์</span>
                  </div>
                  <div class="col-sm-12">
                    <span><strong>จำนวนรางวัล : </strong>ได้เต็มตามจำนวนแนะนำเพื่อนที่ทำได้</span>
                  </div>
                  <div class="col-sm-12">
                    <span><strong>รับโบนัสสูงสุด : </strong>เป็นไปตามที่กำหนดในตั้งค่าเว็บไซต์</span>
                  </div>
                  <div class="col-sm-12">
                    <span><strong>ย้ายเงินขั้นต่ำ : </strong>-</span>
                  </div>
                  <?php
                  }
                  ?>
                  <div class="col-sm-12">
                    <span><strong>ประเภทโบนัส : </strong><?php if($row['p_transfer_type'] == "1"){echo "ย้ายเงินครั้งแรก";}elseif($row['p_transfer_type'] == "2"){echo "ย้ายเงินครั้งแรกของวัน";}elseif($row['p_transfer_type'] == "3"){echo "รับได้ตลอด";}elseif($row['p_transfer_type'] == "4"){echo "ฟรีเครดิต";}elseif($row['p_transfer_type'] == "5"){echo "แนะนำเพื่อน";}?></span>
                  </div>
                  <div class="col-sm-12">
                    <span><strong>ยอดเทิร์น : </strong>มากกว่า <?=$row['p_turnover']?> <?php if($row['p_turnover_type'] == "c"){echo "เท่า";}elseif($row['p_turnover_type'] == "p"){echo "%";}elseif($row['p_turnover_type'] == "w"){echo "เครดิต winloss";}?></span>
                  </div>
                  <div class="col-sm-12">
                    <span><strong>ถอนได้สูงสุด : </strong><?php if($row['p_withdraw_max'] == 0.00){echo "ไม่จำกัด";}else{echo $row['p_withdraw_max']." บาท";}?></span>
                  </div>
                  <div class="col-sm-12 mb-3">
                    <span><strong>แสดงในตัวเลือก : </strong><?php if($row['p_status'] == "0"){echo "ไม่แสดง";}else{echo "แสดง";}?></span>
                  </div>
                  <div class="col-sm-12 mb-1">
                    <button type="button" onclick="open_modal_edit('<?=$row['p_id']?>','<?=$row['p_title']?>','<?=$row['p_detail']?>','<?=$row['p_type']?>','<?=$row['p_reward']?>','<?=$row['p_reward_max']?>','<?=$row['p_transfer_min']?>','<?=$row['p_transfer_type']?>','<?=$row['p_turnover']?>','<?=$row['p_turnover_type']?>','<?=$row['p_withdraw_max']?>','<?=$row['p_status']?>','<?=$row['p_img']?>')" class="btn btn-success btn-block">
                      <i class="fas fa-edit"></i> แก้ไขโปรโมชั่น
                    </button>
                  </div>
                  <?php
                  if($row['p_transfer_type'] != "4" && $row['p_transfer_type'] != "5")
                  {
                  ?>
                  <div class="col-sm-12">
                    <button type="button" onclick="ondelete('<?=$row['p_id']?>')" class="btn btn-danger btn-block">
                      <i class="fas fa-trash-alt"></i> ลบโปรโมชั่น
                    </button>
                  </div>
                  <?php
                  }
                  ?>
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
          <h5 class="modal-title" id="modal_dataLabel">ข้อมูลโปรโมชั่น</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-sm-12 mb-2">
              <strong>รูปโปรโมชั่น <small>ขนาดแนะนำ 680*210 px</small> </strong> <p>ฝากไฟล์ภาพได้ที่เว็บไซต์ <a href="https://imgur.com/" target="_blank">imgur.com</a> <b class="text-danger">(**ต้องใช้ URL ที่ขึ้นต้นด้วย i.imgur เท่านั้น**)</b></p>
              <input type="text" class="form-control" maxlength="255" id="p_img" placeholder="https://i.imgur.com/.....">
            </div>
            <div class="col-sm-12 mb-2">
              <strong>ชื่อโปรโมชั่น</strong>
              <input type="text" class="form-control" maxlength="255" id="p_title">
            </div>
            <div class="col-sm-12 mb-2">
              <strong>รายละเอียด</strong>
              <input type="text" class="form-control" maxlength="1000" id="p_detail">
            </div>
            <div class="col-sm-12 mb-2" id="div_type">
              <strong>การคำนวนโบนัส</strong>
              <select class="form-control" id="p_type">
                <option value="">--- กรุณาเลือก ---</option>
                <option value="p">เปอร์เซ็น</option>
                <option value="c">เครดิต</option>
              </select>
            </div>
            <div class="col-sm-12 mb-2" id="div_reward">
              <strong>จำนวนรางวัล</strong>
              <input type="number" min="0" step="1" class="form-control" maxlength="10" id="p_reward">
            </div>
            <div class="col-sm-12 mb-2" id="div_reward_max">
              <strong>รับโบนัสสูงสุด <span class="text-danger">**ใช้กับโบนัสประเภท เปอร์เซ็น ถ้าเป็นประเภทเครดิต ให้ใส่เท่าจำนวนรางวัล</span></strong>
              <input type="number" min="0" step="1" class="form-control" maxlength="10" id="p_reward_max">
            </div>
            <div class="col-sm-12 mb-2" id="div_transfer_min">
              <strong>ย้ายเงินขั้นต่ำ <span class="text-danger">**ขั้นต่ำ 1 บาท</span></strong>
              <input type="number" min="1" step="1" class="form-control" maxlength="10" id="p_transfer_min">
            </div>
            <div class="col-sm-12 mb-2">
              <strong>ประเภทโบนัส</strong>
              <select class="form-control" id="p_transfer_type">
                <option value="">--- กรุณาเลือก ---</option>
                <option value="1">โปรย้ายเงินครั้งแรก (รับได้ครั้งเดียวเท่านั้น)</option>
                <option value="2">โปรครั้งแรกของวัน (รับได้ 1 ครั้งต่อวัน)</option>
                <option value="3">โปรปกติ (รับได้ตลอด)</option>
                <option value="4">ฟรีเครดิต</option>
                <option value="5">แนะนำเพื่อน</option>
              </select>
            </div>
            <div class="col-sm-12 mb-2">
              <strong>การคำนวนยอดเทิร์น</strong>
              <select class="form-control" id="p_turnover_type">
                <option value="">--- กรุณาเลือก ---</option>
                <option value="c">เท่า</option>
                <option value="p">เปอร์เซ็น</option>
                <option value="w">winloss</option>
              </select>
            </div>
            <div class="col-sm-12 mb-2">
              <strong>ยอดเทิร์น</strong>
              <input type="number" min="1" step="1" class="form-control" maxlength="10" id="p_turnover">
            </div>
            <div class="col-sm-12 mb-2">
              <a href="<?=base_url()?>/config_game_turnover" target="_blank">ตั้งค่าเกมที่นำมาคิดยอดเทิร์น <span class="text-danger">**มีผลกับการคิดยอดเทิร์นแบบ winloss เท่านั้น</span></a>
            </div>
            <div class="col-sm-12 mb-2">
              <strong>ถอนได้สูงสุด <span class="text-danger">**ถอนได้ไม่จำกัด ใส่ 0</span></strong>
              <input type="number" min="0" step="1" class="form-control" maxlength="10" id="p_withdraw_max">
            </div>
            <div class="col-sm-12 mb-2">
              <strong>แสดงให้ลูกค้าเลือก</strong>
              <select class="form-control" id="p_status">
                <option value="">--- กรุณาเลือก ---</option>
                <option value="1">แสดงให้ลูกค้าเห็น</option>
                <option value="0">ไม่แสดงให้ลูกค้าเห็น</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
          <button type="button" onclick="onsave()" class="btn btn-primary">บันทึก</button>
          <input type="hidden" id="hdf_type">
          <input type="hidden" class="form-control" id="hdf_p_id">
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
    $("#p_img").val('');
    $("#p_title").val('');
    $("#p_detail").val('');
    $("#p_type").val('');
    $("#p_reward").val('');
    $("#p_reward_max").val('');
    $("#p_transfer_min").val('');
    $("#p_transfer_type").val('');
    $("#p_turnover").val('');
    $("#p_turnover_type").val('');
    $("#p_withdraw_max").val('');
    $("#p_status").val('');

    $("#p_transfer_type").prop( "disabled", false);
    $("#p_status").prop( "disabled", false);

    $("#hdf_p_id").val('');
    $("#hdf_type").val('add');
    $('#modal_data').modal('show');
  }
  function open_modal_edit(p_id,p_title,p_detail,p_type,p_reward,p_reward_max,p_transfer_min,p_transfer_type,p_turnover,p_turnover_type,p_withdraw_max,p_status,p_img)
  {
    $("#p_img").val(p_img);
    $("#p_title").val(p_title);
    $("#p_detail").val(p_detail);
    $("#p_type").val(p_type);
    $("#p_reward").val(p_reward);
    $("#p_reward_max").val(p_reward_max);
    $("#p_transfer_min").val(p_transfer_min);
    $("#p_transfer_type").val(p_transfer_type);
    $("#p_turnover").val(p_turnover);
    $("#p_turnover_type").val(p_turnover_type);
    $("#p_withdraw_max").val(p_withdraw_max);
    $("#p_status").val(p_status);
    if(p_transfer_type == '4' || p_transfer_type == '5')
    {
      $("#p_transfer_type").prop( "disabled", true);
      $("#p_status").prop( "disabled", true);

      if(p_transfer_type == '5')
      {
        $("#div_type").css("display", "none");
        $("#div_reward").css("display", "none");
        $("#div_reward_max").css("display", "none");
        $("#div_transfer_min").css("display", "none");
      }
      else
      {
        $("#div_type").css("display", "block");
        $("#div_reward").css("display", "block");
        $("#div_reward_max").css("display", "block");
        $("#div_transfer_min").css("display", "block");
      }
    }
    else
    {
      $("#p_transfer_type").prop( "disabled", false);
      $("#p_status").prop( "disabled", false);

      $("#div_type").css("display", "block");
      $("#div_reward").css("display", "block");
      $("#div_reward_max").css("display", "block");
      $("#div_transfer_min").css("display", "block");
    }

    $("#hdf_p_id").val(p_id);
    $("#hdf_type").val('edit');
    $('#modal_data').modal('show');
  }

  function onsave()
  {
    var formData = new FormData();
    formData.append('p_img', $("#p_img").val());
    formData.append('p_title', $("#p_title").val());
    formData.append('p_detail', $("#p_detail").val());
    formData.append('p_type', $("#p_type").val());
    formData.append('p_reward', $("#p_reward").val());
    formData.append('p_reward_max', $("#p_reward_max").val());
    formData.append('p_transfer_min', $("#p_transfer_min").val());
    formData.append('p_transfer_type', $("#p_transfer_type").val());
    formData.append('p_turnover', $("#p_turnover").val());
    formData.append('p_turnover_type', $("#p_turnover_type").val());
    formData.append('p_withdraw_max', $("#p_withdraw_max").val());
    formData.append('p_status', $("#p_status").val());
    formData.append('hdf_p_id', $("#hdf_p_id").val());

    formData.append('type', $("#hdf_type").val());
    formData.append('username', $("#username").val());
    formData.append('ip', $("#ip").val());

      $.ajax({
          type: 'POST',
          url: 'system/api_config_promotion',
          data:formData,
          contentType: false,
          processData: false,
      }).done(function(res){
          result = res;
          alert(result.message);
          window.location = './config_promotion';
          console.clear();
      }).fail(function(jqXHR){
          res = jqXHR.responseJSON;
          alert(res.message);
          console.clear();
      });
  }
  function ondelete(p_id)
  {
    var result = confirm("คุณต้องการลบข้อมูลนี้ ?");
    if (result) {
      var formData = new FormData();
      formData.append('p_id', p_id);
      formData.append('username', $("#username").val());
      formData.append('ip', $("#ip").val());
      formData.append('type', 'delete');

      $.ajax({
          type: 'POST',
          url: 'system/api_config_promotion',
          data:formData,
          contentType: false,
          processData: false,
      }).done(function(res){
          result = res;
          alert(result.message);
          window.location = './config_promotion';
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