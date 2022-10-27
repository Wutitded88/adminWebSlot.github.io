<?php
require_once '../api/config.php';

//ชื่อตาราง
$table = 'log_tb';
//ชื่อคีย์หลัก
$primaryKey = 'id';

//ข้อมูลอะเรที่ส่งป datables
$columns = array(
  array( 'db' => 'l_page', 'dt' => 0,
        /* 'formatter' => function($d, $row) {
          return "<a href='./customer/detail/".$row[0]."' target='_blank'>".$d."</a>";
         }*/
  ),
  array( 'db' => 'l_detail', 'dt' => 1),
  array( 'db' => 'l_create_date', 'dt' => 2),
  array( 'db' => 'l_ip', 'dt' => 3),
  array( 'db' => 'l_create_by', 'dt' => 4),
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
      SSP::complex( $_GET, $sql_details, $table, $primaryKey, $columns, "order by id desc", "id != ''" )
  );
