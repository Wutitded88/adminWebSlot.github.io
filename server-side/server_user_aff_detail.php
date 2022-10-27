<?php
require_once '../api/config.php';

//ชื่อตาราง
$table = 'aff_percent_tb';
//ชื่อคีย์หลัก
$primaryKey = 'aff_id';

//ข้อมูลอะเรที่ส่งป datables
$columns = array(
  array( 'db' => 'aff_user', 'dt' => 0,
    'formatter' => function($d, $row) {
      $q_user_tb = dd_q('SELECT * FROM user_tb WHERE u_user = ?', [$d]);
      if ($q_user_tb->rowCount() > 0)
      {
        $row = $q_user_tb->fetch(PDO::FETCH_ASSOC);
        return "<a href='".base_url()."/customer/detail/".$row['u_id']."' target='_blank'>".$d."</a>";
      }
      else
      {
        return $d;
      }
    }
  ),
  array( 'db' => 'aff_user', 'dt' => 1,
    'formatter' => function($d, $row) {
      $q_user_tb = dd_q('SELECT * FROM user_tb WHERE u_user = ?', [$d]);
      if ($q_user_tb->rowCount() > 0)
      {
        $row = $q_user_tb->fetch(PDO::FETCH_ASSOC);
        return $row['u_fname']." ".$row['u_lname'];
      }
      else
      {
        return $d;
      }
    }
  ),
  array( 'db' => 'aff_amount', 'dt' => 2),
  array( 'db' => 'aff_status', 'dt' => 3,
    'formatter' => function($d, $row) {
      if($d == "0")
      {
        return "<span class='text-danger'>ยังไม่กดรับ</span>";
      }
      else if($d == "1")
      {
        return "<span class='text-success'>กดรับแล้ว</span>";
      }
      else
      {
        return "<span class='text-danger'>เกิดข้อผิดพลาด</span>";
      }
    }
  ),
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
      SSP::complex( $_GET, $sql_details, $table, $primaryKey, $columns, "order by aff_status asc, aff_id desc", "aff_u_user_ref = '".$_GET['userid']."'" )
  );
