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
	else if($type == "add_topup")
	{
		$add_t_u_id = trim($_POST['add_t_u_id']);
		$add_t_amount = trim($_POST['add_t_amount']);
		$add_t_bank_id = trim($_POST['add_t_bank_id']);
		$add_t_date_create = trim($_POST['add_t_date_create']);
		$add_t_time_create_hour = trim($_POST['add_t_time_create_hour']);
		$add_t_time_create_minute = trim($_POST['add_t_time_create_minute']);
		$username = trim($_POST['username']);
		$ip = trim($_POST['ip']);

		if($add_t_u_id != "" AND $add_t_amount != "" AND $add_t_bank_id != "" AND $add_t_date_create != "" AND $add_t_time_create_hour != "" AND $add_t_time_create_minute != "" AND $username != "" AND $ip != "")
		{
			if(strlen($add_t_u_id) == 10)
			{
				$q_1 = dd_q('SELECT * FROM user_tb WHERE u_user=?', [$add_t_u_id]);
				if ($q_1->rowCount() > 0)
				{
					$row_u = $q_1->fetch(PDO::FETCH_ASSOC);

					$q_2 = dd_q('SELECT * FROM autobank_tb WHERE a_id = ?', [$add_t_bank_id]);
					if ($q_2->rowCount() > 0)
					{
						$row_b = $q_2->fetch(PDO::FETCH_ASSOC);

						if(!empty($row_u['u_agent_id']))
						{
							$_GetUserCredit = $api->getUserCredit($row_u['u_agent_id']);
							if ($_GetUserCredit->success == true)
							{
								// 2022/01/10
								// === เช็ค outstanding ยอดล่าสุด ===
								$sum_outstanding = 0;
								$q_trans = dd_q('SELECT * FROM transfergame_tb WHERE t_user = ? AND t_transaction_id != ? ORDER BY t_id DESC LIMIT 1', [
									$row_u['u_user'],
									""
								]);
								 
								if ($q_trans->rowCount() > 0)
								{
									$row_trans = $q_trans->fetch(PDO::FETCH_ASSOC);
									$outs = $api->GetWinLose($row_u['u_agent_id'], $row_trans['t_transaction_id']);
									if ($outs->success == true)
									{
										$outs = json_decode(json_encode($outs->data), true);
										foreach ($outs["data"] as $val)
										{
											$sum_outstanding = $sum_outstanding + ($val['outstanding']);
										}
									}
								}

								$q_top = dd_q('SELECT * FROM topup_db WHERE t_user = ? AND t_transaction_id != ? AND t_status = ? ORDER BY t_id DESC LIMIT 1', [
									$row_u['u_user'],
									"",
									"1"
								]);
								 
								if ($q_top->rowCount() > 0)
								{
									$row_top = $q_top->fetch(PDO::FETCH_ASSOC);
									$outs = $api->GetWinLose($row_u['u_agent_id'], $row_top['t_transaction_id']);
									if ($outs->success == true)
									{
										$outs = json_decode(json_encode($outs->data), true);
										foreach ($outs["data"] as $val)
										{
											$sum_outstanding = $sum_outstanding + ($val['outstanding']);
										}
										 
									}
								}
								// === เช็ค outstanding ยอดล่าสุด ===

								$isResetPromotion = true;
								$_GetUserCredit = json_decode(json_encode($_GetUserCredit->data), true);

								if($sum_outstanding != 0)
								{
									$isResetPromotion = false;
									write_log("topup : add_topup ไม่รีโปร", $username." : ".$row_u['u_user']." ไม่รีโปรเนื่องจาก outstanding = ".$sum_outstanding, $ip);
								}
								else if($_GetUserCredit['credit'] > 5)
								{
									$isResetPromotion = false;
									write_log("topup : add_topup ไม่รีโปร", $username." : ".$row_u['u_user']." ไม่รีโปรเนื่องจากเครดิตคงเหลือ = ".$_GetUserCredit['credit'], $ip);
								}

								if(!$isResetPromotion) //ไม่รีโปร
								{
									$q_trans_add = dd_q('SELECT * FROM transfergame_tb WHERE t_user = ? AND t_active = ? ORDER BY t_id DESC LIMIT 1', [
										$row_u['u_user'],
										"Y"
									]);
									if ($q_trans_add->rowCount() > 0)
									{
										$row_trans = $q_trans_add->fetch(PDO::FETCH_ASSOC);

										$_turnover = 0;
										$q_b = dd_q('SELECT * FROM promotion_tb WHERE p_title = ?', [
											$row_trans["t_promotion_title"]
										]);
										if ($q_b->rowCount() > 0)
										{
											$row_b = $q_b->fetch(PDO::FETCH_ASSOC);
											$_turnover = $row_b["p_turnover"];
										}
										else
										{
											$q_bx = dd_q('SELECT * FROM promotion_fixed_deposit_tb WHERE p_title = ?', [
												$row_trans["t_promotion_title"]
											]);
											if ($q_bx->rowCount() > 0)
											{
												$row_b = $q_bx->fetch(PDO::FETCH_ASSOC);
												$_turnover = $row_b["p_turnover"];
											}
										}
										
										if($row_trans["t_promotion_turntype"] == "c") //เท่า
										{
											$summary = $add_t_amount * $_turnover; //คำนวนยอดเทิร์น
										}
										else if($row_trans["t_promotion_turntype"] == "p") //เปอร์เซ็น
										{
											$summary = $add_t_amount + $add_t_amount * ($_turnover / 100); //คำนวนยอดเทิร์น
										}
										else if($row_trans["t_promotion_turntype"] == "w") //winloss
										{
											$summary = $add_t_amount * $_turnover; //คำนวนยอดเทิร์น
										}
										$t_turnover = $summary + $row_trans["t_turnover"];

										dd_q('UPDATE transfergame_tb SET t_turnover = ? WHERE t_id = ?', [
											$t_turnover, 
											$row_trans['t_id']
										]);
									}
									$deposit = $api->transferCreditTo($row_u['u_agent_id'], str_replace(",","",number_format($add_t_amount, 2)));
									if ($deposit->success == true)
									{
										$deposit = json_decode(json_encode($deposit->data), true);
										$ExternalTransactionId = $deposit['ref'];
										$q_3 = dd_q('INSERT INTO topup_db (t_u_id, t_user, t_agent_id, t_fname, t_topup_bank, t_tx_id, t_amount, t_bank_code, t_bank_number, t_bank_name, t_sys_bank_code, t_sys_bank_name, t_sys_bank_number, t_action_by, t_create_date, t_date_create, t_time_create, t_status, t_type, t_before_wallet, t_after_wallet, t_system_create_date ,t_system_create_time, t_type_system, t_transaction_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
											$row_u['u_id'], 
											$row_u['u_user'], 
											$row_u['u_agent_id'], 
											$row_u['u_fname']." ".$row_u['u_lname'], 
											$row_u['u_bank_number'], 
											'', 
											$add_t_amount, 
											$row_u['u_bank_code'], 
											$row_u['u_bank_number'], 
											$row_u['u_bank_name'], 
											$row_b['a_bank_code'], 
											$row_b['a_bank_name'], 
											$row_b['a_bank_acc_number'], 
											$username, 
											$add_t_date_create." ".$add_t_time_create_hour.":".$add_t_time_create_minute.":00", 
											$add_t_date_create, 
											$add_t_time_create_hour.":".$add_t_time_create_minute.":00", 
											"1", 
											"2", 
											$deposit['before'], 
											$deposit['after'], 
											date('Y-m-d'),
											date('H:i:s'),
											$row_b['a_bank_code'],
											$ExternalTransactionId
										]);
										if($q_3 = true)
										{
											$q_update_u = dd_q('UPDATE user_tb SET u_vip=? WHERE u_id=?', [
												'Vip', 
												$row_u['u_id']
											]);
											if($q_update_u == true)
											{
												//% แนะนำเพื่อน
												if(!empty($row_u['u_aff']))
												{
													if(get_config_website("aff_type") == "1") //ฝากแรกของเพื่อน
													{
														$q_t_aff = dd_q('SELECT * FROM topup_db WHERE t_user = ?', [$row_u['u_user']]);
														if ($q_t_aff->rowCount() == 1)
														{
															if(get_config_website("aff_step") >= 1)
															{
																$aff_percent = $add_t_amount * get_config_website("affpersen");
																dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status, aff_step, aff_create_date, aff_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
																	$row_u['u_aff'], 
																	$row_u['u_user'], 
																	$add_t_amount, 
																	$aff_percent, 
																	'0',
																	1, 
																	date("Y-m-d"),
																	date('H:i:s')
																]);
															}
															if(get_config_website("aff_step") >= 2)
															{
																$q_u2 = dd_q('SELECT * FROM user_tb WHERE u_user = ?', [
																	$row_u['u_aff']
																]);
																if ($q_u2->rowCount() > 0)
																{
																	$row_u2 = $q_u2->fetch(PDO::FETCH_ASSOC);
																	if(!empty($row_u2['u_aff']))
																	{
																		$aff_percent2 = $add_t_amount * get_config_website("affpersen2");
																		dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status, aff_step, aff_create_date, aff_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
																			$row_u2['u_aff'], 
																			$row_u['u_user'], 
																			$add_t_amount, 
																			$aff_percent2, 
																			'0',
																			2, 
																			date("Y-m-d"),
																			date('H:i:s')
																		]);
																	}
																}
															}
															if(get_config_website("aff_step") == 3)
															{
																$q_u2 = dd_q('SELECT * FROM user_tb WHERE u_user = ?', [
																	$row_u['u_aff']
																]);
																if ($q_u2->rowCount() > 0)
																{
																	$row_u2 = $q_u2->fetch(PDO::FETCH_ASSOC);
																	if(!empty($row_u2['u_aff']))
																	{
																		$q_u3 = dd_q('SELECT * FROM user_tb WHERE u_user = ?', [
																			$row_u2['u_aff']
																		]);
																		if ($q_u3->rowCount() > 0)
																		{
																			$row_u3 = $q_u3->fetch(PDO::FETCH_ASSOC);
																			if(!empty($row_u3['u_aff']))
																			{
																				$aff_percent3 = $add_t_amount * get_config_website("affpersen3");
																				dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status, aff_step, aff_create_date, aff_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
																					$row_u3['u_aff'], 
																					$row_u['u_user'], 
																					$add_t_amount, 
																					$aff_percent3, 
																					'0',
																					3, 
																					date("Y-m-d"),
																					date('H:i:s')
																				]);
																			}
																		}
																	}
																}
															}
														}
													}
													else if(get_config_website("aff_type") == "2")  //ทุกยอดฝาก
													{
														if($row_website['aff_step'] >= 1)
														{
															$aff_percent = $add_t_amount * get_config_website("affpersen");
															dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status, aff_step, aff_create_date, aff_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
																$row_u['u_aff'], 
																$row_u['u_user'], 
																$add_t_amount, 
																$aff_percent, 
																'0',
																1, 
																date("Y-m-d"),
																date('H:i:s')
															]);
														}
														if($row_website['aff_step'] >= 2)
														{
															$q_u2 = dd_q('SELECT * FROM user_tb WHERE u_user = ?', [
																$row_u['u_aff']
															]);
															if ($q_u2->rowCount() > 0)
															{
																$row_u2 = $q_u2->fetch(PDO::FETCH_ASSOC);
																if(!empty($row_u2['u_aff']))
																{
																	$aff_percent2 = $add_t_amount * get_config_website("affpersen2");
																	dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status, aff_step, aff_create_date, aff_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
																		$row_u2['u_aff'], 
																		$row_u['u_user'], 
																		$add_t_amount, 
																		$aff_percent2, 
																		'0',
																		2, 
																		date("Y-m-d"),
																		date('H:i:s')
																	]);
																}
															}
														}
														if($row_website['aff_step'] == 3)
														{
															$q_u2 = dd_q('SELECT * FROM user_tb WHERE u_user = ?', [
																$row_u['u_aff']
															]);
															if ($q_u2->rowCount() > 0)
															{
																$row_u2 = $q_u2->fetch(PDO::FETCH_ASSOC);
																if(!empty($row_u2['u_aff']))
																{
																	$q_u3 = dd_q('SELECT * FROM user_tb WHERE u_user = ?', [
																		$row_u2['u_aff']
																	]);
																	if ($q_u3->rowCount() > 0)
																	{
																		$row_u3 = $q_u3->fetch(PDO::FETCH_ASSOC);
																		if(!empty($row_u3['u_aff']))
																		{
																			$aff_percent3 = $add_t_amount * get_config_website("affpersen3");
																			dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status, aff_step, aff_create_date, aff_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
																				$row_u3['u_aff'], 
																				$row_u['u_user'], 
																				$add_t_amount, 
																				$aff_percent3, 
																				'0',
																				3, 
																				date("Y-m-d"),
																				date('H:i:s')
																			]);
																		}
																	}
																}
															}
														}
													}
												}
											}
											write_log("topup : add_topup", $username." : เพิ่มรายการฝากเงิน (ยูสเซอร์ ".$row_u['u_user'].")", $ip);
											dd_return(true, "ทำรายการสำเร็จ");
										}
										else
										{
											dd_return(false, "เพิ่มรายการฝากเงินไม่สำเร็จ");
										}
									}
									else
									{
										dd_return(false, "API Error : ไม่สามารถเติมเครดิตได้ กรุณาลองใหม่อีกครั้ง");
									}
								}
								else //รีโปร
								{
									$deposit = $api->transferCreditTo($row_u['u_agent_id'], str_replace(",", "", number_format($add_t_amount, 2)));
									if ($deposit->success == true)
									{
										$deposit = json_decode(json_encode($deposit->data), true);
										$ExternalTransactionId = $deposit['ref'];
										dd_q('UPDATE transfergame_tb SET t_active=? WHERE t_user=?', [
											"N", 
											$row_u['u_user']
										]);

										$q_3 = dd_q('INSERT INTO topup_db (t_u_id, t_user, t_agent_id, t_fname, t_topup_bank, t_tx_id, t_amount, t_bank_code, t_bank_number, t_bank_name, t_sys_bank_code, t_sys_bank_name, t_sys_bank_number, t_action_by, t_create_date, t_date_create, t_time_create, t_status, t_type, t_before_wallet, t_after_wallet, t_system_create_date ,t_system_create_time, t_type_system, t_transaction_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
											$row_u['u_id'], 
											$row_u['u_user'], 
											$row_u['u_agent_id'], 
											$row_u['u_fname']." ".$row_u['u_lname'], 
											$row_u['u_bank_number'], 
											'', 
											$add_t_amount, 
											$row_u['u_bank_code'], 
											$row_u['u_bank_number'], 
											$row_u['u_bank_name'], 
											$row_b['a_bank_code'], 
											$row_b['a_bank_name'], 
											$row_b['a_bank_acc_number'], 
											$username, 
											$add_t_date_create." ".$add_t_time_create_hour.":".$add_t_time_create_minute.":00", 
											$add_t_date_create, 
											$add_t_time_create_hour.":".$add_t_time_create_minute.":00", 
											"1", 
											"2", 
											$deposit['before'], 
											$deposit['after'], 
											date('Y-m-d'),
											date('H:i:s'),
											$row_b['a_bank_code'],
											$ExternalTransactionId
										]);
										if($q_3 = true)
										{
											$q_update_u = dd_q('UPDATE user_tb SET u_vip=? WHERE u_id=?', [
												'Vip', 
												$row_u['u_id']
											]);
											if($q_update_u == true)
											{
												//% แนะนำเพื่อน
												if(!empty($row_u['u_aff']))
												{
													if(get_config_website("aff_type") == "1") //ฝากแรกของเพื่อน
													{
														$q_t_aff = dd_q('SELECT * FROM topup_db WHERE t_user = ?', [$row_u['u_user']]);
														if ($q_t_aff->rowCount() == 1)
														{
															if(get_config_website("aff_step") >= 1)
															{
																$aff_percent = $add_t_amount * get_config_website("affpersen");
																dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status, aff_step, aff_create_date, aff_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
																	$row_u['u_aff'], 
																	$row_u['u_user'], 
																	$add_t_amount, 
																	$aff_percent, 
																	'0',
																	1, 
																	date("Y-m-d"),
																	date('H:i:s')
																]);
															}
															if(get_config_website("aff_step") >= 2)
															{
																$q_u2 = dd_q('SELECT * FROM user_tb WHERE u_user = ?', [
																	$row_u['u_aff']
																]);
																if ($q_u2->rowCount() > 0)
																{
																	$row_u2 = $q_u2->fetch(PDO::FETCH_ASSOC);
																	if(!empty($row_u2['u_aff']))
																	{
																		$aff_percent2 = $add_t_amount * get_config_website("affpersen2");
																		dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status, aff_step, aff_create_date, aff_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
																			$row_u2['u_aff'], 
																			$row_u['u_user'], 
																			$add_t_amount, 
																			$aff_percent2, 
																			'0',
																			2, 
																			date("Y-m-d"),
																			date('H:i:s')
																		]);
																	}
																}
															}
															if(get_config_website("aff_step") == 3)
															{
																$q_u2 = dd_q('SELECT * FROM user_tb WHERE u_user = ?', [
																	$row_u['u_aff']
																]);
																if ($q_u2->rowCount() > 0)
																{
																	$row_u2 = $q_u2->fetch(PDO::FETCH_ASSOC);
																	if(!empty($row_u2['u_aff']))
																	{
																		$q_u3 = dd_q('SELECT * FROM user_tb WHERE u_user = ?', [
																			$row_u2['u_aff']
																		]);
																		if ($q_u3->rowCount() > 0)
																		{
																			$row_u3 = $q_u3->fetch(PDO::FETCH_ASSOC);
																			if(!empty($row_u3['u_aff']))
																			{
																				$aff_percent3 = $add_t_amount * get_config_website("affpersen3");
																				dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status, aff_step, aff_create_date, aff_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
																					$row_u3['u_aff'], 
																					$row_u['u_user'], 
																					$add_t_amount, 
																					$aff_percent3, 
																					'0',
																					3, 
																					date("Y-m-d"),
																					date('H:i:s')
																				]);
																			}
																		}
																	}
																}
															}
														}
													}
													else if(get_config_website("aff_type") == "2")  //ทุกยอดฝาก
													{
														if($row_website['aff_step'] >= 1)
														{
															$aff_percent = $add_t_amount * get_config_website("affpersen");
															dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status, aff_step, aff_create_date, aff_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
																$row_u['u_aff'], 
																$row_u['u_user'], 
																$add_t_amount, 
																$aff_percent, 
																'0',
																1, 
																date("Y-m-d"),
																date('H:i:s')
															]);
														}
														if($row_website['aff_step'] >= 2)
														{
															$q_u2 = dd_q('SELECT * FROM user_tb WHERE u_user = ?', [
																$row_u['u_aff']
															]);
															if ($q_u2->rowCount() > 0)
															{
																$row_u2 = $q_u2->fetch(PDO::FETCH_ASSOC);
																if(!empty($row_u2['u_aff']))
																{
																	$aff_percent2 = $add_t_amount * get_config_website("affpersen2");
																	dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status, aff_step, aff_create_date, aff_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
																		$row_u2['u_aff'], 
																		$row_u['u_user'], 
																		$add_t_amount, 
																		$aff_percent2, 
																		'0',
																		2, 
																		date("Y-m-d"),
																		date('H:i:s')
																	]);
																}
															}
														}
														if($row_website['aff_step'] == 3)
														{
															$q_u2 = dd_q('SELECT * FROM user_tb WHERE u_user = ?', [
																$row_u['u_aff']
															]);
															if ($q_u2->rowCount() > 0)
															{
																$row_u2 = $q_u2->fetch(PDO::FETCH_ASSOC);
																if(!empty($row_u2['u_aff']))
																{
																	$q_u3 = dd_q('SELECT * FROM user_tb WHERE u_user = ?', [
																		$row_u2['u_aff']
																	]);
																	if ($q_u3->rowCount() > 0)
																	{
																		$row_u3 = $q_u3->fetch(PDO::FETCH_ASSOC);
																		if(!empty($row_u3['u_aff']))
																		{
																			$aff_percent3 = $add_t_amount * get_config_website("affpersen3");
																			dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status, aff_step, aff_create_date, aff_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
																				$row_u3['u_aff'], 
																				$row_u['u_user'], 
																				$add_t_amount, 
																				$aff_percent3, 
																				'0',
																				3, 
																				date("Y-m-d"),
																				date('H:i:s')
																			]);
																		}
																	}
																}
															}
														}
													}
												}
											}
											write_log("topup : add_topup", $username." : เพิ่มรายการฝากเงิน (ยูสเซอร์ ".$row_u['u_user'].")", $ip);
											dd_return(true, "ทำรายการสำเร็จ");
										}
										else
										{
											dd_return(false, "เพิ่มรายการฝากเงินไม่สำเร็จ");
										}
									}
									else
									{
										dd_return(false, "API Error : ไม่สามารถเติมเครดิตได้ กรุณาลองใหม่อีกครั้ง");
									}
								}
							}
							else
							{
								dd_return(false, "API Error : ไม่สามารถดึงข้อมูลเครดิตได้ กรุณาลองใหม่อีกครั้ง");
							}
						}
						else
						{
							dd_return(false, "รหัสเข้าเกมยังไม่ถูกสร้าง");
						}
					}
					else
					{
						dd_return(false, "ไม่พบข้อมูลลูกค้า");
					}
				}
				else
				{
					dd_return(false, "ไม่พบข้อมูลยูสเซอร์");
				}
			}
			else
			{
				dd_return(false, "ยูสเซอร์ ต้องมีจำนวน 10 ตัวเท่านั้น");
			}
		}
		else
		{
			dd_return(false, "กรุณากรอกข้อมูลให้ครบ");
		}
	}
	else if($type == "update_customer")
	{
		$t_id = trim($_POST['t_id']);
		$t_u_id = trim($_POST['t_u_id']);
		$username = trim($_POST['username']);
		$ip = trim($_POST['ip']);

		if($t_id != "" AND $t_u_id != "" AND $username != "" AND $ip != "")
		{
			$q_1 = dd_q('SELECT * FROM topup_db WHERE t_id = ? AND t_status = ? AND t_u_id = ?', [$t_id, '2', '']);
			if ($q_1->rowCount() > 0)
			{
				$q_2 = dd_q('SELECT * FROM user_tb WHERE u_id = ?', [$t_u_id]);
				if ($q_2->rowCount() > 0)
				{
					$row_u = $q_2->fetch(PDO::FETCH_ASSOC);

					$q_3 = dd_q('UPDATE topup_db SET t_u_id=?, t_user=?, t_agent_id=?, t_fname=?, t_bank_code=?, t_bank_number=?, t_bank_name=? WHERE t_id=?', [
						$row_u['u_id'], 
						$row_u['u_user'], 
						$row_u['u_agent_id'], 
						$row_u['u_fname'].' '.$row_u['u_lname'], 
						$row_u['u_bank_code'], 
						$row_u['u_bank_number'], 
						$row_u['u_bank_name'], 
						$t_id
					]);
					if($q_3 = true)
					{
						write_log("topup : update_customer", $username." : อัพเดทรายการฝาก (รหัสรายการ ".$t_id.")", $ip);
						dd_return(true, "ทำรายการสำเร็จ");
					}
					else
					{
						dd_return(false, "ทำรายการไม่สำเร็จ");
					}
				}
				else
				{
					dd_return(false, "ไม่พบข้อมูลลูกค้า");
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
	else if($type == "approve_credit")
	{
		$t_id = trim($_POST['t_id']);
		$username = trim($_POST['username']);
		$ip = trim($_POST['ip']);

		if($t_id != "" AND $username != "" AND $ip != "")
		{
			$q_1 = dd_q('SELECT * FROM topup_db WHERE t_id = ? AND t_status = ? AND t_u_id != ?', [$t_id, '2', '']);
			if ($q_1->rowCount() > 0)
			{
				$row_t = $q_1->fetch(PDO::FETCH_ASSOC);

				$q_2 = dd_q('SELECT * FROM user_tb WHERE u_id = ?', [$row_t['t_u_id']]);
				if ($q_2->rowCount() > 0)
				{
					$row_u = $q_2->fetch(PDO::FETCH_ASSOC);

					if(!empty($row_u['u_agent_id']))
					{
						$_GetUserCredit = $api->getUserCredit($row_u['u_agent_id']);
						if ($_GetUserCredit->success == true)
						{
							// 2022/01/10
							// === เช็ค outstanding ยอดล่าสุด ===
							$sum_outstanding = 0;
							$q_trans = dd_q('SELECT * FROM transfergame_tb WHERE t_user = ? AND t_transaction_id != ? ORDER BY t_id DESC LIMIT 1', [
								$row_u['u_user'],
								""
							]);
							if ($q_trans->rowCount() > 0)
							{
								$row_trans = $q_trans->fetch(PDO::FETCH_ASSOC);
								$outs = $api->GetWinLose($row_u['u_agent_id'], $row_trans['t_transaction_id']);
								if ($outs->success == true)
								{
									$outs = json_decode(json_encode($outs->data), true);
									foreach ($outs["data"] as $val)
									{
										$sum_outstanding = $sum_outstanding + ($val['outstanding']);
									}
								}
							}

							$q_top = dd_q('SELECT * FROM topup_db WHERE t_user = ? AND t_transaction_id != ? AND t_status = ? ORDER BY t_id DESC LIMIT 1', [
								$row_u['u_user'],
								"",
								"1"
							]);
							if ($q_top->rowCount() > 0)
							{
								$row_top = $q_top->fetch(PDO::FETCH_ASSOC);
								$outs = $api->GetWinLose($row_u['u_agent_id'], $row_top['t_transaction_id']);
								if ($outs->success == true)
								{
									$outs = json_decode(json_encode($outs->data), true);
									foreach ($outs["data"] as $val)
									{
										$sum_outstanding = $sum_outstanding + ($val['outstanding']);
									}
								}
							}
							// === เช็ค outstanding ยอดล่าสุด ===

							$isResetPromotion = true;
							$_GetUserCredit = json_decode(json_encode($_GetUserCredit->data), true);

							if($sum_outstanding != 0)
							{
								$isResetPromotion = false;
								write_log("topup : approve_credit ไม่รีโปร", $username." : ".$row_u['u_user']." ไม่รีโปรเนื่องจาก outstanding = ".$sum_outstanding." (รหัสรายการ ".$t_id.")", $ip);
							}
							else if($_GetUserCredit['credit'] > 5)
							{
								$isResetPromotion = false;
								write_log("topup : approve_credit ไม่รีโปร", $username." : ".$row_u['u_user']." ไม่รีโปรเนื่องจากเครดิตคงเหลือ = ".$_GetUserCredit['credit']." (รหัสรายการ ".$t_id.")", $ip);
							}

							if(!$isResetPromotion) //ไม่รีโปร
							{
								$deposit = $api->transferCreditTo($row_u['u_agent_id'], str_replace(",", "", number_format($row_t['t_amount'], 2)));
								if ($deposit->success == true)
								{
									$deposit = json_decode(json_encode($deposit->data), true);
									$ExternalTransactionId = $deposit['ref'];
									$q_3 = dd_q('UPDATE topup_db SET t_status=?, t_before_wallet=?, t_after_wallet=?, t_transaction_id=? WHERE t_id=?', [
										'1', 
										$deposit['before'], 
										$deposit['after'], 
										$ExternalTransactionId,
										$t_id
									]);
									if($q_3 = true)
									{
										$q_update_u = dd_q('UPDATE user_tb SET u_vip=? WHERE u_id=?', [
											'Vip', 
											$row_t['t_u_id']
										]);

										if($q_update_u == true)
										{
											//% แนะนำเพื่อน
											if(!empty($row_u['u_aff']))
											{
												if(get_config_website("aff_type") == "1") //ฝากแรกของเพื่อน
												{
													$q_t_aff = dd_q('SELECT * FROM topup_db WHERE t_user = ?', [$row_u['u_user']]);
													if ($q_t_aff->rowCount() == 1)
													{
														if(get_config_website("aff_step") >= 1)
														{
															$aff_percent = $row_t['t_amount'] * get_config_website("affpersen");
															dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status, aff_step, aff_create_date, aff_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
																$row_u['u_aff'], 
																$row_u['u_user'], 
																$row_t['t_amount'], 
																$aff_percent, 
																'0',
																1, 
																date("Y-m-d"),
																date('H:i:s')
															]);
														}
														if(get_config_website("aff_step") >= 2)
														{
															$q_u2 = dd_q('SELECT * FROM user_tb WHERE u_user = ?', [
																$row_u['u_aff']
															]);
															if ($q_u2->rowCount() > 0)
															{
																$row_u2 = $q_u2->fetch(PDO::FETCH_ASSOC);
																if(!empty($row_u2['u_aff']))
																{
																	$aff_percent2 = $row_t['t_amount'] * get_config_website("affpersen2");
																	dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status, aff_step, aff_create_date, aff_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
																		$row_u2['u_aff'], 
																		$row_u['u_user'], 
																		$row_t['t_amount'], 
																		$aff_percent2, 
																		'0',
																		2, 
																		date("Y-m-d"),
																		date('H:i:s')
																	]);
																}
															}
														}
														if(get_config_website("aff_step") == 3)
														{
															$q_u2 = dd_q('SELECT * FROM user_tb WHERE u_user = ?', [
																$row_u['u_aff']
															]);
															if ($q_u2->rowCount() > 0)
															{
																$row_u2 = $q_u2->fetch(PDO::FETCH_ASSOC);
																if(!empty($row_u2['u_aff']))
																{
																	$q_u3 = dd_q('SELECT * FROM user_tb WHERE u_user = ?', [
																		$row_u2['u_aff']
																	]);
																	if ($q_u3->rowCount() > 0)
																	{
																		$row_u3 = $q_u3->fetch(PDO::FETCH_ASSOC);
																		if(!empty($row_u3['u_aff']))
																		{
																			$aff_percent3 = $row_t['t_amount'] * get_config_website("affpersen3");
																			dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status, aff_step, aff_create_date, aff_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
																				$row_u3['u_aff'], 
																				$row_u['u_user'], 
																				$row_t['t_amount'], 
																				$aff_percent3, 
																				'0',
																				3, 
																				date("Y-m-d"),
																				date('H:i:s')
																			]);
																		}
																	}
																}
															}
														}
													}
												}
												else if(get_config_website("aff_type") == "2")  //ทุกยอดฝาก
												{
													if($row_website['aff_step'] >= 1)
													{
														$aff_percent = $row_t['t_amount'] * get_config_website("affpersen");
														dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status, aff_step, aff_create_date, aff_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
															$row_u['u_aff'], 
															$row_u['u_user'], 
															$row_t['t_amount'], 
															$aff_percent, 
															'0',
															1, 
															date("Y-m-d"),
															date('H:i:s')
														]);
													}
													if($row_website['aff_step'] >= 2)
													{
														$q_u2 = dd_q('SELECT * FROM user_tb WHERE u_user = ?', [
															$row_u['u_aff']
														]);
														if ($q_u2->rowCount() > 0)
														{
															$row_u2 = $q_u2->fetch(PDO::FETCH_ASSOC);
															if(!empty($row_u2['u_aff']))
															{
																$aff_percent2 = $row_t['t_amount'] * get_config_website("affpersen2");
																dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status, aff_step, aff_create_date, aff_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
																	$row_u2['u_aff'], 
																	$row_u['u_user'], 
																	$row_t['t_amount'], 
																	$aff_percent2, 
																	'0',
																	2, 
																	date("Y-m-d"),
																	date('H:i:s')
																]);
															}
														}
													}
													if($row_website['aff_step'] == 3)
													{
														$q_u2 = dd_q('SELECT * FROM user_tb WHERE u_user = ?', [
															$row_u['u_aff']
														]);
														if ($q_u2->rowCount() > 0)
														{
															$row_u2 = $q_u2->fetch(PDO::FETCH_ASSOC);
															if(!empty($row_u2['u_aff']))
															{
																$q_u3 = dd_q('SELECT * FROM user_tb WHERE u_user = ?', [
																	$row_u2['u_aff']
																]);
																if ($q_u3->rowCount() > 0)
																{
																	$row_u3 = $q_u3->fetch(PDO::FETCH_ASSOC);
																	if(!empty($row_u3['u_aff']))
																	{
																		$aff_percent3 = $row_t['t_amount'] * get_config_website("affpersen3");
																		dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status, aff_step, aff_create_date, aff_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
																			$row_u3['u_aff'], 
																			$row_u['u_user'], 
																			$row_t['t_amount'], 
																			$aff_percent3, 
																			'0',
																			3, 
																			date("Y-m-d"),
																			date('H:i:s')
																		]);
																	}
																}
															}
														}
													}
												}
											}
										}
										write_log("topup : approve_credit", $username." : ยืนยันรายการฝาก ระบบเพิ่มกระเป๋าเงิน (รหัสรายการ ".$t_id.")", $ip);
										dd_return(true, "ทำรายการสำเร็จ");
									}
									else
									{
										dd_return(false, "เพิ่มรายการฝากเงินไม่สำเร็จ");
									}
								}
								else
								{
									dd_return(false, "API Error : ไม่สามารถเติมเครดิตได้ กรุณาลองใหม่อีกครั้ง");
								}
							}
							else //รีโปร
							{
								$deposit = $api->transferCreditTo($row_u['u_agent_id'], str_replace(",","",number_format($row_t['t_amount'], 2)));
								if ($deposit->success == true)
								{
									$deposit = json_decode(json_encode($deposit->data), true);
									$ExternalTransactionId = $deposit['ref'];
									dd_q('UPDATE transfergame_tb SET t_active=? WHERE t_user=?', [
										"N", 
										$row_u['u_user']
									]);

									$q_3 = dd_q('UPDATE topup_db SET t_status=?, t_before_wallet=?, t_after_wallet=?, t_transaction_id=? WHERE t_id=?', [
										'1', 
										$deposit['before'], 
										$deposit['after'], 
										$ExternalTransactionId,
										$t_id
									]);
									if($q_3 = true)
									{
										$q_update_u = dd_q('UPDATE user_tb SET u_vip=? WHERE u_id=?', [
											'Vip', 
											$row_t['t_u_id']
										]);
										if($q_update_u == true)
										{
											//% แนะนำเพื่อน
											if(!empty($row_u['u_aff']))
											{
												if(get_config_website("aff_type") == "1") //ฝากแรกของเพื่อน
												{
													$q_t_aff = dd_q('SELECT * FROM topup_db WHERE t_user = ?', [$row_u['u_user']]);
													if ($q_t_aff->rowCount() == 1)
													{
														if(get_config_website("aff_step") >= 1)
														{
															$aff_percent = $row_t['t_amount'] * get_config_website("affpersen");
															dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status, aff_step, aff_create_date, aff_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
																$row_u['u_aff'], 
																$row_u['u_user'], 
																$row_t['t_amount'], 
																$aff_percent, 
																'0',
																1, 
																date("Y-m-d"),
																date('H:i:s')
															]);
														}
														if(get_config_website("aff_step") >= 2)
														{
															$q_u2 = dd_q('SELECT * FROM user_tb WHERE u_user = ?', [
																$row_u['u_aff']
															]);
															if ($q_u2->rowCount() > 0)
															{
																$row_u2 = $q_u2->fetch(PDO::FETCH_ASSOC);
																if(!empty($row_u2['u_aff']))
																{
																	$aff_percent2 = $row_t['t_amount'] * get_config_website("affpersen2");
																	dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status, aff_step, aff_create_date, aff_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
																		$row_u2['u_aff'], 
																		$row_u['u_user'], 
																		$row_t['t_amount'], 
																		$aff_percent2, 
																		'0',
																		2, 
																		date("Y-m-d"),
																		date('H:i:s')
																	]);
																}
															}
														}
														if(get_config_website("aff_step") == 3)
														{
															$q_u2 = dd_q('SELECT * FROM user_tb WHERE u_user = ?', [
																$row_u['u_aff']
															]);
															if ($q_u2->rowCount() > 0)
															{
																$row_u2 = $q_u2->fetch(PDO::FETCH_ASSOC);
																if(!empty($row_u2['u_aff']))
																{
																	$q_u3 = dd_q('SELECT * FROM user_tb WHERE u_user = ?', [
																		$row_u2['u_aff']
																	]);
																	if ($q_u3->rowCount() > 0)
																	{
																		$row_u3 = $q_u3->fetch(PDO::FETCH_ASSOC);
																		if(!empty($row_u3['u_aff']))
																		{
																			$aff_percent3 = $row_t['t_amount'] * get_config_website("affpersen3");
																			dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status, aff_step, aff_create_date, aff_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
																				$row_u3['u_aff'], 
																				$row_u['u_user'], 
																				$row_t['t_amount'], 
																				$aff_percent3, 
																				'0',
																				3, 
																				date("Y-m-d"),
																				date('H:i:s')
																			]);
																		}
																	}
																}
															}
														}
													}
												}
												else if(get_config_website("aff_type") == "2")  //ทุกยอดฝาก
												{
													if($row_website['aff_step'] >= 1)
													{
														$aff_percent = $row_t['t_amount'] * get_config_website("affpersen");
														dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status, aff_step, aff_create_date, aff_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
															$row_u['u_aff'], 
															$row_u['u_user'], 
															$row_t['t_amount'], 
															$aff_percent, 
															'0',
															1, 
															date("Y-m-d"),
															date('H:i:s')
														]);
													}
													if($row_website['aff_step'] >= 2)
													{
														$q_u2 = dd_q('SELECT * FROM user_tb WHERE u_user = ?', [
															$row_u['u_aff']
														]);
														if ($q_u2->rowCount() > 0)
														{
															$row_u2 = $q_u2->fetch(PDO::FETCH_ASSOC);
															if(!empty($row_u2['u_aff']))
															{
																$aff_percent2 = $row_t['t_amount'] * get_config_website("affpersen2");
																dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status, aff_step, aff_create_date, aff_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
																	$row_u2['u_aff'], 
																	$row_u['u_user'], 
																	$row_t['t_amount'], 
																	$aff_percent2, 
																	'0',
																	2, 
																	date("Y-m-d"),
																	date('H:i:s')
																]);
															}
														}
													}
													if($row_website['aff_step'] == 3)
													{
														$q_u2 = dd_q('SELECT * FROM user_tb WHERE u_user = ?', [
															$row_u['u_aff']
														]);
														if ($q_u2->rowCount() > 0)
														{
															$row_u2 = $q_u2->fetch(PDO::FETCH_ASSOC);
															if(!empty($row_u2['u_aff']))
															{
																$q_u3 = dd_q('SELECT * FROM user_tb WHERE u_user = ?', [
																	$row_u2['u_aff']
																]);
																if ($q_u3->rowCount() > 0)
																{
																	$row_u3 = $q_u3->fetch(PDO::FETCH_ASSOC);
																	if(!empty($row_u3['u_aff']))
																	{
																		$aff_percent3 = $row_t['t_amount'] * get_config_website("affpersen3");
																		dd_q('INSERT INTO aff_percent_tb (aff_u_user_ref, aff_user, aff_first_topup, aff_amount, aff_status, aff_step, aff_create_date, aff_create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
																			$row_u3['u_aff'], 
																			$row_u['u_user'], 
																			$row_t['t_amount'], 
																			$aff_percent3, 
																			'0',
																			3, 
																			date("Y-m-d"),
																			date('H:i:s')
																		]);
																	}
																}
															}
														}
													}
												}
											}
										}

										write_log("topup : approve_credit", $username." : ยืนยันรายการฝาก ระบบเพิ่มกระเป๋าเงิน (รหัสรายการ ".$t_id.")", $ip);
										dd_return(true, "ทำรายการสำเร็จ");
									}
									else
									{
										dd_return(false, "เพิ่มรายการฝากเงินไม่สำเร็จ");
									}
								}
								else
								{
									dd_return(false, "API Error : ไม่สามารถเติมเครดิตได้ กรุณาลองใหม่อีกครั้ง");
								}
							}
						}
						else
						{
							dd_return(false, "API Error : ไม่สามารถดึงข้อมูลเครดิตได้ กรุณาลองใหม่อีกครั้ง");
						}
					}
					else
					{
						dd_return(false, "รหัสเข้าเกมยังไม่ถูกสร้าง");
					}
				}
				else
				{
					dd_return(false, "ไม่พบข้อมูลลูกค้า");
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
	else if($type == "approve_no_credit")
	{
		$t_id = trim($_POST['t_id']);
		$username = trim($_POST['username']);
		$ip = trim($_POST['ip']);

		if($t_id != "" AND $username != "" AND $ip != "")
		{
			$q_1 = dd_q('SELECT * FROM topup_db WHERE t_id = ? AND t_status = ? AND t_u_id != ?', [$t_id, '2', '']);
			if ($q_1->rowCount() > 0)
			{
				$q_2 = dd_q('UPDATE topup_db SET t_status=? WHERE t_id=?', [
					'1', 
					$t_id
				]);
				if($q_2 = true)
				{
					write_log("topup : approve_credit", $username." : ยืนยันรายการฝาก ไม่เพิ่มกระเป๋าเงิน (รหัสรายการ ".$t_id.")", $ip);
					dd_return(true, "ทำรายการสำเร็จ");
				}
				else
				{
					dd_return(false, "อัพเดทรายการฝากไม่สำเร็จ");
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
	else if($type == "delete")
	{
		$t_id = trim($_POST['t_id']);
		$username = trim($_POST['username']);
		$ip = trim($_POST['ip']);

		if($t_id != "" AND $username != "" AND $ip != "")
		{
			$q_1 = dd_q('SELECT * FROM topup_db WHERE t_id = ? AND t_status = ? AND t_type = ? ', [$t_id, '1', '2']);
			if ($q_1->rowCount() > 0)
			{
				$row = $q_1->fetch(PDO::FETCH_ASSOC);

				$q_2 = dd_q('SELECT * FROM user_tb WHERE u_user = ?', [$row['t_user']]);
				if ($q_2->rowCount() > 0)
				{
					$row_u = $q_2->fetch(PDO::FETCH_ASSOC);

					if(!empty($row_u['u_agent_id']))
					{
						$_GetUserCredit = $api->getUserCredit($row_u['u_agent_id']);
						if ($_GetUserCredit->success == true)
						{
							$_GetUserCredit = json_decode(json_encode($_GetUserCredit->data), true);
							if($_GetUserCredit['credit'] >= $row['t_amount'])
							{
								$withdraw = $api->TransferCreditOut($row_u['u_agent_id'], str_replace(",", "", $row['t_amount']));
								if ($withdraw->success == true)
								{
									$withdraw = json_decode(json_encode($withdraw->data), true);
									dd_q('UPDATE topup_db SET t_status=?, t_action_by=?, t_before_wallet=?, t_after_wallet=?, t_transaction_id=? WHERE t_id=?', [
										'3',
										$username,
										$withdraw['before'],
										$withdraw['after'],
										"",
										$t_id
									]);
									write_log("topup : delete", $username." : ยกเลิกรายการฝาก (รหัสรายการ ".$t_id.")", $ip);
									dd_return(true, "ทำรายการสำเร็จ");
								}
								else
								{
									dd_return(false, "API Error : ระบบเกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง");
								}
							}
							else
							{
								dd_return(false, "ยอดเงินคงเหลือไม่พอหัก");
							}
						}
						else
						{
							dd_return(false, "API Error : ไม่สามารถดึงข้อมูลเครดิตได้ กรุณาลองใหม่อีกครั้ง");
						}
					}
					else
					{
						dd_return(false, "รหัสเข้าเกมยังไม่ถูกสร้าง");
					}
				}
				else
				{
					dd_return(false, "ไม่พบข้อมูลสมาชิก");
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
		$t_id = trim($_POST['t_id']);
		$username = trim($_POST['username']);
		$ip = trim($_POST['ip']);

		if($t_id != "" AND $username != "" AND $ip != "")
		{
			$q_1 = dd_q('SELECT * FROM topup_db WHERE t_id = ? AND t_status = ? AND t_type = ? ', [$t_id, '2', '1']);
			if ($q_1->rowCount() > 0)
			{
				$q_2 = dd_q('UPDATE topup_db SET t_status=?, t_action_by=? WHERE t_id=?', [
					'3',
					$username,
					$t_id
				]);
				if($q_2 = true)
				{
					write_log("topup : cancel", $username." : ยกเลิกรายการฝาก (รหัสรายการ ".$t_id.")", $ip);
					dd_return(true, "ทำรายการสำเร็จ");
				}
				else
				{
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
}
else
{
	dd_return(false, "Method '{$_SERVER['REQUEST_METHOD']}' not allowed!");
}
?>
