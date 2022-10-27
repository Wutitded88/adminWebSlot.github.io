<?php
require_once '../api/config.php';

//ชื่อตาราง
$table = 'aff_receive';
//ชื่อคีย์หลัก
$primaryKey = 'aff_u_user';

//ข้อมูลอะเรที่ส่งป datables
//, , , , , , 
$columns = array(
  array( 'db' => 'aff_action', 'dt' => 0,
         'formatter' => function( $d, $row ) {
          return date( 'd/m/Y H:i:s', strtotime($d));
        }
  ),
  array( 'db' => 'aff_amount', 'dt' => 1),
  array( 'db' => 'aff_id', 'dt' => 2),
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
      SSP::complex( $_GET, $sql_details, $table, $primaryKey, $columns, "order by aff_id desc", "aff_u_user = '".$_GET['userid']."'" )
  );
