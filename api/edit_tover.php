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
 
    $g_close = $_POST['check'];
    $g_id = $_POST['b_id'];

	$q_1 = dd_q('UPDATE turnover_tb SET t_active=? WHERE t_id=?', [
		$g_close,
		$g_id
	]);
	header("Location: ./config_game");
 

}
else
{
	dd_return(false, "Method '{$_SERVER['REQUEST_METHOD']}' not allowed!");
}
 
?>

 