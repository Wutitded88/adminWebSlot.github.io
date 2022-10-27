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
	$c_reward = trim($_POST['c_reward']);
	$c_turnover = trim($_POST['c_turnover']);
	$c_turnover1 = trim($_POST['c_turnover1']);
	$c_withdraw_max = trim($_POST['c_withdraw_max']);
	$c_target_topup = trim($_POST['c_target_topup']);
	$c_status = trim($_POST['c_status']);
	$c_id = trim($_POST['c_id']);
	$username = trim($_POST['username']);
	$ip = trim($_POST['ip']);

	if(empty(get_session()))
	{
		dd_return(false, "กรุณาเข้าสู่ระบบก่อนทำรายการ");
	}
	else if($c_reward != "" AND $c_turnover != "" AND $c_turnover1 != "" AND $c_withdraw_max != "" AND $c_target_topup != "" AND $c_status != "" AND $c_id AND $username != "" AND $ip != "")
	{
		$q = dd_q('SELECT * FROM checkin_tb WHERE c_id = ?', [$c_id]);
		if ($q->rowCount() > 0)
		{
			$q_1 = dd_q('UPDATE checkin_tb SET c_reward=?, c_turnover=?, c_turnover1=?, c_withdraw_max=?, c_target_topup=?, c_status=?, c_modify_by=?, c_modify_date=? WHERE c_id=?', [
				$c_reward,
				$c_turnover,
				$c_turnover1,
				$c_withdraw_max,
				$c_target_topup,
				$c_status,
				$username, 
				date("Y-m-d H:i:s"),
				$c_id
			]);
			if($q_1 = true)
			{
				write_log("config_checkin", $username." : แก้ไขข้อมูลรางวัลเช็คอินวันที่ ".$c_id, $ip);
				dd_return(true, "ทำรายการสำเร็จ");
			}
			else
			{
				dd_return(false, "ทำรายการไม่สำเร็จ");
			}
		}
		else
		{
			dd_return(false, "ไม่พบข้อมูลรางวัล");
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
