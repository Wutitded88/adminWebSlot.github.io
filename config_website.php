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
<!--
.style1 {
	color: #007bff;
	font-weight: bold;
	font-size: 24px;
}
.style2 {color: #009900}
.form-control {
    display: block;
    width: 100%;
    height: calc(2.25rem + 2px);
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: #ffffff;
    background-color: #242424;
    background-clip: padding-box;
    border: 1px solid #323232;
    border-radius: 0.25rem;
    box-shadow: inset 0 0 0 transparent;
    transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
}
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed text-sm">
  <div class="wrapper">
    <?php include ("partials/_navbar.php"); ?>
    <?php include ("partials/_sidebar.php"); ?>
    <div class="content-wrapper">
      <section class="content-header pt-4 pb-4">
      <div class="row">
        <div class="col-sm-3">
            <div class="card card-danger card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <h2>ตั้งค่าเว็บไซต์</h2>
                </div>
                
                  <span>ชื่อเว็บ</span>
                  <br>
                  <input id="id" name="id" class="form-control" type="text" value="1" size="100" hidden>
                  <input id="namesite" name="namesite" class="form-control" type="text" value="<?php echo $row_u['namesite']; ?>" size="100">
                  <br>
                  <span>title เว็บ</span>
                  <br>
                  <input name="title" id="title" type="text" class="form-control" value="<?php echo $row_u['title']; ?>" size="100">
                  <br>
                  <span>Discription</span>
                  <br>
                  <input name="description" id="description" type="text" class="form-control" value="<?php echo $row_u['description']; ?>" size="100">
                  <br>
                  <span>Keyword</span>
                  <br>
                  <input name="keyword" id="keyword" type="text" class="form-control" value="<?php echo $row_u['keyword']; ?>" size="100">
                  <br>
                  <span>Logo เว็บ </span>
                  <br>
                  <input name="logo" id="logo" type="text" class="form-control" value="<?php echo $row_u['logo']; ?>" size="100">
                  <br>
                  <span>พื้นหลังเว็บ</span>
                  <br>
                  <input name="bg" id="bg"  type="text" class="form-control" value="<?php echo $row_u['bg']; ?>" size="100">
                  <br>
                  <span>Copyright</span>
                  <br>
                  <input name="copyright" id="copyright" class="form-control"  type="text" value="<?php echo $row_u['copyright']; ?>" size="100">
                  <br>
                  <span>ประกาศหน้าสมาชิก</span>
                  <br>
                  <input name="post" id="post"  type="text" class="form-control" value="<?php echo $row_u['post']; ?>" size="100">
                  <br>
                  <span>URL Line ติดต่อ</span>
                  <br>
                  <input name="lineurl" id="lineurl"  type="text" class="form-control" value="<?php echo $row_u['lineurl']; ?>" size="100">
                  <br>
                  <span>ID Line ติดต่อ</span>
                  <br>
                  <input name="line" id="line" type="text" class="form-control" value="<?php echo $row_u['line']; ?>" size="100">
              </div>
            </div>
          </div>

          <div class="col-sm-3">
            <div class="card card-info card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <h2>ตั้งค่ารูปสไลด์โชว์หน้าเว็บ</h2>
                </div>
                  <span>ภาพสไลด์ 1</span>
                  <br>
                  <input name="slider1" id="slider1" class="form-control"  type="text" value="<?php echo $row_u['slider1']; ?>" size="100">
                  <br>
                  <span>ภาพสไลด์ 2</span>
                  <br>
                  <input name="slider2" id="slider2" class="form-control"  type="text" value="<?php echo $row_u['slider2']; ?>" size="100">
                  <br>
                  <span>ภาพสไลด์ 3</span>
                  <br>
                  <input name="slider3" id="slider3" class="form-control"  type="text" value="<?php echo $row_u['slider3']; ?>" size="100">
                  <br>
                  <span>ภาพสไลด์ 4</span>
                  <br>
                  <input name="slider4" id="slider4" class="form-control"  type="text" value="<?php echo $row_u['slider4']; ?>" size="100">
                  <br>
                  <span>ภาพสไลด์ 5</span>
                  <br>
                  <input name="slider5" id="slider5" class="form-control"  type="text" value="<?php echo $row_u['slider5']; ?>" size="100">
                  <br>
                  <span>ภาพสไลด์ 6</span>
                  <br>
                  <input name="slider6" id="slider6" class="form-control"  type="text" value="<?php echo $row_u['slider6']; ?>" size="100">
              </div>
            </div>
          </div>

          <div class="col-sm-3">
            <div class="card card-success card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <h2>ตั้งค่าระบบความปลอดภัย</h2>
                </div>
                  <span>Google reCAPTCHA Site V2</span>
                  <br>
                  <input name="recaptchakey" id="recaptchakey"  type="text" class="form-control" value="<?php echo $row_u['recaptchakey']; ?>" size="100"  >
                  <br>
                  <span>Google reCAPTCHA Secret V2</span>
                  <br>
                  <input name="recapchasecret" id="recapchasecret"  type="text" class="form-control" value="<?php echo $row_u['recapchasecret']; ?>" size="100"  >
              </div>
            </div>
          </div>

          <div class="col-sm-3">
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <h2>ตั้งค่า ชวนเพื่อนและโปรโมชั่นเสริม</h2>
                </div>
                  <span>ลำดับขั้นชวนเพื่อน</span>
                  <br>
                  <select name="aff_step" id="aff_step" class="form-control">
                    <option value="1" <?php if($row_u['aff_step'] == "1"){echo "selected";} ?>>1 ขั้น</option>
                    <option value="2" <?php if($row_u['aff_step'] == "2"){echo "selected";} ?>>2 ขั้น</option>
                    <option value="3" <?php if($row_u['aff_step'] == "3"){echo "selected";} ?>>3 ขั้น</option>
                  </select>
                  <br>
                  <span>% ชวนเพื่อน ขั้น 1</span>
                  <br>
                  <input name="affpersen" id="affpersen"  type="text" class="form-control" value="<?php echo $row_u['affpersen']; ?>" size="20">
                  % (0.1 เท่ากับ 10%) (ตอนนี้ : <span class="style2"><?php echo $row_u['affpersen']*100; ?>%</span>)
                  <br>
                  <br>
                  <span>% ชวนเพื่อน ขั้น 2</span>
                  <br>
                  <input name="affpersen2" id="affpersen2"  type="text" class="form-control" value="<?php echo $row_u['affpersen2']; ?>" size="20">
                  % (0.1 เท่ากับ 10%) (ตอนนี้ : <span class="style2"><?php echo $row_u['affpersen2']*100; ?>%</span>)
                  <br>
                  <br>
                  <span>% ชวนเพื่อน ขั้น 3</span>
                  <br>
                  <input name="affpersen3" id="affpersen3"  type="text" class="form-control" value="<?php echo $row_u['affpersen3']; ?>" size="20">
                  % (0.1 เท่ากับ 10%) (ตอนนี้ : <span class="style2"><?php echo $row_u['affpersen3']*100; ?>%</span>)
                  <br>
                  <br>
                  <span>ประเภทรายได้ที่คิดค่าแนะนำ</span>
                  <br>
                  <select name="aff_type" id="aff_type" class="form-control">
                    <option value="1" <?php if($row_u['aff_type'] == "1"){echo "selected";} ?>>ยอดฝากแรกของเพื่อน</option>
                    <option value="2" <?php if($row_u['aff_type'] == "2"){echo "selected";} ?>>ทุกยอดฝากของเพื่อน</option>
                    <option value="3" <?php if($row_u['aff_type'] == "3"){echo "selected";} ?>>ยอดเสียของเพื่อน เหมือนคืนยอดเสีย</option>
                    <option value="3" <?php if($row_u['aff_type'] == "4"){echo "selected";} ?>>Winloss</option>
                  </select>
                  <br>
                  <span>ค่าแนะนำที่รับได้สูงสุดต่อวัน</span>
                  <br>
                  <input name="aff_maxofday" id="aff_maxofday"  type="number" step="0.01" class="form-control" value="<?php echo $row_u['aff_maxofday']; ?>">
                  (หน่วยเป็นบาท)
                  <br>
                  <br>
                  <span>ถอนค่าแนะนำแบบติดโปร</span>
                  <br>
                  <select name="aff_promotion" id="aff_promotion" class="form-control">
                    <option value="1" <?php if($row_u['aff_promotion'] == "1"){echo "selected";} ?>>เปิด</option>
                    <option value="0" <?php if($row_u['aff_promotion'] == "0"){echo "selected";} ?>>ปิด</option>
                  </select>
                  <span style="color:#f00;">** ก่อนเปิดต้องไปตั้งโปรโมชั่นก่อน</span>
              </div>
            </div>
          </div>

          <div class="col-sm-3">
            <div class="card card-success card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <h2>ตั้งค่าการถอน และคืนยอดเสีย</h2>
                </div>
                  <span>ถอนเงินขั้นต่ำ</span>
                  <br>
                  <input name="min_withdraw" id="min_withdraw" class="form-control"  type="text" value="<?php echo $row_u['min_withdraw']; ?>" size="100">
                  <br>
                  <span>ยอดถอนสูงสุดต่อวัน (สำหรับสมาชิกสมัครใหม่) <span class="badge badge-success">New</span></span>
                  <br>
                  <input name="all_limitcredit" id="all_limitcredit"  type="number" step="1000.0" class="form-control" value="<?php echo $row_u['all_limitcredit']; ?>">
                  <div class="col-md-12 mt-2">
                    <button type="button" class="btn btn-block btn-success" data-toggle="modal" data-target="#modal-edit-player"><i class="fab fa-reddit-alien"></i> ปรับยอดถอนสูงสุดของสมาชิกทุกคน</button>
                  </div>
                  <br>
                  <span>% คืนยอดเสีย</span>
                  <br>
                  <input name="affwinloss" id="affwinloss"  type="number" step="0.1" class="form-control" value="<?php echo $row_u['affwinloss']; ?>" size="20">
                  % (0.1 เท่ากับ 10%) (ตอนนี้ : <span class="style2"><?php echo $row_u['affwinloss']*100; ?>%</span>)
                  <br>
                  <br>
                  <span>ยอดเสียขั้นต่ำที่รับคืนได้</span>
                  <br>
                  <input name="minwinloss" id="minwinloss"  type="number" min="1" class="form-control" value="<?php echo $row_u['minwinloss']; ?>" size="100">
              </div>
            </div>
          </div>

          <div class="col-sm-3">
            <div class="card card-warning card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <img style="background: #262626; padding: 20px; border-radius: 20px; margin: auto;" src="images/wallet-logo.png" width="300">
                  <br>
                  <br>
                  <h2>ตั้งค่า ทรูวอลเลตรับเงิน</h2>
                </div>
                  <span>เบอร์ทรูวอลเลตที่ใช้รับเงิน</span>
                  <br>
                  <input name="truewallet" id="truewallet"  type="text" class="form-control" value="<?php echo $row_u['truewallet']; ?>" size="100">
              </div>
            </div>
          </div>

          <div class="col-sm-3">
            <div class="card card-success card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <img style="background: white; border-radius: 20px; margin: auto;" src="images/linonotify.png" width="300">
                  <br>
                  <br>
                  <h2>ตั้งค่าไลน์แจ้งเตือน</h2>
                </div>
                  <span>Token Line ฝาก</span>
                  <br>
                  <input name="linewallet" id="linewallet"  type="text" class="form-control" value="<?php echo $row_u['linewallet']; ?>" size="100">
                  <br>
                  <span>Token Line สมัคร</span>
                  <br>
                  <input name="lineregister" id="lineregister"  type="text" class="form-control" value="<?php echo $row_u['lineregister']; ?>" size="100">
                  <br>
                  <span>token ไลน์ถอน</span>
                  <br>
                  <input name="tokenline" id="tokenline"  type="text" class="form-control" value="<?php echo $row_u['tokenline']; ?>" size="100"  >
              </div>
            </div>
          </div>

          <div class="col-sm-3">
            <div class="card card-info card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <h2>ตั้งค่าสมัครแบบ SMS</h2>
                </div>
                  <span>เปิด/ปิด SMS OTP</span>
                  <br>
                  <select name="sms_active" id="sms_active" class="form-control">
                    <option value="1" <?php if($row_u['sms_active'] == "1"){echo "selected";} ?>>เปิด</option>
                    <option value="0" <?php if($row_u['sms_active'] == "0"){echo "selected";} ?>>ปิด</option>
                  </select>
                  <br>
                  <span>User THSMS</span>
                  <br>
                  <input name="sms_user" id="sms_user" type="text" class="form-control" value="<?php echo $row_u['sms_user']; ?>" size="20">
                  <br>
                  <span>Password THSMS</span>
                  <br>
                  <input name="sms_pass" id="sms_pass" type="text" class="form-control" value="<?php echo $row_u['sms_pass']; ?>" size="20">
                  <br>
                  <br>
                  <span style="color:#ffa500;">สามารถใช้งานได้โดยสมัครสมาชิกที่เว็บ thsms.com แล้วนำ User และ Password มากรอกเพื่อใช้งานได้</span>
                  <br>
                  <span style="color:#50ca71;">หากเปิดใช้งาน ลูกค้าสามารถกดลืมรหัสผ่านเองได้ ด้วยการยืนยัน OTP</span>
              </div>
            </div>
          </div>

          <div class="col-sm-3">
            <div class="card card-success card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <h2>ตั้งค่า เปิด/ปิด หน้าเล่น</h2>
                </div>
                  <span>เปิด/ปิด คืนยอดเสีย</span>
                  <br>
                  <select name="status_winloss" id="status_winloss" class="form-control">
                    <option value="1" <?php if($row_u['status_winloss'] == "1"){echo "selected";} ?>>เปิด</option>
                    <option value="0" <?php if($row_u['status_winloss'] == "0"){echo "selected";} ?>>ปิด</option>
                  </select>
                  <br>
                  <span>เปิด/ปิด เช็คอินรายวัน</span>
                  <br>
                  <select name="status_checkin" id="status_checkin" class="form-control">
                    <option value="1" <?php if($row_u['status_checkin'] == "1"){echo "selected";} ?>>เปิด</option>
                    <option value="0" <?php if($row_u['status_checkin'] == "0"){echo "selected";} ?>>ปิด</option>
                  </select>
                  <br>
                  <span>เปิด/ปิด แนะนำเพื่อน</span>
                  <br>
                  <select name="status_aff" id="status_aff" class="form-control">
                    <option value="1" <?php if($row_u['status_aff'] == "1"){echo "selected";} ?>>เปิด</option>
                    <option value="0" <?php if($row_u['status_aff'] == "0"){echo "selected";} ?>>ปิด</option>
                  </select>
                  <br>
                  <span>เปิด/ปิด เติมเครดิตฟรี</span>
                  <br>
                  <select name="status_freecredit" id="status_freecredit" class="form-control">
                    <option value="1" <?php if($row_u['status_freecredit'] == "1"){echo "selected";} ?>>เปิด</option>
                    <option value="0" <?php if($row_u['status_freecredit'] == "0"){echo "selected";} ?>>ปิด</option>
                  </select>
                  <br>
                  <span>เปิด/ปิด Ranking</span>
                  <br>
                  <select name="status_ranking" id="status_ranking" class="form-control">
                    <option value="1" <?php if($row_u['status_ranking'] == "1"){echo "selected";} ?>>เปิด</option>
                    <option value="0" <?php if($row_u['status_ranking'] == "0"){echo "selected";} ?>>ปิด</option>
                  </select>
                  <br>
              </div>
            </div>
          </div> 
        </div> 
  <button type="submit" id="settingwebsite" class="btn btn-success btn-lg col-sm-12"><i class="fas fa-save fa-lg"></i> บันทึก</button>
</table>

</form>
    </div>
  </div>

  <!-- modal edit -->
  <div class="modal fade" id="modal-edit-player" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">ปรับยอดถอนสูงสุดของสมาชิกทุกคน</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>จำนวนยอดถอนสูงสุดต่อวัน</label>
            <div class="input-group">
            <input type="hidden" name="username" id="username" value="<?=get_session()?>">
            <input type="hidden" name="ip" id="ip" value="<?=get_client_ip()?>">
              <input name="all_limitcredita" id="all_limitcredita"  type="number" step="1000.0" class="form-control" value="0">
            </div>
          </div>
          <div class="text-right">
            <button type="button" class="btn btn-primary btn-sm" id="btn_save">
              <i class="mdi mdi-content-save mr-1"></i>
              ตกลง
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
</body>
</body>
<?php include('partials/_footer.php'); ?>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/data.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
 <script type="text/javascript">
 $("#btn_save").click(function(e) {
      e.preventDefault();

      var formData = new FormData();
      formData.append('all_limitcredita', $("#all_limitcredita").val());
      formData.append('username', $("#username").val());
      formData.append('ip', $("#ip").val());
      
      $('#loading').show();

      $.ajax({
          type: 'POST',
          url: '<?=base_url()?>/api/api_edit_limitcredit.php',
          data:formData,
          contentType: false,
          processData: false,
      }).done(function(res){
          result = res;
          alert(result.message);
          window.location = './config_website';
          console.clear();
          $('#loading').hide();
      }).fail(function(jqXHR){
          res = jqXHR.responseJSON;
          alert(res.message);
          console.clear();
          $('#loading').hide();
      });
  });
  $("#settingwebsite").click(function(e) {
    e.preventDefault();
    var formData = new FormData();
	formData.append('type', 'settingwebsite');
	formData.append('id', $("#id").val());
  formData.append('namesite', $("#namesite").val());
	formData.append('title', $("#title").val());
	formData.append('description', $("#description").val());
	formData.append('keyword', $("#keyword").val());
	formData.append('line', $("#line").val());
	formData.append('logo', $("#logo").val());
	formData.append('bg', $("#bg").val());
	formData.append('slider1', $("#slider1").val());
	formData.append('slider2', $("#slider2").val());
	formData.append('slider3', $("#slider3").val());
	formData.append('slider4', $("#slider4").val());
	formData.append('slider5', $("#slider5").val());
	formData.append('slider6', $("#slider6").val());
	formData.append('lineurl', $("#lineurl").val());
	formData.append('copyright', $("#copyright").val());
	formData.append('post', $("#post").val());
	formData.append('min_withdraw', $("#min_withdraw").val());
	formData.append('recaptchakey', $("#recaptchakey").val());
	formData.append('tokenline', $("#tokenline").val());
	formData.append('recapchasecret', $("#recapchasecret").val());
	formData.append('affpersen', $("#affpersen").val());
  formData.append('affpersen2', $("#affpersen2").val());
  formData.append('affpersen3', $("#affpersen3").val());
  formData.append('aff_step', $("#aff_step").val());
  formData.append('aff_type', $("#aff_type").val());
  formData.append('aff_maxofday', $("#aff_maxofday").val());
  formData.append('aff_promotion', $("#aff_promotion").val());
	formData.append('truewallet', $("#truewallet").val());
	formData.append('linewallet', $("#linewallet").val());
  formData.append('lineregister', $("#lineregister").val());
  formData.append('affwinloss', $("#affwinloss").val());
  formData.append('minwinloss', $("#minwinloss").val());
  formData.append('status_winloss', $("#status_winloss").val());
  formData.append('status_checkin', $("#status_checkin").val());
  formData.append('status_aff', $("#status_aff").val());
  formData.append('status_freecredit', $("#status_freecredit").val());
  formData.append('status_ranking', $("#status_ranking").val());
  formData.append('sms_user', $("#sms_user").val());
  formData.append('sms_pass', $("#sms_pass").val());
  formData.append('sms_active', $("#sms_active").val());
  formData.append('all_limitcredit', $("#all_limitcredit").val());

    $.ajax({
      type: 'POST',
      url: 'api/edit_website.php',
      data:formData,
      contentType: false,
      processData: false,
    }).done(function(res){
      result = res;
      alert(result.message);
      window.location = './config_website';
      console.clear();
    }).fail(function(jqXHR){
      res = jqXHR.responseJSON;
      alert(res.message);
      console.clear();
    });
  });
 </script>
</html>