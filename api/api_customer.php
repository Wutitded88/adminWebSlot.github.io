<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'config.php';
require_once 'Service/amb.php';
$api = new AMBAPI();

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
	else if($type == "edit")
	{
		$u_user = trim($_POST['u_user']);
		$u_agent_id = trim($_POST['u_agent_id']);
	    $u_password = trim($_POST['u_password']);
		$u_fname = trim($_POST['u_fname']);
		$u_refer_id = trim($_POST['u_refer_id']);
		$u_refer_name = trim($_POST['u_refer_name']);
		$u_bank_code = trim($_POST['u_bank_code']);
		$u_bank_name = trim($_POST['u_bank_name']);
		$u_bank_number = trim($_POST['u_bank_number']);
		$u_line = trim($_POST['u_line']);
		$u_limitcredit = trim($_POST['u_limitcredit']);
		$username = trim($_POST['username']);
		$ip = trim($_POST['ip']);

		if($u_user != "" AND $u_agent_id != "" AND $u_password != "" AND $u_fname != "" AND $u_refer_id != "" AND $u_refer_name != "" AND $u_bank_code != "" AND $u_bank_name != "" AND $u_bank_number != "" AND $u_line != "" AND $username != "" AND $ip != "" AND $u_limitcredit != "")
		{
			$pass_uppercase = preg_match('@[A-Z]@', $u_password);
			$pass_lowercase = preg_match('@[a-z]@', $u_password);
			$pass_number = preg_match('@[0-9]@', $u_password);
			$pass_thai = preg_match('@[ก-๙]@', $u_password);
			if(!$pass_uppercase || !$pass_lowercase || !$pass_number || $pass_thai || strlen($u_password) < 8)
			{
				dd_return(false, "รหัสผ่านต้องมี ตัวอักษรตัวเล็กและตัวใหญ่ และตัวเลข 8 ตัวขึ้นไป (ตัวอย่างเช่น Aa123456)");
			}
			else
			{
				$setpassword = $api->setPasswordUser($u_agent_id, $u_password);
				if ($setpassword->success == true)
				{
					$password_hash = password_encode($u_password);
					$q_1 = dd_q('UPDATE user_tb SET u_password=?,u_agent_id=?,u_agent_pass=?,u_fname=?,u_refer_id=?,u_refer_name=?,u_bank_code=?,u_bank_name=?,u_bank_number=?,u_line=?,u_limitcredit =? WHERE u_user=?', [
						$password_hash,
						$u_agent_id,
						$u_password,
						$u_fname,
						$u_refer_id,
						$u_refer_name,
						$u_bank_code,
						$u_bank_name,
						$u_bank_number,
						$u_line,
						$u_limitcredit,
						$u_user
					]);
					if($q_1 = true)
					{
						write_log("customer : edit", $username." : แก้ไขข้อมูลลูกค้า (".$u_user.")", $ip);
						dd_return(true, "ทำรายการสำเร็จ");
					}
					else
					{
						dd_return(false, "ทำรายการไม่สำเร็จ");
					}
				}
				else
				{
					dd_return(false, $setpassword->message);
				}
			}
		}
		else
		{
			dd_return(false, "กรุณากรอกข้อมูลให้ครบ");
		}
	}
	else if($type == "b_login")
	{
		$u_user = trim($_POST['u_user']);
		$u_block_login = trim($_POST['u_block_login']);
		$username = trim($_POST['username']);
		$ip = trim($_POST['ip']);

		if($u_user != "" AND $u_block_login != "" AND $username != "" AND $ip != "")
		{
			if($u_block_login == 0)
			{
				$block_login = 1;
			}
			else if($u_block_login == 1)
			{
				$block_login = 0;
			}
			$q_1 = dd_q('UPDATE user_tb SET u_block_login=? WHERE u_user=?', [
				$block_login,
				$u_user
			]);
			if($q_1 = true)
			{
				if($block_login == 0)
				{
					write_log("customer : block login", $username." : ปลดบล็อคการล็อคอิน (".$u_user.")", $ip);
				}
				else if($block_login == 1)
				{
					write_log("customer : block login", $username." : บล็อคการล็อคอิน (".$u_user.")", $ip);
				}
				dd_return(true, "ทำรายการสำเร็จ");
			}
			else
			{
				dd_return(false, "ทำรายการไม่สำเร็จ");
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
