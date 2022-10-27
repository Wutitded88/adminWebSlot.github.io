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
$q_u = dd_q('SELECT * FROM website_tb WHERE (id = ?)', [1]);
$row_u = $q_u->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
  <?php include("master/MasterPages.php"); ?>
  <style type="text/css">
 
.style1 {
	color: #007bff;
	font-weight: bold;
	font-size: 24px;
}
.style2 {color: #009900}
.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
</style>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed text-sm">
  <div class="wrapper">
    <?php include ("partials/_navbar.php"); ?>
    <?php include ("partials/_sidebar.php"); ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <div class="content-wrapper">
      <section class="content-header pt-4 pb-4">
        <h1 style="font-size: 30px;">ตั้งค่าค่ายเกม</h1>
        <?php
          $q_1 = dd_q('SELECT * FROM game_tb');
          while($row = $q_1->fetch(PDO::FETCH_ASSOC))
          {
        ?>
        <div class="card card-outline">
          <div class="card-body box-profile">
            <table width="753" border="0">
              <tr>
                <td><img style="border-radius:20px; width:150px;" src="https://aqua.paylegacy.com/<?php echo $row['g_img'] ?>"></td>
                <td><?php echo $row['g_name'] ?></td>
                <td>
                    <p>สถานะ ปกติ/ปรับปรุง</p>
                    <label class="switch">
                        <input class="checkboxclosegame" type="checkbox" b_id="<?php echo $row['g_id']; ?>" <?php if($row['g_closegame'] == 1) { echo "checked"; }?>>
                        <span class="slider round"></span>
                    </label>
                </td>
                <td>
                    <p>แสดงให้ลูกค้าเห็น</p>
                    <label class="switch">
                        <input class="checkboxshowgame" type="checkbox" b_id="<?php echo $row['g_id']; ?>" <?php if($row['g_status'] == 1) { echo "checked"; }?>>
                        <span class="slider round"></span>
                    </label>
                </td>
              </tr>
            </table>
          </div>
        </div>

        <?php
          }
        ?>
<script>
$(document).ready(function(){
    $(".checkboxclosegame").click(function () {
        var b_id=$(this).attr("b_id");
        var check = $(this).prop('checked') ? 1 : 0;
        $.ajax({
            type: "POST",
            url: "api/edit_game.php",
            data: "check=" + check + "&b_id=" + b_id,
            success: function(data) {
              swal("สำเร็จ", "บันทึกเรียบร้อย", {
                  icon : "success",
                  buttons: {        			
                      confirm: {
                          className : 'btn btn-danger'
                      }
                  },
              });
            }
        });
    });
});
$(document).ready(function(){
    $(".checkboxshowgame").click(function () {
        var b_id=$(this).attr("b_id");
        var check = $(this).prop('checked') ? 1 : 0;
        $.ajax({
            type: "POST",
            url: "api/edit_gamestatus.php",
            data: "check=" + check + "&b_id=" + b_id,
            success: function(data) {
              swal("สำเร็จ", "บันทึกเรียบร้อย", {
                  icon : "success",
                  buttons: {        			
                      confirm: {
                          className : 'btn btn-danger'
                      }
                  },
              });
            }
        });
    });
});
</script>
</form>
    </div>
  </div>
</body>
</body>
<?php include('partials/_footer.php'); ?>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/data.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>

</html>