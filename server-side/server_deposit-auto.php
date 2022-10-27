<?php
require_once '../api/config.php';

//ชื่อตาราง
$table = 'topup_db';
//ชื่อคีย์หลัก
$primaryKey = 't_id';

//ข้อมูลอะเรที่ส่งป datables
$columns = array(
  array( 'db' => 't_user', 'dt' => 0,
         'formatter' => function( $d, $row ) {
          return "<a href='./customer/detail/".$row[9]."' target='_blank'>".$d."</a>";
         }
  ),
  array( 'db' => 't_fname', 'dt' => 1),
  array( 'db' => 't_amount', 'dt' => 2),
  array( 'db' => 't_sys_bank_name', 'dt' => 3,
         'formatter' => function( $d, $row ) {
          if($row[10] == "scb")
          {
            return "<span style='color: #4e2e7f !important;'>".$d." (".$row[11].")</span>";
          }
          else if($row[10] == "bay")
          {
            return "<span style='color: #fec43b !important;'>".$d." (".$row[11].")</span>";
          }
          else if($row[10] == "tmw")
          {
            return "<span style='color: #FF9800 !important;'>".$d." (".$row[11].")</span>";
          }
         }
   ),
  array( 'db' => 't_type', 'dt' => 4,
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
  array( 'db' => 't_system_create_date', 'dt' => 5,
         'formatter' => function( $d, $row ) {
          return date( 'd/m/Y', strtotime($d));
         }
  ),
  array( 'db' => 't_system_create_time', 'dt' => 6),
  array( 'db' => 't_status', 'dt' => 7,
        'formatter' => function( $d, $row ) {
          if($d == "0")
          {
            return "<a href='./deposit/".$row[8]."' target='_blank'><span class='text-warning'><u>รอทำรายการ</u></span></a>";
          }
          else if($d == "1")
          {
            return "<a href='./deposit/".$row[8]."' target='_blank'><span class='text-success'><u>ทำรายการสำเร็จ</u></span></a>";
          }
          else if($d == "2")
          {
            return "<a href='./deposit/".$row[8]."' target='_blank'><span class='text-danger'><u>ทำรายการผิดพลาด</u></span></a>";
          }
          else if($d == "3")
          {
            return "<a href='./deposit/".$row[8]."' target='_blank'><span class='text-danger'><u>ยกเลิก</u></span></a>";
          }
        }
  ),
  array( 'db' => 't_id', 'dt' => 8,
         'formatter' => function( $d, $row ) {
          if($row[4] == "2")
          {
            if($row['t_status'] == "1")
            {
              return "<button type='button' onclick='onCancel(".$d.")' class='btn btn-danger'><i class='fas fa-trash-alt'></i></button>";
            }
          }
        }
  ),
  array( 'db' => 't_u_id', 'dt' => 9),
  array( 'db' => 't_sys_bank_code', 'dt' => 10),
  array( 'db' => 't_sys_bank_number', 'dt' => 11),
  array( 'db' => 't_status', 'dt' => 12)
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
  //$request, $conn, $table, $primaryKey, $columns, $whereResult=null, $whereAll=null
  echo json_encode(
      SSP::complex( $_GET, $sql_details, $table, $primaryKey, $columns, "order by t_id desc", "t_type = '1' AND t_date_create >= '".$_GET['startdate']."' AND t_date_create <= '".$_GET['enddate']."'" )
  );
