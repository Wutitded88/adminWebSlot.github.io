<?php
require_once '../api/config.php';

//ชื่อตาราง
$table = 'withdraw_auto_tb';
//ชื่อคีย์หลัก
$primaryKey = 'w_id';

//ข้อมูลอะเรที่ส่งป datables
$columns = array(
  array( 'db' => 'w_u_user', 'dt' => 0,
         'formatter' => function( $d, $row ) {
          return "<a href='./customer/detail/".$row[3]."' target='_blank'>".$d."</a>";
         }
  ),
  array( 'db' => 'w_u_id', 'dt' => 1,
        'formatter' => function($d, $row) {
          $q_1 = dd_q('SELECT * FROM user_tb WHERE u_id = ? LIMIT 1', [$d]);
          if ($q_1->rowCount() > 0)
          {
            $row_1 = $q_1->fetch(PDO::FETCH_ASSOC);
            return $row_1['u_fname']." ".$row_1['u_lname'];
          }
          else
          {
            return "-";
          }
        }
  ),
  array( 'db' => 'w_id', 'dt' => 2,
        'formatter' => function($d, $row) {
          return "<button type='button' onclick='onDelete(".$d.")' class='btn btn-danger'><i class='fas fa-trash-alt'></i></button>";
        }
  ),
  array( 'db' => 'w_u_id', 'dt' => 3)
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
      SSP::complex( $_GET, $sql_details, $table, $primaryKey, $columns, "order by w_id desc", "w_id != ''" )
  );
