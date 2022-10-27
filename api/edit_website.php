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
	$type = trim($_POST['type']);
	if(empty(get_session()))
	{
		dd_return(false, "กรุณาเข้าสู่ระบบก่อนทำรายการ");
	}
	else if($type == "settingwebsite")
	{

		$namesite = trim($_POST['namesite']);
	    $title = trim($_POST['title']);
		$description = trim($_POST['description']);
		$keyword = trim($_POST['keyword']);
		$line = trim($_POST['line']);
		$logo = trim($_POST['logo']);
		$bg = trim($_POST['bg']);
		$slider1 = trim($_POST['slider1']);
		$slider2 = trim($_POST['slider2']);
		$slider3 = trim($_POST['slider3']);
		$slider4 = trim($_POST['slider4']);
		$slider5 = trim($_POST['slider5']);
		$slider6 = trim($_POST['slider6']);
		$lineurl = trim($_POST['lineurl']);
		$copyright = trim($_POST['copyright']);
		$post = trim($_POST['post']);
		$min_withdraw = trim($_POST['min_withdraw']);
		$recaptchakey = trim($_POST['recaptchakey']);
		$tokenline = trim($_POST['tokenline']);
		$recapchasecret = trim($_POST['recapchasecret']);
		$affpersen = trim($_POST['affpersen']);
		$affpersen2 = trim($_POST['affpersen2']);
		$affpersen3 = trim($_POST['affpersen3']);
		$aff_step = trim($_POST['aff_step']);
		$aff_type = trim($_POST['aff_type']);
		$aff_maxofday = trim($_POST['aff_maxofday']);
		$aff_promotion = trim($_POST['aff_promotion']);
		$truewallet = trim($_POST['truewallet']);
		$linewallet = trim($_POST['linewallet']);
		$lineregister = trim($_POST['lineregister']);
		$affwinloss = trim($_POST['affwinloss']);
		$minwinloss = trim($_POST['minwinloss']);
		$status_winloss = trim($_POST['status_winloss']);
		$status_checkin = trim($_POST['status_checkin']);
		$status_aff = trim($_POST['status_aff']);
		$status_freecredit = trim($_POST['status_freecredit']);
		$status_ranking = trim($_POST['status_ranking']);
		$sms_user = trim($_POST['sms_user']);
		$sms_pass = trim($_POST['sms_pass']);
		$sms_active = trim($_POST['sms_active']);
		$all_limitcredit = trim($_POST['all_limitcredit']);
		
		$id = trim($_POST['id']);

		if($all_limitcredit != "" AND $namesite != "" AND $title != "" AND $description != "" AND $keyword != "" AND $line != "" AND $logo != "" AND $bg != "" AND $slider1 != "" AND $slider2 != "" AND $slider3 != "" AND $slider4 != "" AND $slider5 != "" AND $slider6 != "" AND $lineurl != "" AND $copyright != "" AND $post != "" AND $min_withdraw != "" AND $recaptchakey != "" AND $tokenline != "" AND $recapchasecret != ""  AND $affpersen != "" AND $affpersen2 != "" AND $affpersen3 != "" AND $truewallet != "" AND $linewallet != "" AND $lineregister != "" AND $affwinloss != "" AND $minwinloss != "" AND $id != "" AND $aff_step != "" AND $aff_type != "" AND $aff_maxofday != "" AND $aff_promotion != "" AND $status_winloss != "" AND $status_checkin != "" AND $status_aff != "" AND $status_freecredit != "" AND $status_ranking != "" AND $sms_user != "" AND $sms_pass != "" AND $sms_active != "")
		{
			if($aff_step > 0 && $aff_step <= 3)
			{
				$q_1 = dd_q('UPDATE website_tb SET namesite=?, title=?, description=?, keyword=?, line=?, logo=?, bg=?, slider1=?, slider2=?, slider3=?, slider4=?, slider5=?, slider6=?, lineurl=?, copyright=?, post=?, min_withdraw=?, recaptchakey=?, tokenline=?, recapchasecret=? , affpersen=?, affpersen2=?, affpersen3=? , truewallet=?, linewallet=?, lineregister=?, affwinloss=?, minwinloss=?, aff_step=?, aff_type=?, aff_maxofday=?, aff_promotion=?, status_winloss=?, status_checkin=?, status_aff=?, status_freecredit=?, status_ranking=?, sms_user=?, sms_pass=?, sms_active=? , all_limitcredit=? WHERE id=?', [
					$namesite,
					$title,
					$description,
					$keyword,
					$line,
					$logo,
					$bg,
					$slider1,
					$slider2,
					$slider3,
					$slider4,
					$slider5,
					$slider6,
					$lineurl,
					$copyright,
					$post,
					$min_withdraw,
					$recaptchakey,
					$tokenline,
					$recapchasecret,
					$affpersen,
					$affpersen2,
					$affpersen3,
					$truewallet,
					$linewallet,
					$lineregister,
					$affwinloss,
					$minwinloss,
					$aff_step,
					$aff_type, 
					$aff_maxofday,
					$aff_promotion,
					$status_winloss,
					$status_checkin,
					$status_aff,
					$status_freecredit,
					$status_ranking,
					$sms_user,
					$sms_pass,
					$sms_active,
					$all_limitcredit,
					$id
				]);
				if($q_1 = true)
				{
					dd_return(true, "Success");
				}
				else
				{
					dd_return(false, "Error");
				}
			}
			else
			{
				dd_return(false, "ลำดับขั้นชวนเพื่อน 1-3 ท่านั้น");
			}
		}
		else
		{
			dd_return(false, "No input");
		}
	}
}
else
{
	dd_return(false, "Method '{$_SERVER['REQUEST_METHOD']}' not allowed!");
}
 
?>
