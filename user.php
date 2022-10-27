<?php
require_once 'api/config.php';
if(empty(get_session()))
{
  echo "<SCRIPT LANGUAGE='JavaScript'>
          window.location.href = './login';
        </SCRIPT>";
  exit();
}

if(isset($_GET['search']) && $_GET['search'] != "")
{
  $search = $_GET['search'];
}
else
{
  $search = "";
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
        <h1 style="font-size: 30px;">ข้อมูลผู้เล่น</h1>
      </section>
      <section class="content">
        <div class="row">
          <div class="col-sm-12">
            <div class="card card-outline">
              <div class="card-body box-profile">
                <div class="row">
                  <div class="col-sm-12 mb-3">
                    <span style="font-size: 16px;">ค้นหาผู้เล่น (ยูสเซอร์, ชื่อ, นามสกุล, เลขบัญชี, เบอร์โทรศัพท์) :</span>
                  </div>
                  <div class="col-sm-12 mb-3">
                    <input type="text" class="form-control" id="txt_search" placeholder="คำที่ต้องการค้นหา" value="<?=$search?>">
                  </div>
                  <div class="col-sm-12">
                    <button type="button" id="btn_search" class="btn btn-success btn-block">
                      <i class="fas fa-search"></i> ค้นหา
                    </button>
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
                        <th scope="col">เบอร์</th>
                        <th scope="col">ชื่อ-นามสกุล</th>
                        <th scope="col">เครดิตฟรี</th>
                        <th scope="col">สถานะ</th>
                        <th scope="col">ยูสเข้าเกม</th>
                        <th scope="col">รหัสผ่าน</th>
                        <th scope="col">#</th>
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
      "ajax": "./server-side/server_user.php?searchdata=<?=$search?>"
    });
  });

  $("#btn_search").click(function(e) {
    e.preventDefault();
    window.location = './user?search='+$("#txt_search").val();
});
</script>
</html>