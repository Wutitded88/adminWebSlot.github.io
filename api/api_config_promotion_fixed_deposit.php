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
	$type = trim($_POST['type']);
	if(empty(get_session()))
	{
		dd_return(false, "กรุณาเข้าสู่ระบบก่อนทำรายการ");
	}
	else if($type == "delete")
	{
		$p_id = trim($_POST['p_id']);
		$username = trim($_POST['username']);
		$ip = trim($_POST['ip']);

		if($p_id != "" AND $username != "" AND $ip != "")
		{
			$q = dd_q('DELETE FROM promotion_fixed_deposit_tb WHERE p_id=?', [$p_id]);
			if($q == true)
			{
				write_log("promotion_fixed_deposit : ".$type, $username." : ลบข้อมูลโปรโมชั่น (".$p_id.")", $ip);
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
	else if($type == "edit")
	{
		$p_img = trim($_POST['p_img']);
		$p_title = trim($_POST['p_title']);
	    $p_detail = trim($_POST['p_detail']);
	    $p_type = trim($_POST['p_type']);
	    $p_reward = trim($_POST['p_reward']);
	    $p_transfer_min = trim($_POST['p_transfer_min']);
	    $p_deposit_day = trim($_POST['p_deposit_day']);
	    $p_turnover = trim($_POST['p_turnover']);
	    $p_turnover1 = trim($_POST['p_turnover1']);
	    $p_turnover_type = trim($_POST['p_turnover_type']);
	    $p_withdraw_max = trim($_POST['p_withdraw_max']);
	    $p_status = trim($_POST['p_status']);
	    $hdf_p_id = trim($_POST['hdf_p_id']);
		$username = trim($_POST['username']);
		$ip = trim($_POST['ip']);

		if($p_title != "" AND $p_detail != "" AND $p_type != "" AND $p_reward != "" AND $p_transfer_min != "" AND $p_deposit_day != "" AND $p_turnover != "" AND $p_turnover1 != "" AND $p_turnover_type != "" AND $p_withdraw_max != "" AND $p_status != "" AND $hdf_p_id != "" AND $username != "" AND $ip != "")
		{
			$q = dd_q('SELECT * FROM promotion_fixed_deposit_tb WHERE (p_id = ?)', [$hdf_p_id]);
			if ($q->rowCount() > 0)
			{
				$q_1 = dd_q('UPDATE promotion_fixed_deposit_tb SET p_title=?, p_img=?, p_detail=?, p_type=?, p_reward=?, p_transfer_min=?, p_deposit_day=?, p_turnover=?, p_turnover1=?, p_turnover_type=?, p_withdraw_max=?, p_status=?, p_modify_by=?, p_modify_date=? WHERE p_id=?', [
					$p_title, 
					$p_img, 
					$p_detail, 
					$p_type, 
					$p_reward, 
					$p_transfer_min, 
					$p_deposit_day, 
					$p_turnover, 
					$p_turnover1, 
					$p_turnover_type, 
					$p_withdraw_max, 
					$p_status, 
					$username, 
					date("Y-m-d H:i:s"),
					$hdf_p_id
				]);
				if($q_1 = true)
				{
					write_log("promotion_fixed_deposit : ".$type, $username." : แก้ไขข้อมูลโปรโมชั่น (".$p_title.")", $ip);
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
	else if($type == "add")
	{
		$p_img = trim($_POST['p_img']);
		$p_title = trim($_POST['p_title']);
	    $p_detail = trim($_POST['p_detail']);
	    $p_type = trim($_POST['p_type']);
	    $p_reward = trim($_POST['p_reward']);
	    $p_transfer_min = trim($_POST['p_transfer_min']);
	    $p_deposit_day = trim($_POST['p_deposit_day']);
	    $p_turnover = trim($_POST['p_turnover']);
	    $p_turnover1 = trim($_POST['p_turnover1']);
	    $p_turnover_type = trim($_POST['p_turnover_type']);
	    $p_withdraw_max = trim($_POST['p_withdraw_max']);
	    $p_status = trim($_POST['p_status']);
	    $hdf_p_id = trim($_POST['hdf_p_id']);
		$username = trim($_POST['username']);
		$ip = trim($_POST['ip']);

		if($p_title != "" AND $p_detail != "" AND $p_type != "" AND $p_reward != "" AND $p_transfer_min != "" AND $p_deposit_day != "" AND $p_turnover != "" AND $p_turnover1 != "" AND $p_turnover_type != "" AND $p_withdraw_max != "" AND $p_status != "" AND $username != "" AND $ip != "")
		{
			$q_1 = dd_q('INSERT INTO promotion_fixed_deposit_tb (p_title, p_img, p_detail, p_type, p_reward, p_transfer_min, p_deposit_day, p_turnover, p_turnover1, p_turnover_type, p_withdraw_max, p_status, p_create_by, p_create_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
					$p_title, 
					$p_img,
					$p_detail, 
					$p_type, 
					$p_reward, 
					$p_transfer_min, 
					$p_deposit_day, 
					$p_turnover, 
					$p_turnover1, 
					$p_turnover_type, 
					$p_withdraw_max, 
					$p_status, 
					$username, 
					date("Y-m-d H:i:s")
				]);
			if($q_1 = true)
			{
				write_log("promotion_fixed_deposit : ".$type, $username." : เพิ่มข้อมูลโปรโมชั่น (".$p_title.")", $ip);
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
