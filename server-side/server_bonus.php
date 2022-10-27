<?php
require_once '../api/config.php';

//ชื่อตาราง
$table = 'transfergame_tb';
//ชื่อคีย์หลัก
$primaryKey = 't_id';

//ข้อมูลอะเรที่ส่งป datables
$columns = array(
  array( 'db' => 't_user', 'dt' => 0,
         'formatter' => function( $d, $row ) {
          return "<a href='./customer/detail/".$row[7]."' target='_blank'>".$d."</a>";
         }
  ),
  array( 'db' => 't_fname', 'dt' => 1),
  array( 'db' => 't_bonus', 'dt' => 2),
  array( 'db' => 't_promotion_title','dt' => 3),
  array( 'db' => 't_after_wallet', 'dt' => 4),
  array( 'db' => 't_date_create', 'dt' => 5,
         'formatter' => function( $d, $row ) {
          return date( 'd/m/Y', strtotime($d));
         }
  ),
  array( 'db' => 't_time_create', 'dt' => 6),
  array( 'db' => 't_u_id', 'dt' => 7),
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
      SSP::complex( $_GET, $sql_details, $table, $primaryKey, $columns, "order by t_id desc", "t_date_create >= '".$_GET['startdate']."' AND t_date_create <= '".$_GET['enddate']."'" )
  );
