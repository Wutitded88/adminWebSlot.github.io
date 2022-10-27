<?php
require_once '../api/config.php';

//ชื่อตาราง
$table = 'user_tb';
//ชื่อคีย์หลัก
$primaryKey = 'u_id';

//ข้อมูลอะเรที่ส่งป datables
$columns = array(
  array( 'db' => 'u_user', 'dt' => 0),
  array( 'db' => 'u_fname', 'dt' => 1),
  array( 'db' => 'u_creditfree', 'dt' => 2),
  array( 'db' => 'u_vip', 'dt' => 3),
  array( 'db' => 'u_agent_id', 'dt' => 4,
         'formatter' => function($d, $row) {
          return $d;
        }
  ),
  array( 'db' => 'u_password', 'dt' => 5,
         'formatter' => function($d, $row) {
          return password_decode($d);
        }
  ),
  array( 'db' => 'u_id', 'dt' => 6,
         'formatter' => function($d, $row) {
          return "<a href='./customer/detail/".$d."' target='_blank' class='btn btn-primary'>ดูข้อมูล</a>";
        }),
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
    SSP::complex( $_GET, $sql_details, $table, $primaryKey, $columns, "order by u_id desc", "u_block_login = 1 OR u_block_agent = 1")
  );
