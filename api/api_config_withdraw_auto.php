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
	$w_id = trim($_POST['w_id']);
	if(empty(get_session()))
	{
		dd_return(false, "กรุณาเข้าสู่ระบบก่อนทำรายการ");
	}
	else if($w_id != "" )
	{
		$q = dd_q('DELETE FROM withdraw_auto_tb WHERE w_id=?', [$w_id]);
		if($q == true)
		{
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
else
{
	dd_return(false, "Method '{$_SERVER['REQUEST_METHOD']}' not allowed!");
}
?>
