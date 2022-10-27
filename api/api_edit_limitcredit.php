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
	$all_limitcredit = trim($_POST['all_limitcredita']);
	$username = trim($_POST['username']);
	$ip = trim($_POST['ip']);
	if(empty(get_session()))
	{
		dd_return(false, "กรุณาเข้าสู่ระบบก่อนทำรายการ");
	} else if($all_limitcredit != "") {
		$q_1 = dd_q('UPDATE user_tb SET u_limitcredit = ?', [$all_limitcredit]);
		if($q_1 = true)
		{
			write_log("Setting : edit", $username." ปรับยอดถอนสูงสุดทั้งหมด ".$all_limitcredit, $ip);
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
	dd_return(false, "Method '{$_SERVER['REQUEST_METHOD']}' not allowed!");
}
?>
