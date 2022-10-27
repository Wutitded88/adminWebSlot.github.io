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
<style>
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
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed text-sm">
  <input type="hidden" id="username" value="<?=get_session()?>">
  <input type="hidden" id="ip" value="<?=get_client_ip()?>">
  <div class="wrapper">
    <?php include ("partials/_navbar.php"); ?>
    <?php include ("partials/_sidebar.php"); ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <div class="content-wrapper">
      <section class="content-header pt-4 pb-2">
        <h1 style="font-size: 30px;" class="mb-1">ตั้งค่าเกมที่นำมาคิดยอดเทิร์น <small>(Admin only)</small></h1>
        <h5 class="text-danger">**ตั้งค่าครั้งเดียวมีผลกับทุกโปรโมชั่น ที่มีการคิดยอดเทิร์นแบบ winloss</h5>
      </section>
      <section class="content">
        <div class="col-sm-12">
            <div class="card card-outline">
              <div class="card-body box-profile">
                <div class="table-responsive">
                  <form method="POST">
                    <table class="table table-bordered table-hover" id="tb_data" style="width: 100%;">
                      <thead class="thead-light text-center">
                        <tr>
                          <th scope="col">ลำดับ</th>
                          <th scope="col">ประเภท</th>
                          <th scope="col">คิดยอดเทิร์น</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $_no = 0;
                        $q_1 = dd_q('SELECT * FROM turnover_tb ORDER BY t_id ASC');
                        while($row = $q_1->fetch(PDO::FETCH_ASSOC))
                        {
                          $_no++;
                        ?>
                        <tr>
                          <td class="text-center"><?=$_no?></td>
                          <td><?=$row['t_code']?></td>
                          <td>
                            <label class="switch">
                              <input class="checkboxtover" type="checkbox" b_id="<?php echo $row['t_id']; ?>" <?php if($row['t_active'] == 1) { echo "checked"; }?>>
                              <span class="slider round"></span>
                          </label>
                          </td>
                        </tr>
                        <?php
                        }
                        ?>
                      </tbody>
                    </table>
 
                  </form>
                </div>
              </div>
            </div>
          </div>
      </section>
    </div>
  </div>
<script> 
$(document).ready(function(){
    $(".checkboxtover").click(function () {
        var b_id=$(this).attr("b_id");
        var check = $(this).prop('checked') ? 1 : 0;
        $.ajax({
            type: "POST",
            url: "api/edit_tover.php",
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
</body>
<?php include('partials/_footer.php'); ?>
</html>