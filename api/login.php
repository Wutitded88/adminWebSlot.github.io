<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'config.php';

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
	$username = trim($_POST['username']);
    $password = trim($_POST['password']);
	$ip = trim($_POST['ip']);
	
	if($username != "" AND $password != "" AND $ip != "")
	{
	/* 	if (preg_match('/^[a-zA-Z0-9]+$/', $username) AND preg_match('/^[a-zA-Z0-9@.]+$/', $password))
		{ */
			$q_1 = dd_q('SELECT * FROM admin_tb WHERE (a_user = ?)', [$username]);
			if ($q_1->rowCount() >= 1)
			{
				
				
				$row = $q_1->fetch(PDO::FETCH_ASSOC);
				/* dd_return(false,array('p'=>$row['a_password']) ); */
				if ($row['a_password'] == $password)
				{
					if($row['a_status'] == '1')
					{
						$_SESSION['a_user'] = $row['a_user'];
						write_log("login", $username." : เข้าสู่ระบบสำเร็จ", $ip);
						$q_2 = dd_q('UPDATE admin_tb SET a_last_login=? WHERE a_user=?', [
							date("Y-m-d H:i:s"),
							$username
						]);
						dd_return(true, "เข้าสู่ระบบสำเร็จ");
					}
					else
					{
						write_log("login", $username." : ผู้ใช้งานที่ถูกปิดการใช้งาน พยายามเข้าสู่ระบบ", $ip);
						dd_return(false, "ไม่พบข้อมูล");
					}
				}
				else
				{
					write_log("login", $username." : รหัสผ่านไม่ถูกต้อง", $ip);
					dd_return(false, "รหัสผ่านไม่ถูกต้อง");
				}
			}
			else
			{
				write_log("login", $username." : ไม่พบข้อมูล", $ip);
				dd_return(false, "ไม่พบข้อมูล");
			}
		/* }
		else
		{
			dd_return(false, "กรุณากรอก ภาษาอังกฤษ / ตัวเลข เท่านั้น");
		} */
	}
	else
	{
		dd_return(false, "กรุณากรอกข้อมูลให้ครบ");
	}
}
else
{
	dd_return(false, "Method '{$_SERVER['REQUEST_METHOD']}' not allowed!");
}
?>
