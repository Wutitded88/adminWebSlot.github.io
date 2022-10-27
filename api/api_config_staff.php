<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'config.php';

// ===== get permission =====
if(get_admin("a_role") > 2)
{
	echo "<SCRIPT LANGUAGE='JavaScript'>
	window.location.href = './unauthorized';
	</SCRIPT>";
	exit;
}
// ===== get permission =====
 
function dd_return($status, $message)
{
	$json = ['message' => $message];
    if($status)
    {
        http_response_code(200);
        die(json_encode($json));
    }
    else
    {
        http_response_code(400);
        die(json_encode($json));
    }
}

//////////////////////////////////////////////////////////////////////////

header('Content-Type: application/json; charset=utf-8;');

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$type = trim($_POST['type']);
	if(empty(get_session()))
	{
		dd_return(false, "กรุณาเข้าสู่ระบบก่อนทำรายการ");
	}
	else if($type == "delete")
	{
		$a_user = trim($_POST['a_user']);
		$username = trim($_POST['username']);
		$ip = trim($_POST['ip']);

		if($a_user != "" AND $username != "" AND $ip != "")
		{
			if($a_user != $username)
			{
				$q = dd_q('DELETE FROM admin_tb WHERE a_user=?', [$a_user]);
				if($q == true)
				{
					write_log("config_staff : ".$type, $username." : ลบข้อมูลพนักงาน (".$a_user.")", $ip);
					dd_return(true, "ทำรายการสำเร็จ");
				}
				else
				{
					dd_return(false, "ทำรายการไม่สำเร็จ");
				}
			}
			else
			{
				dd_return(false, "ไม่สามารถลบข้อมูลของตัวเองได้");
			}
		}
		else
		{
			dd_return(false, "กรุณากรอกข้อมูลให้ครบ");
		}
	}
	else if($type == "edit")
	{
		$hdf_username = trim($_POST['hdf_username']);
	    $txt_password = trim($_POST['txt_password']);
		$ddl_role = trim($_POST['ddl_role']);
		$ddl_status = trim($_POST['ddl_status']);
		$username = trim($_POST['username']);
		$ip = trim($_POST['ip']);

		if($hdf_username != "" AND $txt_password != "" AND $ddl_role != "" AND $ddl_status != "" AND $type != "" AND $username != "" AND $ip != "")
		{
			$pass_uppercase = preg_match('@[A-Z]@', $txt_password);
			$pass_lowercase = preg_match('@[a-z]@', $txt_password);
			$pass_number = preg_match('@[0-9]@', $txt_password);
			$pass_thai = preg_match('@[ก-๙]@', $txt_password);
			if(!$pass_uppercase || !$pass_lowercase || !$pass_number || $pass_thai || strlen($txt_password) < 8)
			{
				dd_return(false, "รหัสผ่านต้องมี ตัวอักษรตัวเล็กและตัวใหญ่ และตัวเลข 8 ตัวขึ้นไป (ตัวอย่างเช่น Aa123456)");
			}
			else
			{
				$password_hash = password_encode($txt_password);
				$q_1 = dd_q('UPDATE admin_tb SET a_password=?,a_role=?,a_status=? WHERE a_user=?', [
					$password_hash,
					$ddl_role,
					$ddl_status,
					$hdf_username
				]);
				if($q_1 = true)
				{
					write_log("config_staff : ".$type, $username." : แก้ไขข้อมูลพนักงาน (".$hdf_username.")", $ip);
					dd_return(true, "ทำรายการสำเร็จ");
				}
				else
				{
					dd_return(false, "ทำรายการไม่สำเร็จ");
				}
			}
		}
		else
		{
			dd_return(false, "กรุณากรอกข้อมูลให้ครบ");
		}
	}
	else if($type == "insertnaja")
	{
		$txt_username = trim($_POST['txt_username']);
	    $txt_password = trim($_POST['txt_password']);
		$ddl_role = trim($_POST['ddl_role']);
		$ddl_status = trim($_POST['ddl_status']);
		$username = trim($_POST['username']);
		$ip = trim($_POST['ip']);

		if($txt_username != "" AND $txt_password != "" AND $ddl_role != "" AND $ddl_status != "" AND $type != "" AND $username != "" AND $ip != "")
		{
			$pass_uppercase = preg_match('@[A-Z]@', $txt_password);
			$pass_lowercase = preg_match('@[a-z]@', $txt_password);
			$pass_number = preg_match('@[0-9]@', $txt_password);
			$pass_thai = preg_match('@[ก-๙]@', $txt_password);
			if(!$pass_uppercase || !$pass_lowercase || !$pass_number || $pass_thai || strlen($txt_password) < 8)
			{
				dd_return(false, "รหัสผ่านต้องมี ตัวอักษรตัวเล็กและตัวใหญ่ และตัวเลข 8 ตัวขึ้นไป (ตัวอย่างเช่น Aa123456)");
			}
			else
			{
				if (preg_match('/^[a-zA-Z0-9]+$/', $txt_username))
				{
					$q_1 = dd_q('SELECT * FROM admin_tb WHERE (a_user = ?)', [$txt_username]);
					if ($q_1->rowCount() < 1)
					{
						$password_hash = password_encode($txt_password);
						$q_2 = dd_q('INSERT INTO admin_tb (a_user, a_password, a_role, a_status) VALUES (?, ?, ?, ?)', [
							$txt_username,
							$password_hash,
							$ddl_role,
							$ddl_status
						]);
						if($q_2 = true)
						{
							write_log("config_staff : ".$type, $username." : เพิ่มข้อมูลพนักงาน (".$txt_username.")", $ip);
							dd_return(true, "ทำรายการสำเร็จ");
						}
						else
						{
							dd_return(false, "ทำรายการไม่สำเร็จ");
						}
					}
					else
					{
						dd_return(false, "มีชื่อผู้ใช้นี้ในระบบแล้ว");
					}
				}
				else
				{
					dd_return(false, "กรุณากรอกชื่อผู้ใช้ ภาษาอังกฤษ / ตัวเลข เท่านั้น");
				}
			}
		}
		else
		{
			dd_return(false, "กรุณากรอกข้อมูลให้ครบ");
		}
	}
}
else
{
	dd_return(false, "Method '{$_SERVER['REQUEST_METHOD']}' not allowed!");
}
?>
