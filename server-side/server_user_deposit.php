<?php
require_once '../api/config.php';

//ชื่อตาราง
$table = 'topup_db';
//ชื่อคีย์หลัก
$primaryKey = 't_id';

//ข้อมูลอะเรที่ส่งป datables
$columns = array(
  array( 'db' => 't_system_create_date', 'dt' => 0,
         'formatter' => function( $d, $row ) {
          return date( 'd/m/Y', strtotime($d));
        }
  ),
  array( 'db' => 't_system_create_time', 'dt' => 1),
  array( 'db' => 't_amount', 'dt' => 2),
  array( 'db' => 't_before_wallet', 'dt' => 3),
  array( 'db' => 't_after_wallet', 'dt' => 4),
  array( 'db' => 't_status', 'dt' => 5,
        'formatter' => function( $d, $row ) {
          if($d == "0")
          {
            return "<span class='text-warning'>รอทำรายการ</span>";
          }
          else if($d == "1")
          {
            return "<span class='text-success'>ทำรายการสำเร็จ</span>";
          }
          else if($d == "2")
          {
            return "<span class='text-danger'>ทำรายการผิดพลาด</span>";
          }
          else if($d == "3")
          {
            return "<span class='text-danger'>ยกเลิก</span>";
          }
        }
  ),
  array( 'db' => 't_type', 'dt' => 6,
         'formatter' => function( $d, $row ) {
          if($d == "1")
          {
            return "<span class='text-success'>ระบบ</span>";
          }
          else if($d == "2")
          {
            return "<span class='text-danger'>พนักงาน</span>";
          }
        }
  ),
  array( 'db' => 't_id', 'dt' => 7)
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
      SSP::complex( $_GET, $sql_details, $table, $primaryKey, $columns, "order by t_id desc", "t_user = '".$_GET['userid']."'" )
  );
