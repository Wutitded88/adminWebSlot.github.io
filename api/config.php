<?php

ob_start();

session_start();

date_default_timezone_set("Asia/Bangkok");

header ("Last-Modified: " . gmdate ("D, d M Y H:i:s") . " GMT");



function base_url()

{

 return "http" . "://$_SERVER[HTTP_HOST]"."/ALKI67A"; // ใช้test localhost

  // return "https" . "://$_SERVER[HTTP_HOST]"."";

  // return "https" . "://$_SERVER[HTTP_HOST]".""; // ใช้เวลาขึ้นเครื่องจริง

}



function get_client_ip()

{

  $ipaddress = '';

  if (getenv('HTTP_CLIENT_IP'))

    $ipaddress = getenv('HTTP_CLIENT_IP');

  else if(getenv('HTTP_X_FORWARDED_FOR'))

    $ipaddress = getenv('HTTP_X_FORWARDED_FOR');

  else if(getenv('HTTP_X_FORWARDED'))

    $ipaddress = getenv('HTTP_X_FORWARDED');

  else if(getenv('HTTP_FORWARDED_FOR'))

    $ipaddress = getenv('HTTP_FORWARDED_FOR');

  else if(getenv('HTTP_FORWARDED'))

    $ipaddress = getenv('HTTP_FORWARDED');

  else if(getenv('REMOTE_ADDR'))

    $ipaddress = getenv('REMOTE_ADDR');

  else

    $ipaddress = 'UNKNOWN';

  return $ipaddress;

}



function password_encode($string)

{

  $ciphering = "AES-128-CTR";

  $encryption_key = "BS539TDGZF3ND71";

  $options = 0;

  $encryption_iv = '1234567891011121';

  $encrypted = openssl_encrypt($string, $ciphering, $encryption_key, $options, $encryption_iv); 

  return $encrypted;

}



function password_decode($string)

{

  $ciphering = "AES-128-CTR";

  $decryption_key = "BS539TDGZF3ND71";

  $options = 0;

  $decryption_iv = '1234567891011121';

  $decrypted = openssl_decrypt ($string, $ciphering, $decryption_key, $options, $decryption_iv); 

  return $decrypted;

}



function generateRandomString($length = 10)

{

  $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

  $charactersLength = strlen($characters);

  $randomString = '';

  for ($i = 0; $i < $length; $i++)

  {

    $randomString .= $characters[rand(0, $charactersLength - 1)];

  }

  return $randomString;

}



function generateRandomInt($length = 6)

{

  $characters = '0123456789';

  $charactersLength = strlen($characters);

  $randomString = '';

  for ($i = 0; $i < $length; $i++)

  {

    $randomString .= $characters[rand(0, $charactersLength - 1)];

  }

  return $randomString;

}



function notify_message($message)

{

  $q_u = dd_q('SELECT * FROM website_tb WHERE (id = ?)', [1]);

  $row_u = $q_u->fetch(PDO::FETCH_ASSOC);

  $token = $row_u['tokenline']; //ใส่ Token line n zAsIWCNDeZ2jYsuirykx97NIoPUH6vPp9RTqqTwjcL2



  $chOne = curl_init(); 

  curl_setopt($chOne, CURLOPT_URL, "https://notify-api.line.me/api/notify"); 

  curl_setopt($chOne, CURLOPT_SSL_VERIFYHOST, 0); 

  curl_setopt($chOne, CURLOPT_SSL_VERIFYPEER, 0); 

  curl_setopt($chOne, CURLOPT_POST, 1); 

  curl_setopt($chOne, CURLOPT_POSTFIELDS, "message=".$message); 

  $headers = array('Content-type: application/x-www-form-urlencoded', 'Authorization: Bearer '.$token.'',);

  curl_setopt($chOne, CURLOPT_HTTPHEADER, $headers); 

  curl_setopt($chOne, CURLOPT_RETURNTRANSFER, 1); 

  $result = curl_exec($chOne); 



  //Result error

  $resMsg = "";

  if(curl_error($chOne)) 

  { 

    $resMsg = 'error: '.curl_error($chOne); 

  } 

  else { 

    $result_ = json_decode($result, true); 

    $resMsg = "status: ".$result_['status']." message: ".$result_['message'];

  } 

  curl_close($chOne);

  return $resMsg;

}



class DB

{

  public static $str_hosting = 'localhost'; // แก้ไขได้

  public static $str_database = 'paylega_aqua783'; // แก้ไขได้

  public static $str_username = 'root'; // แก้ไขได้

  public static $str_password = ''; // แก้ไขได้



  protected static $pdo = null;

  public static function getConnection()

  {

    // initialize $pdo on first call

    if (self::$pdo == null)

    {

      self::init();

    }



    // now we should have a $pdo, whether it was initialized on this call or a previous one

    // but it could have experienced a disconnection

    try

    {

      // echo "Testing connection...\n";

      $old_errlevel = error_reporting(0);

      self::$pdo->query("SELECT 1");

    }

    catch (PDOException $e)

    {

      self::init();

    }

    error_reporting($old_errlevel);

    return self::$pdo;

  }



  protected static function init()

  {

    try

    {

      self::$pdo = new PDO("mysql:host=".self::$str_hosting.";dbname=".self::$str_database, self::$str_username, self::$str_password);

      self::$pdo->exec("set names utf8");

      self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    }

    catch (PDOException $e)

    {

      die($e->getMessage());

    }

  }

}



function dd_q($str, $arr = [])

{

  $pdo = DB::getConnection();

  try

  {

    $exec = $pdo->prepare($str);

    $exec->execute($arr);

  }

  catch (PDOException $e)

  {

    return false;

  }

  return $exec;

}



function get_session()

{

  if(isset($_SESSION['a_user']))

  {

    $user = $_SESSION['a_user'];

  }

  else

  {

    $user = "";

  }

  return $user;

}



function get_admin($col_name)

{

  $q_1 = dd_q('SELECT * FROM admin_tb WHERE a_user = ? LIMIT 1', [$_SESSION['a_user']]);

  $row_1 = $q_1->fetch(PDO::FETCH_ASSOC);

  return $row_1["$col_name"];

}



function write_log($page_name, $detail, $ip)

{

  $q_log = dd_q('INSERT INTO log_admin_tb (l_page, l_detail, l_create_date, l_ip, l_create_by) VALUES (?, ?, ?, ?, ?)', [

              $page_name,

              $detail,

              date("Y-m-d H:i:s"),

              $ip,

              get_session()

            ]);

  return $q_log;

}



$q_u = dd_q('SELECT * FROM website_tb WHERE (id = ?)', [1]);

$row_u = $q_u->fetch(PDO::FETCH_ASSOC);





// ############### New ###############

function get_config_website($col_name)

{

  $q_1 = dd_q('SELECT * FROM website_tb WHERE id = ? LIMIT 1', ['1']);

  $row_1 = $q_1->fetch(PDO::FETCH_ASSOC);

  return $row_1["$col_name"];

}



function set_config_website($key_name, $value)

{

  $q_website_tb = dd_q('UPDATE website_tb SET '.$key_name.' = ? WHERE id = ?', [

    $value,

    1

  ]);

  return $q_website_tb;

}













$_CONFIG = array();

//title หัวเว็บ

$_CONFIG['title'] = "ระบบจัดการหลังบ้าน - Paylegacy"; // แก้ไขได้

//header slide

$_CONFIG['slide'] = "Paylegacy"; // แก้ไขได้

//footer

$_CONFIG['footer'] = "Copyright © paylegacy.com 2021 All rights reserved."; // แก้ไขได้

//URL backoffice

$_CONFIG['backoffice'] = ""; // แก้ไขได้



//prefix for test

$_CONFIG['prefixfortest'] = ""; // แก้ไขได้



// เปอร์เซ็นชวนเพื่อน

$_CONFIG['aff_percent'] = $row_u['affpersen']; // แก้ไขได้

?>