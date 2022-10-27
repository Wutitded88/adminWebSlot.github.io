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
	else if($type == "btn_close_scb")
	{
		$username = trim($_POST['username']);
		$ip = trim($_POST['ip']);
		if($username != "" AND $ip != "")
		{
			$q_1 = dd_q('UPDATE autobank_tb SET a_bank_status=? WHERE a_bank_code=?', [
				'0',
				'scb',
			]);
			if($q_1 = true)
			{
				write_log("bank_config : ".$type, $username." : ปิดใช้งาน scb", $ip);
				dd_return(true, "ปิดใช้งาน scb สำเร็จ");
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
	else if($type == "btn_open_scb")
	{
		$username = trim($_POST['username']);
		$ip = trim($_POST['ip']);
		if($username != "" AND $ip != "")
		{
			$q_1 = dd_q('UPDATE autobank_tb SET a_bank_status=? WHERE a_bank_code=?', [
				'1',
				'scb',
			]);
			if($q_1 = true)
			{
				write_log("bank_config : ".$type, $username." : เปิดใช้งาน scb", $ip);
				dd_return(true, "เปิดใช้งาน scb สำเร็จ");
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
	else if($type == "btn_save_scb")
	{
	    $a_bank_acc_number = trim($_POST['a_bank_acc_number']);
	    $a_bank_acc_name = trim($_POST['a_bank_acc_name']);
		$username = trim($_POST['username']);
		$ip = trim($_POST['ip']);

		if($a_bank_acc_name != "" AND $a_bank_acc_number != "" AND $username != "" AND $ip != "")
		{
			$q_1 = dd_q('UPDATE autobank_tb SET a_bank_acc_number=?, a_bank_acc_name=? WHERE a_bank_code=?', [
				$a_bank_acc_number,
				$a_bank_acc_name,
				'scb',
			]);
			if($q_1 = true)
			{
				write_log("bank_config : ".$type, $username." : บันทึกข้อมูล scb", $ip);
				dd_return(true, "บันทึกข้อมูล scb สำเร็จ");
			}
			else
			{
				dd_return(false, "บันทึกข้อมูล scb ไม่สำเร็จ");
			}
		}
		else
		{
			dd_return(false, "กรุณากรอกข้อมูลให้ครบ");
		}
	}
	else if($type == "btn_close_bay")
	{
		$username = trim($_POST['username']);
		$ip = trim($_POST['ip']);
		if($username != "" AND $ip != "")
		{
			$q_1 = dd_q('UPDATE autobank_tb SET a_bank_status=? WHERE a_bank_code=?', [
				'0',
				'noauto',
			]);
			if($q_1 = true)
			{
				write_log("bank_config : ".$type, $username." : ปิดใช้งาน bay", $ip);
				dd_return(true, "ปิดใช้งาน bay สำเร็จ");
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
	else if($type == "btn_open_bay")
	{
		$username = trim($_POST['username']);
		$ip = trim($_POST['ip']);
		if($username != "" AND $ip != "")
		{
			$q_1 = dd_q('UPDATE autobank_tb SET a_bank_status=? WHERE a_bank_code=?', [
				'1',
				'noauto',
			]);
			if($q_1 = true)
			{
				write_log("bank_config : ".$type, $username." : เปิดใช้งาน bay", $ip);
				dd_return(true, "เปิดใช้งาน bay สำเร็จ");
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
	else if($type == "btn_save_bay")
	{
		$a_bank_acc_number = trim($_POST['a_bank_acc_number']);
	    $a_bank_acc_name = trim($_POST['a_bank_acc_name']);
		$a_bank_acc_name_eng = trim($_POST['a_bank_acc_name_eng']);
 
		$username = trim($_POST['username']);
		$ip = trim($_POST['ip']);

		if($a_bank_acc_name != "" AND $a_bank_acc_number != "" AND $ip != "")
		{
			$q_1 = dd_q('UPDATE autobank_tb SET a_bank_acc_number=?, a_bank_acc_name=?, a_bank_acc_name_eng=? WHERE a_bank_code=?', [
				$a_bank_acc_number,
				$a_bank_acc_name,
				$a_bank_acc_name_eng,
				'noauto',
			]);
			if($q_1 = true)
			{
				write_log("bank_config : ".$type, $username." : บันทึกข้อมูล ", $ip);
				dd_return(true, "บันทึกข้อมูล  สำเร็จ");
			}
			else
			{
				dd_return(false, "บันทึกข้อมูล  ไม่สำเร็จ");
			}
		}
		else
		{
			dd_return(false, "กรุณากรอกข้อมูลให้ครบ");
		}
	}
	else if($type == "btn_close_tmw")
	{
		$username = trim($_POST['username']);
		$ip = trim($_POST['ip']);
		if($username != "" AND $ip != "")
		{
			$q_1 = dd_q('UPDATE autobank_tb SET a_bank_status=? WHERE a_bank_code=?', [
				'0',
				'tmw',
			]);
			if($q_1 = true)
			{
				write_log("bank_config : ".$type, $username." : ปิดใช้งาน tmw", $ip);
				dd_return(true, "ปิดใช้งาน tmw สำเร็จ");
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
	else if($type == "btn_open_tmw")
	{
		$username = trim($_POST['username']);
		$ip = trim($_POST['ip']);
		if($username != "" AND $ip != "")
		{
			$q_1 = dd_q('UPDATE autobank_tb SET a_bank_status=? WHERE a_bank_code=?', [
				'1',
				'tmw',
			]);
			if($q_1 = true)
			{
				write_log("bank_config : ".$type, $username." : เปิดใช้งาน tmw", $ip);
				dd_return(true, "เปิดใช้งาน tmw สำเร็จ");
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
	else if($type == "btn_save_tmw")
	{
		$a_bank_acc_number = trim($_POST['a_bank_acc_number']);
	    $a_bank_acc_name = trim($_POST['a_bank_acc_name']);
		$a_bank_username = trim($_POST['a_bank_username']);
		$a_bank_password = trim($_POST['a_bank_password']);
		$username = trim($_POST['username']);
		$ip = trim($_POST['ip']);

		if($a_bank_acc_name != "" AND $a_bank_acc_number != "" AND $a_bank_username != "" AND $a_bank_password != "" AND $username != "" AND $ip != "")
		{
			$q_1 = dd_q('UPDATE autobank_tb SET a_bank_acc_number=?, a_bank_acc_name=?, a_bank_username=?, a_bank_password=? WHERE a_bank_code=?', [
				$a_bank_acc_number,
				$a_bank_acc_name,
				$a_bank_username,
				$a_bank_password,
				'tmw',
			]);
			if($q_1 = true)
			{
				write_log("bank_config : ".$type, $username." : บันทึกข้อมูล tmw", $ip);
				dd_return(true, "บันทึกข้อมูล tmw สำเร็จ");
			}
			else
			{
				dd_return(false, "บันทึกข้อมูล tmw ไม่สำเร็จ");
			}
		}
		else
		{
			dd_return(false, "กรุณากรอกข้อมูลให้ครบ");
		}
	}
	else if($type == "btn_close_kbank")
	{
		$username = trim($_POST['username']);
		$ip = trim($_POST['ip']);
		if($username != "" AND $ip != "")
		{
			$q_1 = dd_q('UPDATE autobank_tb SET a_bank_status=? WHERE a_bank_code=?', [
				'0',
				'kbank',
			]);
			if($q_1 = true)
			{
				write_log("bank_config : ".$type, $username." : ปิดใช้งาน kbank", $ip);
				dd_return(true, "ปิดใช้งาน kbank สำเร็จ");
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
	else if($type == "btn_open_kbank")
	{
		$username = trim($_POST['username']);
		$ip = trim($_POST['ip']);
		if($username != "" AND $ip != "")
		{
			$q_1 = dd_q('UPDATE autobank_tb SET a_bank_status=? WHERE a_bank_code=?', [
				'1',
				'kbank',
			]);
			if($q_1 = true)
			{
				write_log("bank_config : ".$type, $username." : เปิดใช้งาน kbank", $ip);
				dd_return(true, "เปิดใช้งาน kbank สำเร็จ");
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
	else if($type == "btn_save_kbank")
	{
	    $a_bank_acc_number = trim($_POST['a_bank_acc_number']);
	    $a_bank_acc_name = trim($_POST['a_bank_acc_name']);
		$a_bank_username = trim($_POST['a_bank_username']);
		$a_bank_password = trim($_POST['a_bank_password']);
		$username = trim($_POST['username']);
		$ip = trim($_POST['ip']);

		if($a_bank_acc_name != "" AND $a_bank_acc_number != "" AND $a_bank_username != "" AND $a_bank_password != "" AND $username != "" AND $ip != "")
		{
			$q_1 = dd_q('UPDATE autobank_tb SET a_bank_acc_number=?, a_bank_acc_name=?, a_bank_username=?, a_bank_password=? WHERE a_bank_code=?', [
				$a_bank_acc_number,
				$a_bank_acc_name,
				$a_bank_username,
				$a_bank_password,
				'kbank',
			]);
			if($q_1 = true)
			{
				write_log("bank_config : ".$type, $username." : บันทึกข้อมูล kbank", $ip);
				dd_return(true, "บันทึกข้อมูล kbank สำเร็จ");
			}
			else
			{
				dd_return(false, "บันทึกข้อมูล kbank ไม่สำเร็จ");
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
