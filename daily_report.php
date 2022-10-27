<?php
require_once 'api/config.php';
if(empty(get_session()))
{
  echo "<SCRIPT LANGUAGE='JavaScript'>
          window.location.href = './login';
        </SCRIPT>";
  exit();
}

if(isset($_GET['start_date']) && isset($_GET['end_date']) && $_GET['start_date'] != "" && $_GET['end_date'] != "")
{
  $start_date = $_GET['start_date'];
  $end_date = $_GET['end_date'];
}
elseif(isset($_GET['start_date']) && $_GET['start_date'] != "")
{
  $start_date = $_GET['start_date'];
  $end_date = date('Y-m-d');
}
elseif(isset($_GET['end_date']) && $_GET['end_date'] != "")
{
  $start_date = date('Y-m-d');
  $end_date = $_GET['end_date'];
}
else
{
  $start_date = date('Y-m-d');
  $end_date = date('Y-m-d');
}

$q_1 = dd_q('SELECT COUNT(t_id) AS count_topup_auto, SUM(t_amount) AS sum_topup_auto FROM topup_db WHERE t_date_create >= ? AND t_date_create <= ? AND t_status = ? AND t_type = ?', [$start_date, $end_date, "1", "1"]);
$row_1 = $q_1->fetch(PDO::FETCH_ASSOC);

$q_2 = dd_q('SELECT COUNT(t_id) AS count_topup_manual, SUM(t_amount) AS sum_topup_manual FROM topup_db WHERE t_date_create >= ? AND t_date_create <= ? AND t_status = ? AND t_type = ?', [$start_date, $end_date, "1", "2"]);
$row_2 = $q_2->fetch(PDO::FETCH_ASSOC);

$q_3 = dd_q('SELECT COUNT(w_id) AS count_withdraw_auto, SUM(w_amount) AS sum_withdraw_auto FROM withdraw_tb WHERE w_date_create >= ? AND w_date_create <= ? AND w_status = ? AND w_type = ?', [$start_date, $end_date, "1", "1"]);
$row_3 = $q_3->fetch(PDO::FETCH_ASSOC);

$q_4 = dd_q('SELECT COUNT(w_id) AS count_withdraw_manual, SUM(w_amount) AS sum_withdraw_manual FROM withdraw_tb WHERE w_date_create >= ? AND w_date_create <= ? AND w_status = ? AND w_type = ?', [$start_date, $end_date, "1", "2"]);
$row_4 = $q_4->fetch(PDO::FETCH_ASSOC);

$sum_topup_auto = $row_1['sum_topup_auto'];
$sum_topup_manual = $row_2['sum_topup_manual'];
$sum_topup_all = $sum_topup_auto + $sum_topup_manual;

$count_topup_auto = $row_1['count_topup_auto'];
$count_topup_manual = $row_2['count_topup_manual'];
$count_topup_all = $count_topup_auto + $count_topup_manual;

$sum_withdraw_auto = $row_3['sum_withdraw_auto'];
$sum_withdraw_manual = $row_4['sum_withdraw_manual'];
$sum_withdraw_all = $sum_withdraw_auto + $sum_withdraw_manual;

$count_withdraw_auto = $row_3['count_withdraw_auto'];
$count_withdraw_manual = $row_4['count_withdraw_manual'];
$count_withdraw_all = $count_withdraw_auto + $count_withdraw_manual;

$sum_total_all = $sum_topup_all - $sum_withdraw_all;

$q_5 = dd_q('SELECT COUNT(t_id) AS count_bonus, SUM(t_bonus) AS sum_bonus FROM transfergame_tb WHERE t_date_create >= ? AND t_date_create <= ? AND t_bonus > ?', [$start_date, $end_date, 0]);
$row_5 = $q_5->fetch(PDO::FETCH_ASSOC);

$sum_bonus = $row_5['sum_bonus'];
$count_bonus = $row_5['count_bonus'];

$q_6 = dd_q('SELECT COUNT(u_id) AS count_user FROM user_tb WHERE u_date_create >= ? AND u_date_create <= ?', [$start_date, $end_date]);
$row_6 = $q_6->fetch(PDO::FETCH_ASSOC);

$count_user = $row_6['count_user'];
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
        <h1 style="font-size: 30px;">รายงานประจำวัน 
          <span style="font-size: 24px;" class="text-success">
            (วันที่ <?=$start_date?> ถึง <?=$end_date?>)
          </span>
        </h1>
      </section>
      <section class="content">
        <div class="row">
          <div class="col-sm-12">
            <div class="card card-outline">
              <div class="card-body box-profile">
                <div class="row">
                  <div class="col-sm-2 mb-1">
                    <b>เลือกช่วง วัน/เวลา :</b>
                  </div>
                  <div class="col-sm-3 mb-1">
                    <div class="input-group">
                      <input type="text" class="form-control" id="txt_dateStart" value="<?=date('d/m/Y', strtotime($start_date))?>">
                      <div class="input-group-append">
                        <span class="input-group-text">
                          <span class="far fa-calendar-alt"></span>
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-3 mb-1">
                    <div class="input-group">
                      <input type="text" class="form-control" id="txt_dateEnd" value="<?=date('d/m/Y', strtotime($end_date))?>">
                      <div class="input-group-append">
                        <span class="input-group-text">
                          <span class="far fa-calendar-alt"></span>
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-3 mb-1">
                    <button type="button" id="btn_search" class="btn btn-primary">ยืนยัน</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
      <section class="content">
	  <div class="row">
          <div class="col-sm-12">
            <div class="card <?php if (strpos($sum_total_all, '-') !== false){echo "card-danger";}else{echo "card-success";}?> card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <h1 style="color:#009900;">฿ <?=number_format($sum_total_all -  $sum_bonus, 2)?></h1>
                  <h5>รายได้สุทธิ</h5>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12">
            <div class="card <?php if (strpos($sum_total_all, '-') !== false){echo "card-danger";}else{echo "card-success";}?> card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <h1 style="color:#009900;">฿ <?=number_format($sum_total_all, 2)?></h1>
                  <h5>รายได้รวม(ยังไม่หักโปรโมชั่น)</h5>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-6">
            <div class="card card-success card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <h2 style="color:#009900;">฿ +<?=number_format($sum_topup_all, 2)?></h2>
                  <p>ยอดรวมฝากทั้งหมด (<?=number_format($count_topup_all, 0)?> รายการ)</p>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="card card-danger card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <h2 style="color:#FF0000;">฿ -<?=number_format($sum_withdraw_all, 2)?></h2>
                  <p>ยอดรวมถอนทั้งหมด (<?=number_format($count_withdraw_all, 0)?> รายการ)</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-6">
            <div class="card card-warning card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <h2 style="color:#ffc107;">฿ <?=number_format($sum_bonus, 2)?></h2>
                  <p>โบนัสรวม (<?=number_format($count_bonus, 0)?> รายการ)</p>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="card card-info card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <h2 style="color:#17a2b8;"><?=number_format($count_user, 0)?> ยูส</h2>
                  <p>ผู้เล่นสมัคร</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-3">
            <div class="card card-success card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <h2>฿ <?=number_format($sum_topup_manual, 2)?></h2>
                  <span>ยอดฝาก (ปรับมือ)</span>
                  <br>
                  <span>(<?=number_format($count_topup_manual, 0)?> รายการ)</span>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-3">
            <div class="card card-danger card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <h2>฿ <?=number_format($sum_withdraw_manual, 2)?></h2>
                  <span>ยอดถอน (ปรับมือ)</span>
                  <br>
                  <span>(<?=number_format($count_withdraw_manual, 0)?> รายการ)</span>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-3">
            <div class="card card-success card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <h2>฿ <?=number_format($sum_topup_auto, 2)?></h2>
                  <span>ยอดฝาก (ปรับอัตโนมัติ)</span>
                  <br>
                  <span>(<?=number_format($count_topup_auto, 0)?> รายการ)</span>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-3">
            <div class="card card-danger card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <h2>฿ <?=number_format($sum_withdraw_auto, 2)?></h2>
                  <span>ยอดถอน (ปรับอัตโนมัติ)</span>
                  <br>
                  <span>(<?=number_format($count_withdraw_auto, 0)?> รายการ)</span>
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
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/data.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script type="text/javascript">
  $('#txt_dateStart').datepicker({
    format:'dd/mm/yyyy',
    autoclose: true,
    todayBtn: true
  });
  $('#txt_dateEnd').datepicker({
    format:'dd/mm/yyyy',
    autoclose: true,
    todayBtn: true
  });
  $("#btn_search").click(function(e) {
    e.preventDefault();
    var arr_dateStart = $("#txt_dateStart").val().split("/");
    var _dateStart = arr_dateStart[2]+'-'+arr_dateStart[1]+'-'+arr_dateStart[0];

    var arr_dateEnd = $("#txt_dateEnd").val().split("/");
    var _dateEnd = arr_dateEnd[2]+'-'+arr_dateEnd[1]+'-'+arr_dateEnd[0];

    window.location = './daily_report?start_date='+_dateStart+'&end_date='+_dateEnd;
});
</script>
</html>