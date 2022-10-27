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

$q_1 = dd_q('SELECT COUNT(t_id) AS count_topup, SUM(t_amount) AS sum_topup FROM topup_db WHERE t_date_create >= ? AND t_date_create <= ?', [$start_date, $end_date]);
$row_1 = $q_1->fetch(PDO::FETCH_ASSOC);
$count_topup = $row_1['count_topup'];

$q_2 = dd_q('SELECT COUNT(t_id) AS count_topup, SUM(t_amount) AS sum_topup FROM topup_db WHERE t_date_create >= ? AND t_date_create <= ? AND t_status = ?', [$start_date, $end_date, '1']);
$row_2 = $q_2->fetch(PDO::FETCH_ASSOC);
$count_topup_1 = $row_2['count_topup'];
$sum_topup_1 = $row_2['sum_topup'];

$q_3 = dd_q('SELECT COUNT(t_id) AS count_topup FROM topup_db WHERE t_date_create >= ? AND t_date_create <= ? AND t_status = ?', [$start_date, $end_date, '2']);
$row_3 = $q_3->fetch(PDO::FETCH_ASSOC);
$count_topup_2 = $row_3['count_topup'];

$q_4 = dd_q('SELECT COUNT(t_id) AS count_topup FROM topup_db WHERE t_date_create >= ? AND t_date_create <= ? AND t_type = ? AND t_status = ?', [$start_date, $end_date, '2', '1']);
$row_4 = $q_4->fetch(PDO::FETCH_ASSOC);
$count_topup_staff = $row_4['count_topup'];

$q_5 = dd_q('SELECT COUNT(t_id) AS count_topup FROM topup_db WHERE t_date_create >= ? AND t_date_create <= ? AND t_type = ?', [$start_date, $end_date, '1']);
$row_5 = $q_5->fetch(PDO::FETCH_ASSOC);
$count_topup_system = $row_5['count_topup'];

/* query bank */
$q_scb = dd_q('SELECT COUNT(t_id) AS count_topup, SUM(t_amount) AS sum_topup FROM topup_db WHERE t_date_create >= ? AND t_date_create <= ? AND t_type_system = ? AND t_status = ?', [$start_date, $end_date, 'scb', '1']);
$row_scb = $q_scb->fetch(PDO::FETCH_ASSOC);
$sum_topup_scb = $row_scb['sum_topup'];
$count_topup_scb = $row_scb['count_topup'];

$q_tmw = dd_q('SELECT COUNT(t_id) AS count_topup, SUM(t_amount) AS sum_topup FROM topup_db WHERE t_date_create >= ? AND t_date_create <= ? AND t_type_system = ? AND t_status = ?', [$start_date, $end_date, 'tmw', '1']);
$row_tmw = $q_tmw->fetch(PDO::FETCH_ASSOC);
$sum_topup_tmw = $row_tmw['sum_topup'];
$count_topup_tmw = $row_tmw['count_topup'];
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
        <h1 style="font-size: 30px;">รายการฝากเงินปรับมือ 
          <span style="font-size: 24px;" class="text-success">
            (วันที่ <?=$start_date?> ถึง <?=$end_date?>)
          </span>
        </h1>
      </section>
      <section class="content">
        <div class="row">
          <div class="col-sm-6">
            <div class="card card-success card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <h2>฿ <?=number_format($sum_topup_1, 2)?></h2>
                  <p>รายการยอดเงินฝากทั้งหมด</p>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <h2><?=number_format($count_topup, 0)?></h2>
                  <p>รายการฝากทั้งหมด</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-3">
            <div class="card card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <h2 class="text-danger"><?=number_format($count_topup_2, 0)?></h2>
                  <p>รายการผิดพลาด</p>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-3">
            <div class="card card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <h2 class="text-success"><?=number_format($count_topup_1, 0)?></h2>
                  <p>รายการที่ยืนยันแล้ว</p>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-3">
            <div class="card card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <h2><?=number_format($count_topup_staff, 0)?></h2>
                  <p>รายการฝาก (ปรับมือ)</p>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-3">
            <div class="card card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <h2><?=number_format($count_topup_system, 0)?></h2>
                  <p>รายการฝาก (ปรับอัตโนมัติ)</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
      <section class="content">
        <div class="row">
          <div class="col-sm-6 grid-margin stretch-card">
            <div class="card d-flex align-items-center card-outline">
              <div class="card-body box-profile">
                <div class="d-flex flex-row align-items-center">
                  <img class="rounded" src="./images/bank/scb.png" alt="SCB" style="width: 50px;">    
                  <div class="ml-4">
                    <h3 class="text-primary">฿ <?=number_format($sum_topup_scb, 2)?></h3>
                    <p class="mt-2 text-muted card-text"><?=number_format($count_topup_scb, 0)?> รายการ</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-6 grid-margin stretch-card">
            <div class="card d-flex align-items-center card-outline">
              <div class="card-body box-profile">
                <div class="d-flex flex-row align-items-center">
                  <img class="rounded" src="./images/bank/tmw.png" alt="True Wallet" style="width: 50px;">    
                  <div class="ml-4">
                    <h3 class="text-danger">฿ <?=number_format($sum_topup_tmw, 2)?></h3>
                    <p class="mt-2 text-muted card-text"><?=number_format($count_topup_tmw, 0)?> รายการ</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
      <section class="content mb-3">
        <div class="row">
          <div class="col-sm-12">
            <button type="button" id="btn_topup_modal" class="btn btn-success btn-block btn-lg">
              <i class="fas fa-plus"></i> เพิ่มรายการฝาก
            </button>
          </div>
        </div>
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
                        <th scope="col">จำนวนเงินฝาก</th>
                        <th scope="col">โอนมายังธนาคาร</th>
                        <th scope="col">สำเร็จโดย</th>
                        <th scope="col">วันสร้าง</th>
                        <th scope="col">เวลาสร้าง</th>
                        <th scope="col">สถานะ</th>
                        <th scope="col">ลบ</th>
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

  <!-- Modal -->
  <div class="modal fade" id="modal_topup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">เพิ่มรายการฝาก</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>ยูสเซอร์</label>
            <input type="number" class="form-control" maxlength="10" id="add_t_u_id" placeholder="0812345678">
          </div>
          <div class="form-group">
            <label>จำนวนเงินฝาก</label>
            <input type="number" class="form-control" id="add_t_amount" placeholder="0.00" min="0" step="0.01">
          </div>
          <div class="form-group">
            <label>เลือกธนาคารที่โอนเข้ามา</label>
            <select class="form-control" id="add_t_bank_id">
              <option value="">เลือกธนาคาร</option>
              <?php
              $q_bank = dd_q('SELECT a_id, a_bank_code, a_bank_name, a_bank_acc_number FROM autobank_tb', [$start_date, $end_date]);
              while($row_bank = $q_bank->fetch(PDO::FETCH_ASSOC))
              {
              ?>
              <option value="<?=$row_bank['a_id']?>"><?=$row_bank['a_bank_code']?> - <?=$row_bank['a_bank_name']?> (<?=$row_bank['a_bank_acc_number']?>)</option>
              <?php
              }
              ?>
            </select>
          </div>
          <div class="form-group">
            <label>วันที่</label>
            <div class="input-group">
              <input type="text" class="form-control" id="add_t_date_create" value="<?=date('d/m/Y')?>">
              <div class="input-group-append">
                <span class="input-group-text">
                  <span class="far fa-calendar-alt"></span>
                </span>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label>เวลา</label>
            <div class="row no-gutters">
              <div class="col-6">
                <input type="number" class="form-control" id="add_t_time_create_hour" placeholder="ชั่วโมง" step="1" min="0" max="24" value="<?=date('H')?>">
              </div>
              <div class="col-6">
                <input type="number" class="form-control" id="add_t_time_create_minute" placeholder="นาที" step="1" min="0" max="60" value="<?=date('i')?>">
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <div class="text-right">
            <button type="submit" id="btn_add_topup" class="btn btn-primary">
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
      "ajax": "./server-side/server_deposit-manual.php?startdate=<?=$start_date?>&enddate=<?=$end_date?>",
      "createdRow": function( row, data, dataIndex ) {
        if ( data[12] == "3" ) {
          $(row).addClass('delete-data-color');
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
  $('#add_t_date_create').datepicker({
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

    window.location = './deposit-manual?start_date='+_dateStart+'&end_date='+_dateEnd;
  });

  $("#btn_topup_modal").click(function(e) {
    e.preventDefault();
    $("#add_t_u_id").val('');
    $("#add_t_amount").val('');
    $("#add_t_bank_code").val('');
    $("#add_t_date_create").val("<?=date('d/m/Y')?>");
    $("#add_t_time_create_hour").val("<?=date('H')?>");
    $("#add_t_time_create_minute").val("<?=date('i')?>");
    $("#modal_topup").modal('show');
  });

  function onCancel($t_id)
  {
    var result = confirm("ยืนยันการดำเนินการต่อ ?");
    if (result)
    {
      var formData = new FormData();
      formData.append('t_id', $t_id);
      formData.append('type', "delete");
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
          window.location = '<?=base_url()?>/deposit-manual';
          console.clear();
          $('#loading').hide();
      }).fail(function(jqXHR){
          res = jqXHR.responseJSON;
          alert(res.message);
          console.clear();
          $('#loading').hide();
      });
    }
  }

  $("#btn_add_topup").click(function(e) {
    e.preventDefault();
    var result = confirm("ยืนยันการดำเนินการต่อ ?");
    if (result)
    {
      var formData = new FormData();
      formData.append('add_t_u_id', $("#add_t_u_id").val());
      formData.append('add_t_amount', $("#add_t_amount").val());
      formData.append('add_t_bank_id', $("#add_t_bank_id").val());
      var arr_date_create = $("#add_t_date_create").val().split("/");
      var _date_create = arr_date_create[2]+'-'+arr_date_create[1]+'-'+arr_date_create[0];
      formData.append('add_t_date_create', _date_create);
      formData.append('add_t_time_create_hour', $("#add_t_time_create_hour").val());
      formData.append('add_t_time_create_minute', $("#add_t_time_create_minute").val());

      formData.append('type', "add_topup");
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
          window.location = '<?=base_url()?>/deposit-manual';
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