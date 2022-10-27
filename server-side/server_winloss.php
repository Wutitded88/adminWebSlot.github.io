<?php
require_once '../api/config.php';

//ชื่อตาราง
$table = 'winloss_receive_tb';
//ชื่อคีย์หลัก
$primaryKey = 'wl_id';

//ข้อมูลอะเรที่ส่งป datables
$columns = array(
  array( 'db' => 'wl_user', 'dt' => 0,
         'formatter' => function($d, $row) {
          return "<a href='./customer/detail/".$row[6]."' target='_blank'>".$d."</a>";
         }
  ),
  array( 'db' => 'wl_fname', 'dt' => 1),
  array( 'db' => 'wl_date', 'dt' => 2),
  array( 'db' => 'wl_cashback', 'dt' => 3),
  array( 'db' => 'wl_action_date', 'dt' => 4),
  array( 'db' => 'wl_action_time', 'dt' => 5),
  array( 'db' => 'wl_u_id', 'dt' => 6),
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
      SSP::complex( $_GET, $sql_details, $table, $primaryKey, $columns, "order by wl_id desc", "wl_action_date >= '".$_GET['startdate']."' AND wl_action_date <= '".$_GET['enddate']."'" )
  );
