<?php
require_once '../api/config.php';

//ชื่อตาราง
$table = 'withdraw_tb';
//ชื่อคีย์หลัก
$primaryKey = 'w_id';

//ข้อมูลอะเรที่ส่งป datables
$columns = array(
  array( 'db' => 'w_user', 'dt' => 0,
         'formatter' => function( $d, $row ) {
          return "<a href='./customer/detail/".$row[9]."' target='_blank'>".$d."</a>";
         }
  ),
  array( 'db' => 'w_fname', 'dt' => 1),
  array( 'db' => 'w_amount', 'dt' => 2),
  array( 'db' => 'w_bank_name', 'dt' => 3),
  array( 'db' => 'w_action_by', 'dt' => 4),
  array( 'db' => 'w_date_create', 'dt' => 5,
         'formatter' => function($d, $row) {
          return date( 'd/m/Y', strtotime($d));
         }
  ),
  array( 'db' => 'w_time_create', 'dt' => 6),
  array( 'db' => 'w_status', 'dt' => 7,
        'formatter' => function($d, $row) {
          if($d == "0")
          {
            return "<a href='./withdraw/detail/".$row[8]."' target='_blank'><span class='text-warning'><u>รอโอนเงิน</u></span></a>";
          }
          else if($d == "1")
          {
            return "<a href='./withdraw/detail/".$row[8]."' target='_blank'><span class='text-success'><u>โอนเงินสำเร็จ</u></span></a>";
          }
          else if($d == "2")
          {
            return "<a href='./withdraw/detail/".$row[8]."' target='_blank'><span class='text-danger'><u>ยกเลิกรายการ</u></span></a>";
          }
          else if($d == "3")
          {
            return "<a href='./withdraw/detail/".$row[8]."' target='_blank'><span class='text-primary'><u>คืนเครดิต</u></span></a>";
          }
          else if($d == "99")
          {
            return "<a href='./withdraw/detail/".$row[8]."' target='_blank'><span class='text-warning'><u>รอแอดมินตรวจสอบ</u></span></a>";
          }
        }
  ),
  array( 'db' => 'w_id', 'dt' => 8),
  array( 'db' => 'w_u_id', 'dt' => 9),
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
      SSP::complex( $_GET, $sql_details, $table, $primaryKey, $columns, "order by w_id desc", "w_date_create >= '".$_GET['startdate']."' AND w_date_create <= '".$_GET['enddate']."'" )
  );
