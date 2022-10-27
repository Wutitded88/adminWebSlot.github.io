<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'config.php';
if(get_admin("a_role") > 2)
{
	echo "<SCRIPT LANGUAGE='JavaScript'>
	window.location.href = './unauthorized';
	</SCRIPT>";
	exit;
}
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
	$g_phone = trim($_POST['g_phone']);
	$username = trim($_POST['username']);
	$ip = trim($_POST['ip']);

	if(empty(get_session()))
	{
		dd_return(false, "กรุณาเข้าสู่ระบบก่อนทำรายการ");
	}
	else if($g_phone != "" AND $username != "" AND $ip != "")
	{
		$q = dd_q('SELECT * FROM user_tb WHERE u_user = ?', [$g_phone]);
		if($q->rowCount() > 0)
		{
			$q_1 = dd_q('SELECT * FROM gencode_tb WHERE g_phone = ?', [$g_phone]);
			if ($q_1->rowCount() == 0)
			{
				$loop_stop = false;
				while (!$loop_stop)
				{
					$code = generateRandomString(8);
					$q_2 = dd_q('SELECT * FROM gencode_tb WHERE g_code = ?', [$code]);
					if ($q_2->rowCount() == 0)
					{
						$loop_stop = true;
						$sms = new thsms();
						$result = $sms->send($g_phone, "Free credit code : (".$code.")");
						if($result == "success")
						{
							$q_3 = dd_q('INSERT INTO gencode_tb (g_code, g_phone, g_use, g_create_by, g_create_date, g_date_create, g_time_create) VALUES (?, ?, ?, ?, ?, ?, ?)', [
								$code, 
								$g_phone, 
								'0', 
								$username, 
								date("Y-m-d H:i:s"),
								date("Y-m-d"),
								date("H:i:s")
							]);
							if($q_3 = true)
							{
								write_log("freecredit : ", $username." : แจกเครดิตฟรี code(".$code.") / phone(".$g_phone.")", $ip);
								dd_return(true, "ทำรายการสำเร็จ");
							}
							else
							{
								dd_return(false, "ทำรายการไม่สำเร็จ");
							}
						}
						else
						{
							dd_return(false, $result);
						}
					}
				}
			}
			else
			{
				dd_return(false, "หมายเลขโทรศัพท์นี้ เคยรับเครดิตฟรีแล้ว");
			}
		}
		else
		{
			dd_return(false, "ไม่มีข้อมูลเบอร์โทรศัพท์นี้ในระบบ");
		}	
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
