<?php
require_once '../api/config.php';

//ชื่อตาราง
$table = 'user_tb';
//ชื่อคีย์หลัก
$primaryKey = 'u_id';

//ข้อมูลอะเรที่ส่งป datables
$columns = array(
  array( 'db' => 'u_user', 'dt' => 0,
         'formatter' => function($d, $row) {
          return "<a href='./customer/detail/".$row[3]."' target='_blank'>".$d."</a>";
         }
  ),
  array( 'db' => 'u_refer_name', 'dt' => 1),
  array( 'db' => 'u_aff', 'dt' => 2),
  array( 'db' => 'u_time_create', 'dt' => 3),
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
      SSP::complex( $_GET, $sql_details, $table, $primaryKey, $columns, "order by u_id desc", "u_date_create >= '".$_GET['startdate']."' AND u_date_create <= '".$_GET['enddate']."'" )
  );
