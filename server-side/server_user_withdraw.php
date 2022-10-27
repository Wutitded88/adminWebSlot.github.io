<?php
require_once '../api/config.php';

//ชื่อตาราง
$table = 'withdraw_tb';
//ชื่อคีย์หลัก
$primaryKey = 'w_id';

//ข้อมูลอะเรที่ส่งป datables
//, , , , , w_action_by
$columns = array(
  array( 'db' => 'w_date_create', 'dt' => 0,
         'formatter' => function( $d, $row ) {
          return date( 'd/m/Y', strtotime($d));
        }
  ),
  array( 'db' => 'w_time_create', 'dt' => 1),
  array( 'db' => 'w_amount', 'dt' => 2),
  array( 'db' => 'w_refund', 'dt' => 3),
  array( 'db' => 'w_status', 'dt' => 4,
         'formatter' => function($d, $row) {
          if($d == "0")
          {
            return "<span class='text-warning'>รอโอนเงิน</span>";
          }
          else if($d == "1")
          {
            return "<span class='text-success'>โอนเงินสำเร็จ</span>";
          }
          else if($d == "2")
          {
            return "<span class='text-danger'>ยกเลิกรายการ</span>";
          }
          else if($d == "3")
          {
            return "<span class='text-primary'>คืนเครดิต</span>";
          }
          else if($d == "0")
          {
            return "<span class='text-warning'>รอแอดมินตรวจสอบ</span>";
          }
        }
  ),
  array( 'db' => 'w_type', 'dt' => 5,
         'formatter' => function($d, $row) {
          if($d == "1")
          {
            return "<span class='text-success'>ระบบ</span>";
          }
          else if($d == "2")
          {
            return "<span class='text-success'>พนักงาน</span>";
          }
        }
  ),
  array( 'db' => 'w_action_by', 'dt' => 6),
  array( 'db' => 'w_id', 'dt' => 7),
);

  //เชื่อต่อฐานข้อมูล
  $sql_details = array(
    'user' => DB::$str_username,
    'pass' => DB::$str_password,
    'db'   => DB::$str_database,
    'host' => DB::$str_hosting
  );

  // เรียกใช้ไฟล์ spp.class.php
  require( 'ssp.class.php' );

  //ส่งข้อมูลกลับไปเป็น JSON
  echo json_encode(
      SSP::complex( $_GET, $sql_details, $table, $primaryKey, $columns, "order by w_id desc", "w_user = '".$_GET['userid']."'" )
  );
