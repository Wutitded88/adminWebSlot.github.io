<?php
require_once 'api/config.php';
if(empty(get_session()))
{
  echo "<SCRIPT LANGUAGE='JavaScript'>
          window.location.href = './login';
        </SCRIPT>";
  exit();
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
-->
.imgborder {
	border: 5px solid #4f6ab5;
	width: 50%;
  	height: auto;
	float: left;
	padding:10px;
}
@media screen and (max-width: 600px) {
	.imgborder {
		width:100%;
	}
}
h1 {
	padding-bottom:10px;
}
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed text-sm">
  <div class="wrapper">
    <?php include ("partials/_navbar.php"); ?>
    <?php include ("partials/_sidebar.php"); ?>
    <div class="content-wrapper">
      <section class="content-header pt-4 pb-4">
        <h1 style="font-size: 30px;">วิธีใช้งานหลังบ้าน Paylegacy </h1>
		<br>
		<div id="accordion">
		  <div class="card">
			<div class="card-header" id="heading1">
			  <h5 class="mb-0">
				<button class="btn btn-link" data-toggle="collapse" data-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
				  1. ระบบฝาก ปรับมือ - อัตโนมัติ - รายการผิดพลาด
				</button>
			  </h5>
			</div>
		
			<div id="collapse1" class="collapse" aria-labelledby="heading1" data-parent="#accordion">
			  <div class="card-body">
				<h1>1. ระบบฝากอัตโนมัติ จะทำงานทุก 1 นาที</h1>
				<h1>2. วิธีปรับมือ ให้กดไปที่ รายการฝาก > ปรับมือ</h1>
				<div class="temjor">
				<img class="imgborder" src="imghowto/deposit1.png">
				<img class="imgborder" src="imghowto/deposit2.png">
				</div>
				<h1>3. เมื่อมีรายการผิดพลาด</h1>
				<img class="imgborder" src="imghowto/deposit3.png">
				<img class="imgborder" src="imghowto/deposit4.png">
				<img class="imgborder" src="imghowto/deposit5.png">
				</div>
				<p>
					<h1>** เพิ่มเติม **</h1>
					<h1>1. ฝากอัตโนมัติ ช่วงเวลา 23:00 - 00:00 ธนาคารจะมีปัญหา ต้องปรับมือเท่านั้น</h1>
					<h1>2. วิธีแก้รายการผิดพลาด 1. เลขบัญชีลูกค้าอาจกรอกมาไม่ตรง หรือ ชื่อลูกค้าไม่ตรง ให้ขอเลขกับชื่อของลูกค้า มาแก้ไขให้ถูกต้อง</h1>
				</p>
			  </div>
			</div>
		  </div>
		  <div class="card">
			<div class="card-header" id="heading2">
			  <h5 class="mb-0">
				<button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
				 2. ถอนเงิน
				</button>
			  </h5>
			</div>
			<div id="collapse2" class="collapse" aria-labelledby="heading2" data-parent="#accordion">
			  <div class="card-body">
				<h1>วิธีถอนเงิน</h1>
				<h1>เมื่อมีรายการถอนเงินเข้ามา กดที่ รอโอนเงิน</h1>
				<p><img class="imgborder" src="imghowto/ton1.png"></p>
				<p><img class="imgborder" src="imghowto/ton2.png"></p>
				<p><img class="imgborder" src="imghowto/ton3.png"></p>
			  </div>
			</div>
		  </div>
		  <div class="card">
			<div class="card-header" id="heading3">
			  <h5 class="mb-0">
				<button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
				  3. วิธีแก้ไขข้อมูล สมาชิก ตรวจสอบสมาชิก (รวมถึงวิธีลบยูส)
				</button>
			  </h5>
			</div>
			<div id="collapse3" class="collapse" aria-labelledby="heading3" data-parent="#accordion">
			  <div class="card-body">
				<h1>1. กดไปที่เมนู ข้อมูลผู้เล่น </h1>
				<h1>2. ค้นหาผู้เล่นที่ต้องการ</h1>
				<h1>3. กด <a class="btn btn-primary">ดูข้อมูล</a> </h1>
				<h1>3. หากต้องการแก้ไขข้อมูล กดแก้ไขข้อมูล <button class="btn btn-block btn-warning">แก้ไขข้อมูล</button></h1>
			  </div>
			</div>
		  </div>
		  
		  <div class="card">
			<div class="card-header" id="heading4">
			  <h5 class="mb-0">
				<button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse4" aria-expanded="false" aria-controls="collapse4">
				 4. วิธีตั้งค่า โปรโมชั่น
				</button>
			  </h5>
			</div>
			<div id="collapse4" class="collapse" aria-labelledby="heading4" data-parent="#accordion">
			  <div class="card-body">
				<h1>กดไปที่เมนู จัดการโปรโมชั่น</h1>
				<h1>กดปุ่มสีเขียวด้านบน เพื่อเพิ่มโปรโมชั่น</h1>
				<h1>** โปรโมชั่นแรก ฟรีเครดิต 50 จะไม่แสดงหน้าโปรโมชั่นให้ลูกค้าเห็น จะรับโปรนี้ได้ต้องใส่ โค้ดเครดิตฟรีเท่านั้น ** </h1>
				<p><img class="imgborder" src="imghowto/pro1.png"></p>
				<h1>ข้อ 1 ใส่ลิงค์รูปของโปรโมชั่น </h1>
				<h1>ข้อ 2 ชื่อโปรโมชั่น เช่น สมาชิกใหม่รับ 20%</h1>
				<h1>ข้อ 3 รายละเอียดของโปรโมชั่น</h1>
				<h1>ข้อ 4 การคำนวน ถ้าเป็น เปอร์เซ็น ตรงจำนวนรางวัลจะเป็น % ของโปรนี้ เช่น จำนวนรางวัล 20 ก็จะเป็นการรับโปร 20%</h1>
				<h1>ข้อ 4.1 การคำนวน ถ้าเป็นการคำนวนแบบ เครดิต ใส่จำนวนรางวัล 100 ก็จะได้รับเครดิตเพิ่ม 100</h1>
				<h1>ข้อ 5 จำนวนเครดิต</h1>
				<h1>ข้อ 6 รับโบนัสสูงสุดเท่าไหร่ ถ้าการคำนวนแบบ เครดิต ให้ใส่เท่าจำนวนรางวัล</h1>
				<h1>ข้อ 7 ย้ายเงินขั้นต่ำ คือ ต้องมีเงินเท่าไหร่ถึงจะรับโปรนี้ได้ </h1>
				<h1>ข้อ 8 ประเภทโบนัส ตั้งได้ตามใจชอบ</h1>
				<h1>ข้อ 9 การคำนวนยอดเทิร์น</h1>
				<h1>ข้อ 9.1 แบบ เท่า คือต้องมีเครดิตเท่านี้ถึงถอนได้ เช่น โปรรับ 20% ฝาก 100 รับ 20% เป็น 120 ทำยอด 2 เท่าเป็น 240 ก็คือต้องเล่นให้ได้เครดิต 240 ถึงถอนได้</h1>
				<h1>ข้อ 9.2 แบบ % คือต้องมีเครดิตเท่านี้ถึงถอนได้ เช่น รับโปร 20% ฝาก 100 รับ 20% เป็น 120 ทำยอด 20% เป็น 144 ก็คือต้องเล่นให้ได้เครดิต 144 ถึงถอนได้</h1>
				<h1>ข้อ 9.3 แบบ winloss คือการนับยอด ได้เสีย ในการเล่นนับรวมเป็นยอดเทิร์น สามารถตั้งค่าเกมที่นำมาคิดยอดเทิร์นได้</h1>
				<h1>ข้อ 10 ยอดเทิร์นที่ต้องทำ</h1>
				<h1>ข้อ 11 ถอนได้สูงสุดเท่าไหร่ **ถ้าถอนไม่จำกัด ใส่ 0 (เช่น ตั้งโปรไว้ถอนสูงสุด 500 หากลูกค้ารับโปร แล้วทำยอดได้ 1000 ถอนมา 1000 ระบบจะตัดออก 500 อัตโนมัติ)</h1>
				<h1>ข้อ 12 เปิด/ปิด แสดงให้ลูกค้าเห็น</h1>
			  </div>
			</div>
		  </div>
		  
		  <div class="card">
			<div class="card-header" id="heading5">
			  <h5 class="mb-0">
				<button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse5" aria-expanded="false" aria-controls="collapse5">
				 5. วิธีตั้งค่า รางวัลเช็คอิน
				</button>
			  </h5>
			</div>
			<div id="collapse5" class="collapse" aria-labelledby="heading5" data-parent="#accordion">
			  <div class="card-body">
				<h1>กดไปที่เมนู ตั้งค่ารางวัลเช็คอิน</h1>
				<h1>กดแก้ไขรางวัล </h1>
				<h1>1. จำนวนรางวัล ต้องการให้เท่าไหร่ </h1>
				<h1>2. ยอดเทิร์น เป็นแบบเท่า เท่านั้น คือต้องทำยอดให้ได้ กี่เท่าถึงจะถอนได้ </h1>
				<h1>3. ถอนได้สูงสุดเท่าไหร่</h1>
				<h1>4. ต้องเติมเงินวันนี้ทั้งหมดเท่าไหร่ ถึงจะกดรับได้</h1>

			  </div>
			</div>
		  </div>
		  
		  <div class="card">
			<div class="card-header" id="heading6">
			  <h5 class="mb-0">
				<button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse6" aria-expanded="false" aria-controls="collapse6">
				 6. จัดการพนักงาน
				</button>
			  </h5>
			</div>
			<div id="collapse6" class="collapse" aria-labelledby="heading6" data-parent="#accordion">
			  <div class="card-body">
				<h1>กดไปที่เมนู จัดการข้อมูลพนักงาน</h1>
				<h1>กดแก้ไขรางวัล </h1>
				<h1>1. กดเพิ่มพนักงาน เพื่อเพิ่มพนักงาน</h1>
				<h1>2. กดแก้ไขข้อมูล เพื่อแก้ไขพนักงาน</h1>
				<h1>3. กดลบข้อมูล เพื่อลบพนักงาน</h1>
				<h1>** สิทธิ์ของพนักงาน **</h1>
				<h1>1. ไม่สามารถ ตั้งค่าบัญชีธนาคาร ได้</h1>
				<h1>2. ไม่สามารถ จัดการโปรโมชั่น ได้</h1>
				<h1>3. ไม่สามารถ ตั้งค่ารางวัลเช็คอิน ได้</h1>
				<h1>4. ไม่สามารถ จัดการข้อมูลพนักงาน ได้</h1>
				<h1>4. ไม่สามารถ จัดการข้อมูลเว็บไซต์ ได้</h1>
				<h1>** สิทธิ์ของแอดมินจะสามารถจัดการได้ทั้งหมด **</h1>

			  </div>
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
	formData.append('truewallet', $("#truewallet").val());
	formData.append('linewallet', $("#linewallet").val());
  formData.append('lineregister', $("#lineregister").val());
  formData.append('affwinloss', $("#affwinloss").val());
  formData.append('minwinloss', $("#minwinloss").val());

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