<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'config.php';
require_once 'Service/amb.php';
$api = new AMBAPI();
require_once 'api_scb_withdraw.php';
$apiSCB = new SCBAPI();

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
	if($type == "approve_auto")
	{
		$w_id = trim($_POST['w_id']);
		$wcode = trim($_POST['wcode']);
		$username = trim($_POST['username']);
		$ip = trim($_POST['ip']);

		if($wcode != "" AND $w_id != "" AND $username != "" AND $ip != "")
		{
			$q_1 = dd_q('SELECT * FROM withdraw_tb WHERE w_id = ? AND (w_status = ? OR w_status = ?)', [$w_id, '0', '99']);
			if ($q_1->rowCount() > 0)
			{
				$row = $q_1->fetch(PDO::FETCH_ASSOC);

				if($wcode == $row['w_transaction_id'])
				{
					$value = trim($row['w_bank_name']);
					$bankcode = "";
					if ($value == "ธนาคารไทยพาณิชย์")
					{
						$bankcode = "014";
					}
					else if ($value == "ธนาคารกสิกรไทย")
					{
						$bankcode = "004";
					}
					else if ($value == "ธนาคารกรุงไทย")
					{
						$bankcode = "006";
					}
					else if ($value == "ธนาคารกรุงเทพ")
					{
						$bankcode = "002";
					}
					else if ($value == "ธนาคารทหารไทยธนชาต")
					{
						$bankcode = "011";
					}
					else if ($value == "ธนาคารออมสิน")
					{
						$bankcode = "030";
					}
					else if ($value == "ธนาคารกรุงศรีอยุธยา")
					{
						$bankcode = "025";
					}
					else if ($value == "ธนาคารเพื่อการเกษตรและสหกรณ์การเกษตร")
					{
						$bankcode = "034";
					}
					else if ($value == "ธนาคารยูโอบี")
					{
						$bankcode = "024";
					}
					else if ($value == "ธนาคารอาคารสงเคราะห์")
					{
						$bankcode = "033";
					}
					else if ($value == "ธนาคารซีไอเอ็มบี")
					{
						$bankcode = "022";
					}
					else if ($value == "ธนาคารซิตี้แบงค์")
					{
						$bankcode = "017";
					}
					else if ($value == "ธนาคารเกียรตินาคิน")
					{
						$bankcode = "069";
					}
					else if ($value == "ธนาคารแลนด์ แอนด์ เฮ้าส์")
					{
						$bankcode = "073";
					}
					else if ($value == "ธนาคารทิสโก้")
					{
						$bankcode = "067";
					}
					else if ($value == "ธนาคารอิสลามแห่งประเทศไทย")
					{
						$bankcode = "066";
					}

					if($bankcode != "")
				    {
				    	$q_1 = dd_q('SELECT * FROM autobank_tb WHERE a_bank_code = ? AND a_bank_status = ?', ['scb', 1]);
				    	$row_scb_w = $q_1->fetch(PDO::FETCH_ASSOC);
				    	if($row_scb_w['a_bank_acc_number'] != "" && $row_scb_w['a_bank_username'] != "" && $row_scb_w['a_bank_password'] != "")
				    	{
				    		$data_balance = $apiSCB->balance($row_scb_w['a_bank_username']);
				    		if ($data_balance->success == true)
				    		{
				    			$limit = 1000000;
		                    	if($row['w_amount'] <= $limit)
		                    	{
		                    		if($data_balance->balance >= $row['w_amount'])
		                    		{
		                    			$data_name = $apiSCB->getname($row_scb_w['a_bank_username'], trim($row['w_bank_number']), $bankcode);
		                    			if ($data_name->success == true)
		                    			{
		                    				if(strpos(trim($data_name->name), trim($row['w_fname'])) !== true)
		                    				{
		                    					$withdraw = $apiSCB->withdraw($row_scb_w['a_bank_username'], $row['w_bank_number'], $bankcode, $row['w_amount']);
		                    					if ($withdraw->success == true)
		                    					{
		                    						$q_2 = dd_q('UPDATE withdraw_tb SET w_status=?, w_action_by=? WHERE w_id=?', [
														'1',
														$username,
														$w_id
													]);
													if($q_2 = true)
													{
														write_log("withdraw : approve_auto", $username." : อนุมัติรายการถอน (รหัสรายการ ".$w_id.")", $ip);
														notify_message("✋ ".$username." อนุมัติรายการถอน Auto ของยูส ".$row['w_user']." จำนวน ".$row['w_amount']." บาท เวลา ".date("d/m/Y H:i:s"));
														dd_return(true, "ทำรายการสำเร็จ");
													}
													else
													{
														dd_return(false, "ทำรายการไม่สำเร็จ");
													}
		                    					}
		                    					else
		                    					{
		                    						dd_return(false, json_encode($withdraw->message, JSON_UNESCAPED_UNICODE));
		                    					}
		                    				}
		                    				else
		                    				{
		                    					dd_return(false, "ชื่อบัญชีไม่ตรงกับ ชื่อ-นามสกุล ที่สมัครไว้ ไม่สามารถถอนเงินได้ -".trim($data_name->name)."-".trim($row['w_fname']));
		                    				}
		                    			}
		                    			else
		                    			{
		                    				dd_return(false, json_encode($data_name->message, JSON_UNESCAPED_UNICODE));
		                    			}
		                    		}
		                    		else
		                    		{
		                    			dd_return(false, "ยอดเงินคงเหลือในธนาคารไม่เพียงพอ");
		                    		}
		                    	}
		                    	else
		                    	{
		                    		dd_return(false, "ไม่สามารถถอน Auto ได้ เนื่องจากยอดถอนเกิน ".number_format($limit, 2, '.', ',')." บาท กรุณาถอนแบบ Manual");
		                    	}
				    		}
				    		else
				    		{
				    			dd_return(false, json_encode($data_balance->message, JSON_UNESCAPED_UNICODE));
				    		}
				    	}
				    	else
				    	{
				    		dd_return(false, "ระบบโอนเงินออโต้ปิดใช้งาน");
				    	}
				    }
				    else
				    {
				    	dd_return(false, $row['w_bank_name']." ไม่รองรับการถอนเงินอัตโนมัติ กรุณาติดต่อผู้พัฒนา");
				    }
				}
				else
				{
					write_log("withdraw : approve_auto", $username." : กรอกรหัสสำหรับโอนเงินไม่ถูกต้อง (รหัสรายการ ".$w_id.")", $ip);
					dd_return(false, "รหัสสำหรับโอนเงินไม่ถูกต้อง");
				}
			}
			else
			{
				dd_return(false, "รายการถูกดำเนินการแล้ว");
			}
		}
		else
		{
			dd_return(false, "กรุณากรอกข้อมูลให้ครบ");
		}
	}
	else if($type == "approve")
	{
		$w_id = trim($_POST['w_id']);
		$username = trim($_POST['username']);
		$ip = trim($_POST['ip']);

		if($w_id != "" AND $username != "" AND $ip != "")
		{
			$q_1 = dd_q('SELECT * FROM withdraw_tb WHERE w_id = ? AND (w_status = ? OR w_status = ?)', [$w_id, '0', '99']);
			if ($q_1->rowCount() > 0)
			{
				$row = $q_1->fetch(PDO::FETCH_ASSOC);

				$q_2 = dd_q('UPDATE withdraw_tb SET w_status=?, w_action_by=? WHERE w_id=?', [
					'1',
					$username,
					$w_id
				]);

				if($q_2 = true)
				{
					write_log("withdraw : approve", $username." : อนุมัติรายการถอน (รหัสรายการ ".$w_id.")", $ip);
					dd_return(true, "ทำรายการสำเร็จ");
				}
				else
				{
					write_log("withdraw : approve", $username." : อนุมัติรายการถอน (รหัสรายการ ".$w_id.") : อัพเดทรายการคืนเงินไม่สำเร็จ", $ip);
					dd_return(false, "ทำรายการไม่สำเร็จ");
				}
			}
			else
			{
				dd_return(false, "รายการถูกดำเนินการแล้ว");
			}
		}
		else
		{
			dd_return(false, "กรุณากรอกข้อมูลให้ครบ");
		}
	}
	else if($type == "cancel")
	{
		$w_id = trim($_POST['w_id']);
		$username = trim($_POST['username']);
		$ip = trim($_POST['ip']);

		if($w_id != "" AND $username != "" AND $ip != "")
		{
			$q_1 = dd_q('SELECT * FROM withdraw_tb WHERE w_id = ? AND (w_status = ? OR w_status = ?)', [$w_id, '0', '99']);
			if ($q_1->rowCount() > 0)
			{
				$row = $q_1->fetch(PDO::FETCH_ASSOC);

				$q_2 = dd_q('UPDATE withdraw_tb SET w_status=?, w_action_by=? WHERE w_id=?', [
					'2',
					$username,
					$w_id
				]);

				if($q_2 = true)
				{
					write_log("withdraw : cancel", $username." : ยกเลิกรายการถอน (รหัสรายการ ".$w_id.")", $ip);
					dd_return(true, "ทำรายการสำเร็จ");
				}
				else
				{
					write_log("withdraw : cancel", $username." : ยกเลิกรายการถอน (รหัสรายการ ".$w_id.") : อัพเดทรายการคืนเงินไม่สำเร็จ", $ip);
					dd_return(false, "ทำรายการไม่สำเร็จ");
				}
			}
			else
			{
				dd_return(false, "รายการถูกดำเนินการแล้ว");
			}
		}
		else
		{
			dd_return(false, "กรุณากรอกข้อมูลให้ครบ");
		}
	}
	else if($type == "refund")
	{
		$w_id = trim($_POST['w_id']);
		$username = trim($_POST['username']);
		$ip = trim($_POST['ip']);

		if($w_id != "" AND $username != "" AND $ip != "")
		{
			$q_1 = dd_q('SELECT * FROM withdraw_tb WHERE w_id = ? AND (w_status = ? OR w_status = ?)', [$w_id, '0', '99']);
			if ($q_1->rowCount() > 0)
			{
				$row = $q_1->fetch(PDO::FETCH_ASSOC);

				// call api 
				$ExternalTransactionId = uniqid();
				$deposit = $api->transferCreditTo($row['w_agent_id'], str_replace(",", "", number_format($row['w_credit'], 2, '.', '')));
				if ($deposit->success == true)
				{
					$q_2 = dd_q('UPDATE withdraw_tb SET w_refund=?, w_status=?, w_action_by=? WHERE w_id=?', [
						$row['w_credit'],
						'3',
						$username,
						$w_id
					]);
					if($q_2 = true)
					{
						if(!empty($row['w_transfer_id']) && $row['w_transfer_id'] != "")
						{
							$q_tr = dd_q('SELECT * FROM transfergame_tb WHERE (t_id = ? AND t_user = ?) ORDER BY t_id DESC LIMIT 1', [$row['w_transfer_id'], $row['w_user']]);
							if ($q_tr->rowCount() > 0)
							{
								$row_tr = $q_tr->fetch(PDO::FETCH_ASSOC);
								if($row_tr['t_active'] == "N")
								{
									dd_q('UPDATE transfergame_tb SET t_active=? WHERE t_id=?', [
										"Y",
										$row_tr['t_id']
									]);
								}
							}
						}
						write_log("withdraw : refund", $username." : คืนเงินรายการถอน (รหัสรายการ ".$w_id.") : สำเร็จ", $ip);
						dd_return(true, "ทำรายการสำเร็จ");
					}
					else
					{
						write_log("withdraw : refund", $username." : คืนเงินรายการถอน (รหัสรายการ ".$w_id.") : อัพเดทรายการคืนเงินไม่สำเร็จ", $ip);
						dd_return(false, "ทำรายการไม่สำเร็จ");
					}
				}
				else
				{
					write_log("refund", $username." - ".$deposit->Message, $ip);
					dd_return(false, "ระบบเกิดข้อผิดพลาด กรุณาติดต่อเจ้าหน้าที่");
				}
			}
			else
			{
				dd_return(false, "รายการถูกดำเนินการแล้ว");
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
