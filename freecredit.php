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

$q_1 = dd_q('SELECT COUNT(g_id) AS count_freecredit FROM gencode_tb WHERE g_date_create >= ? AND g_date_create <= ?', [$start_date, $end_date]);
$row_1 = $q_1->fetch(PDO::FETCH_ASSOC);
$count_freecredit_all = $row_1['count_freecredit'];

$q_1_1 = dd_q('SELECT COUNT(g_id) AS count_freecredit FROM gencode_tb WHERE g_date_create >= ? AND g_date_create <= ? AND g_use = ?', [$start_date, $end_date, '0']);
$row_1_1 = $q_1_1->fetch(PDO::FETCH_ASSOC);
$count_freecredit_0 = $row_1_1['count_freecredit'];

$q_1_2 = dd_q('SELECT COUNT(g_id) AS count_freecredit FROM gencode_tb WHERE g_date_create >= ? AND g_date_create <= ? AND g_use = ?', [$start_date, $end_date, '1']);
$row_1_2 = $q_1_2->fetch(PDO::FETCH_ASSOC);
$count_freecredit_1 = $row_1_2['count_freecredit'];
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
        <h1 style="font-size: 30px;">แจกเครดิตฟรี 
          <span style="font-size: 24px;" class="text-success">
            (วันที่ <?=$start_date?> ถึง <?=$end_date?>)
          </span>
        </h1>
      </section>
      <section class="content">
        <div class="row">
          <div class="col-sm-6">
            <div class="card card-warning card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <h2>฿ <?=number_format(($count_freecredit_all * 50), 2)?></h2>
                  <p>รายการยอดเงินแจกเครดิตฟรีทั้งหมด</p>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <h2><?=number_format($count_freecredit_all, 0)?></h2>
                  <p>รายการแจกเครดิตฟรีทั้งหมด</p>
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
                  <h2><?=number_format($count_freecredit_1, 0)?></h2>
                  <p>เติมโค๊ดแล้วทั้งหมด</p>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="card card-danger card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <h2><?=number_format($count_freecredit_0, 0)?></h2>
                  <p>ยังไม่เติมโค๊ดทั้งหมด</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
      <section class="content mb-3">
        <!-- <div class="row">
          <div class="col-sm-12">
            <button type="button" class="btn btn-success btn-block" data-toggle="modal" data-target="#modal-freecredit">
              <i class="fas fa-plus"></i> แจกเครดิตฟรี
            </button>
          </div>
        </div> -->

        <!-- modal edit -->
        <div class="modal fade" id="modal-freecredit" tabindex="-1" role="dialog">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">แจกเครดิตฟรี</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <div class="form-group">
                  <label>เบอร์โทรศัพท์ที่ใช้สมัครสมาชิก <span class="text-danger">**ตัวเลขเท่านั้น</span></label>
                  <div class="input-group">
                    <input type="number" class="form-control" id="g_phone" maxlength="10" placeholder="เบอร์โทรศัพท์">
                  </div>
                </div>
                <p class="text-danger">สมัคร thsms.com เพื่อใช้ฟังชั่นส่ง sms หากสมัครแล้วกรุณาติดต่อผู้พัฒนา เพื่อทำการเปิดใช้งานฟังชั่น</p>
                <div class="text-right">
                  <button type="button" class="btn btn-primary btn-sm" id="btn_save">
                    <i class="mdi mdi-content-save mr-1"></i>
                    บันทึก
                  </button>
                  <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                    <i class="mdi mdi-close mr-1"></i>
                    ปิด
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- end modal edit -->

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
            <div class="card card-outline">
              <div class="card-body box-profile">
                <div class="table-responsive">
                  <table class="table" id="tb_data" style="width: 100%;">
                    <thead class="text-center">
                      <tr>
                        <th scope="col">ยูเซอร์</th>
                        <th scope="col">ชื่อ นามสกุล</th>
                        <th scope="col">โค๊ดเครดิตฟรี</th>
                        <th scope="col">สถานะ</th>
                        <th scope="col">วันสร้าง</th>
                        <th scope="col">เวลาสร้าง</th>
                        <th scope="col">สร้างโดย</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $q_2 = dd_q('SELECT u.u_id AS u_id, u.u_user AS u_user, u.u_fname AS u_fname, u.u_lname AS u_lname, g.g_code AS g_code, g.g_use AS g_use, g.g_create_by AS g_create_by, g.g_create_date AS g_create_date, g.g_date_create AS g_date_create, g.g_time_create AS g_time_create FROM gencode_tb AS g JOIN user_tb AS u ON g.g_phone = u.u_user WHERE g.g_date_create >= ? AND g.g_date_create <= ? ORDER BY g.g_id DESC', [$start_date, $end_date]);
                      while($row = $q_2->fetch(PDO::FETCH_ASSOC))
                      {
                      ?>
                      <tr>
                        <td>
                          <a href="./customer/detail/<?=$row['u_id']?>" target="_blank"><?=$row['u_user']?></a>
                        </td>
                        <td>
                          <?=$row['u_fname']?> <?=$row['u_lname']?>
                        </td>
                        <td>
                          <?=$row['g_code']?>
                        </td>
                        <td>
                          <?php
                          if($row['g_use'] == "0")
                          {
                          ?>
                          <span class="text-danger">ยังไม่ใช้งาน</span>
                          <?php
                          }
                          else if($row['g_use'] == "1")
                          {
                          ?>
                          <span class="text-success">ใช้งานแล้ว</span>
                          <?php
                          }
                          ?>
                        </td>
                        <td>
                          <?=date('d/m/Y', strtotime($row['g_date_create']))?>
                        </td>
                        <td>
                          <?=$row['g_time_create']?>
                        </td>
                        <td>
                          <?=$row['g_create_by']?>
                        </td>
                      </tr>
                      <?php
                      }
                      ?>
                    </tbody>
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
      }
    });
  });

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

    window.location = './freecredit?start_date='+_dateStart+'&end_date='+_dateEnd;
  });

  $("#btn_save").click(function(e) {
    e.preventDefault();

    var formData = new FormData();
    formData.append('g_phone', $("#g_phone").val());

    formData.append('username', $("#username").val());
    formData.append('ip', $("#ip").val());

    $.ajax({
        type: 'POST',
        url: '<?=base_url()?>/system/api_freecredit',
        data:formData,
        contentType: false,
        processData: false,
    }).done(function(res){
        result = res;
        alert(result.message);
        window.location = '<?=base_url()?>/freecredit';
        console.clear();
    }).fail(function(jqXHR){
        res = jqXHR.responseJSON;
        alert(res.message);
        $("#g_phone").val('');
        console.clear();
    });
  });

</script>
</html>