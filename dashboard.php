<?php
require_once 'api/config.php';
if(empty(get_session()))
{
  echo "<SCRIPT LANGUAGE='JavaScript'>
          window.location.href = './login';
        </SCRIPT>";
  exit();
}
/* password_encode($string) */
$start_date = date('Y-m-d');
$end_date = date('Y-m-d');

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

$q_5 = dd_q('SELECT COUNT(t_id) AS count_bonus, SUM(t_bonus) AS sum_bonus FROM transfergame_tb WHERE t_date_create >= ? AND t_date_create <= ? AND t_bonus > ?', [$start_date, $end_date, 0]);
$row_5 = $q_5->fetch(PDO::FETCH_ASSOC);

$sum_bonus = $row_5['sum_bonus'];
$count_bonus = $row_5['count_bonus'];

$q_6 = dd_q('SELECT COUNT(u_id) AS count_user FROM user_tb');
$row_6 = $q_6->fetch(PDO::FETCH_ASSOC);

$count_user = $row_6['count_user'];


/* Static value report */
$end_date_report = date('Y-m-d');
$start_date_report = date('Y-m-d', strtotime($end_date_report. ' - 6 days'));
$date_rang_report = getDatesFromRange($start_date_report, $end_date_report);
 
/*  */
/* $q_7 = dd_q("SELECT COUNT(u_id) AS count_online FROM user_tb WHERE (`u_last_login` LIKE '%".date("Y-m-d")."%')", [$end_date]);
$row_7 = $q_7->fetch(PDO::FETCH_ASSOC); */
$count_online_all =0; /* $row_7['count_online'];
 */
/* Getting report money */
$q_report_topup = dd_q('SELECT t_date_create AS date_topup, SUM(t_amount) AS sum_topup FROM topup_db WHERE t_date_create >= ? AND t_date_create <= ? AND t_status = ? GROUP BY t_date_create', [$start_date_report, $end_date_report, "1"]);
$arr_report_topup = array();
while($row_report_topup = $q_report_topup->fetch(PDO::FETCH_ASSOC))
{
  array_push($arr_report_topup, $row_report_topup);
}

$q_report_withdraw = dd_q('SELECT w_date_create AS date_withdraw, SUM(w_amount) AS sum_withdraw FROM withdraw_tb WHERE w_date_create >= ? AND w_date_create <= ? AND w_status = ? GROUP BY w_date_create', [$start_date_report, $end_date_report, "1"]);
$arr_report_withdraw = array();
while($row_report_withdraw = $q_report_withdraw->fetch(PDO::FETCH_ASSOC))
{
  array_push($arr_report_withdraw, $row_report_withdraw);
}

/* Getting report user */
$q_report_user = dd_q('SELECT u_date_create as date_user, COUNT(u_id) AS count_user FROM user_tb WHERE u_date_create >= ? AND u_date_create <= ? GROUP BY u_date_create', [$start_date_report, $end_date_report]);
$arr_report_user = array();
$arr_report_user_topup = array();
while($row_report_user = $q_report_user->fetch(PDO::FETCH_ASSOC))
{
  // print_r($row_report_user);
  array_push($arr_report_user, $row_report_user);

  $q_user_tp = dd_q('SELECT u_id FROM user_tb AS t1 INNER JOIN topup_db AS t2 ON t1.u_id = t2.t_u_id WHERE t1.u_date_create = ? GROUP BY t2.t_u_id', [$row_report_user["date_user"]]);
  $array_user_tp = array();
  if ($q_user_tp->rowCount() > 0)
  {
    $u_topup_count = 0;
    while($row_q_user_tp = $q_user_tp->fetch(PDO::FETCH_ASSOC))
    {
      $u_topup_count++;
    }
    $array_user_tp = array("date_user_topup" => $row_report_user["date_user"], "count_user_topup" => $u_topup_count);
    array_push($arr_report_user_topup, $array_user_tp);
  }
  else
  {
    $array_user_tp = array("date_user_topup" => $row_report_user["date_user"], "count_user_topup" => 0);
    array_push($arr_report_user_topup, $array_user_tp);
  }
}

// Function to get all the dates in given range 
function getDatesFromRange($start, $end, $format = 'Y-m-d') { 
      
  // Declare an empty array 
  $array = array(); 
    
  // Variable that store the date interval 
  // of period 1 day 
  $interval = new DateInterval('P1D'); 

  $realEnd = new DateTime($end); 
  $realEnd->add($interval); 

  $period = new DatePeriod(new DateTime($start), $interval, $realEnd); 

  // Use loop to store date into array 
  foreach($period as $date) {                  
      $array[] = $date->format($format);  
  } 

  // Return the array elements 
  return $array; 
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
        <h1 style="font-size: 30px;">หน้าแรก</h1>
      </section>
      <section class="content">
        <div class="row">
          <div class="col-sm-6">
            <div class="card card-success card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <h2>฿ <?=number_format($sum_topup_all, 2)?></h2>
                  <p>ยอดรวมฝากทั้งหมด (<?=number_format($count_topup_all, 0)?> รายการ)</p>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="card card-danger card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <h2>฿ <?=number_format($sum_withdraw_all, 2)?></h2>
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
                  <h2>฿ <?=number_format($sum_bonus, 2)?></h2>
                  <p>โบนัสรวม (<?=number_format($count_bonus, 0)?> รายการ)</p>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-3">
            <div class="card card-info card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <h2><?=number_format($count_user, 0)?></h2>
                  <p>ผู้เล่นทั้งหมด</p>
                </div>
              </div>
            </div>
          </div>
		  <div class="col-sm-3">
            <div class="card card-info card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <h2><?=number_format($count_online_all, 0)?></h2>
                  <p>สมาชิกเล่นวันนี้</p>
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
      <section class="content">
        <div class="row">
          <div class="col-sm-6">
            <div class="card card-success card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <h4 class="text-success">รายการฝากเงินล่าสุด</h4>
                </div>
                <div class="table-responsive">
                  <div id="div_topup"></div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="card card-danger card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <h4 class="text-danger">รายการถอนเงินล่าสุด</h4>
                </div>
                <div class="table-responsive">
                  <div id="div_withdraw"></div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-12">
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <div id="div_report_1"></div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-12">
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <div id="div_report_2"></div>
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
  var date_rang = <?php echo json_encode($date_rang_report); ?>;

  /* variable report money */
  var arr_report_topup = <?php echo json_encode($arr_report_topup); ?>;
  var arr_report_withdraw = <?php echo json_encode($arr_report_withdraw); ?>;

  var arr_topup = [];
  var arr_withdraw = [];
  var arr_balance = [];
  date_rang.forEach((dt) => {
    var find_topup = arr_report_topup.find(a => a.date_topup == dt);
    arr_topup.push(find_topup ? parseFloat(find_topup.sum_topup) : 0);

    var find_withdraw = arr_report_withdraw.find(a => a.date_withdraw == dt);
    arr_withdraw.push(find_withdraw ? parseFloat(find_withdraw.sum_withdraw) : 0);

    arr_balance.push((find_topup ? parseFloat(find_topup.sum_topup) : 0) - (find_withdraw ? parseFloat(find_withdraw.sum_withdraw) : 0));

  });
  
  /* variable report user */
  var arr_report_user = <?php echo json_encode($arr_report_user); ?>;
  var arr_report_user_topup = <?php echo json_encode($arr_report_user_topup); ?>;

  var arr_user = [];
  var arr_user_topup = [];

  date_rang.forEach((dt) => {
    var find_user = arr_report_user.find(a => a.date_user == dt);
    arr_user.push(find_user ? parseFloat(find_user.count_user) : 0);

    var find_user_topup = arr_report_user_topup.find(a => a.date_user_topup == dt);
    arr_user_topup.push(find_user_topup ? parseFloat(find_user_topup.count_user_topup) : 0);
  });

  // var arr_report_user_series = date_rang.map((dt) => {
  //   var user_count = arr_report_user.find(a => a.date_user == dt);
  //   if(user_count){
  //     return parseInt(user_count.count_user);
  //   }
  //   else{
  //     return 0;
  //   }
  // });

  $(function(){
    var get_topup=$.ajax({
      url:"./dashboard_topup",
      data:"rev=1",
      async:false,
      success:function(get_topup){
        $("#div_topup").html(get_topup);
      }
    }).responseText;

    setInterval(function(){
      var get_topup=$.ajax({
        url:"./dashboard_topup",
        data:"rev=1",
        async:false,
        success:function(get_topup){
          $("#div_topup").html(get_topup);
        }
      }).responseText;
    },10000);

    var get_withdraw=$.ajax({
      url:"./dashboard_withdraw",
      data:"rev=1",
      async:false,
      success:function(get_withdraw){
        $("#div_withdraw").html(get_withdraw);
      }
    }).responseText;

    setInterval(function(){
      var get_withdraw=$.ajax({
        url:"./dashboard_withdraw",
        data:"rev=1",
        async:false,
        success:function(get_withdraw){
          $("#div_withdraw").html(get_withdraw);
        }
      }).responseText;
    },10000);

    $('#div_report_1').highcharts({
    chart: {
        type: 'column'
    },
    credits: {
        enabled: false
    },
    exporting: {
      enabled: false
    },
    title: {        
        useHTML: true,
        align: "left",
        text: "<h3><i class='fas fa-chart-bar'></i> ยอดเงิน ฝาก-ถอน <font class='text-success'>(7 วันล่าสุด)</font></h3>"

    },
    subtitle: {
        useHTML: true,
        align: "left",
        text: "<h6 class='text-danger'>เฉพาะยอดเงินที่ทำรายการสำเร็จ</h6>"
    },
    xAxis: {
        categories: date_rang
    },
    yAxis: {
        min: 0,
        labels: {
          format: '฿ {value}'
        },
        title: {
          text: ''
        }
    },
    tooltip: {
        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
            '<td style="padding:0"><b>{point.y:.0f} บาท</b></td></tr>',
        footerFormat: '</table>',
        shared: true,
        useHTML: true
    },
    legend:{
        align: 'right',
        symbolRadius: 0
    },
    colors: ["#007bff", "#dc3545", "#28a745"],
    series: [{
        name: 'รายการฝาก',
        data: arr_topup

    }, {
        name: 'รายการถอน',
        data: arr_withdraw

    }, {
        name: 'รายการหักลบ',
        data: arr_balance
    }]
    });

    $('#div_report_2').highcharts({
    chart: {
        type: 'areaspline'
    },
    credits: {
        enabled: false
    },
    exporting: {
      enabled: false
    },
    title: {
        useHTML: true,
        align: "left",
        text: "<h3><i class='fas fa-user-plus'></i> แสดงผู้เล่นสมัครสมาชิก <font class='text-success'>(7 วันล่าสุด)</font></h3>"
    },
    xAxis: {
        categories: date_rang
    },
    yAxis: {
        title: {
          text: ''
        }
    },
    tooltip: {
        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
            '<td style="padding:0"><b>{point.y:.0f} คน</b></td></tr>',
        footerFormat: '</table>',
        shared: true,
        useHTML: true
    },
    legend:{
        enabled: false
    },
    plotOptions: {
        areaspline: {
            fillOpacity: 0.5
        }
    },
    colors: ["#007bff", "#dc3545"],
    series: [{
        name: 'ผู้เล่นสมัครรายวัน',
        data: arr_user

    }, {
        name: 'ผู้เล่นสมัครฝาก',
        data: arr_user_topup

    }]
  })

});
 /*var news = 'แจ้งให้ทราบ|<a style="color: #dc3545; font-family: thaisanslite_r1; font-size: 18px;">เคลียเครดิต % ถือสู้ทุกวัน อังคาร (หากไม่เคลียภายในวันที่แจ้งทางเราขอ ปิดใช้งานเว็บก่อนจนกว่าจะเคลียยอด)</a>';
  var news_imgs = '';

  news = news.split('|');
  var wrapper = document.createElement('div');
  wrapper.innerHTML = news[1];

  setTimeout(function(){
    swal.fire({
      title: news[0],
      text: news[1],
      imageUrl: news_imgs,
      icon: news_imgs,
      content: wrapper,
      animation: true,
    });
    $('.swal2-content').html(news[1]);
    $('.swal2-title').css('color','red');
  },1000); */
</script> 
</html>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/8.11.8/sweetalert2.all.js" aria-hidden="true"></script>