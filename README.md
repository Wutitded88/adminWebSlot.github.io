<?php
require_once 'api/config.php';
if(!empty(get_session()))
{
  echo "<SCRIPT LANGUAGE='JavaScript'>
          window.location.href = './dashboard';
        </SCRIPT>";
}
else
{
	echo "<SCRIPT LANGUAGE='JavaScript'>
        window.location.href = './login';
      </SCRIPT>";
}
?>
