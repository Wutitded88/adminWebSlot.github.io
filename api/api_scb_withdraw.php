<?php
class SCBAPI
{
    function balance($api_key)
    {
        try
        {
            require_once 'config.php';
            require_once 'scbClass.php';
            $response = new stdClass();

            $q_1 = dd_q('SELECT * FROM autobank_tb WHERE a_bank_code = ? AND a_bank_status = ?', ['scb', 1]);
            if ($q_1->rowCount() > 0)
            {
                $row = $q_1->fetch(PDO::FETCH_ASSOC);

                $_deviceid = $row['a_bank_username'];
                $_pin = $row['a_bank_password'];
                $_acc_num = str_replace("-", "", $row['a_bank_acc_number']);
                $apiSCB = new SCB($_deviceid, $_pin, $_acc_num);
                $apiSCB->login();
                $balance = $apiSCB->summary();
                if(array_key_exists('totalAvailableBalance', $balance) && array_key_exists('status', $balance))
                {
                    if(array_key_exists('code', $balance["status"]) && array_key_exists('description', $balance["status"]))
                    {
                        if($balance["status"]["code"] == 1000)
                        {
                            $response->success=true;
                            $response->balance=str_replace(",", "", $balance["totalAvailableBalance"]);
                        }
                        else
                        {
                            $response->success=false;
                            $response->message=$balance["status"]["description"];
                        }
                    }
                    else
                    {
                        $response->success=false;
                        $response->message="";
                    }
                }
                else
                {
                    $response->success=false;
                    $response->message=$balance["status"]["description"];
                }
            }
            else
            {
                $response->success=false;
                $response->message="ออโต้ถอนปิดใช้งาน";
            }
            return $response;
        }
        catch(Exception $e)
        {
            $response = new stdClass();
            $response->success=false;
            $response->message=$e->getMessage();
            return $response;
        }
    }

    function getname($api_key, $bank_account_number_to, $bank_code_to)
    {
        if ($bank_account_number_to == "")
        {
            $response = new stdClass();
            $response->success=false;
            $response->message='bank account null';
            return $response;
        }

        try
        {
            require_once 'config.php';
            require_once 'scbClass.php';
            $response = new stdClass();

            $q_1 = dd_q('SELECT * FROM autobank_tb WHERE a_bank_code = ? AND a_bank_status = ?', ['scb', 1]);
            if ($q_1->rowCount() > 0)
            {
                $row = $q_1->fetch(PDO::FETCH_ASSOC);

                $_deviceid = $row['a_bank_username'];
                $_pin = $row['a_bank_password'];
                $_acc_num = str_replace("-", "", $row['a_bank_acc_number']);
                $apiSCB = new SCB($_deviceid, $_pin, $_acc_num);
                $apiSCB->login();

                $verify = $apiSCB->transfer_verify($bank_account_number_to, $bank_code_to, 1);
                if(array_key_exists('data', $verify) && array_key_exists('status', $verify))
                {
                    if(array_key_exists('code', $verify["status"]) && array_key_exists('description', $verify["status"]))
                    {
                        if($verify["status"]["code"] == 1000)
                        {
                            $response->success=true;
                            $response->name=$verify["data"]["accountToName"];
                        }
                        else
                        {
                            $response->success=false;
                            $response->message=$verify["status"]["description"];
                        }
                    }
                    else
                    {
                        $response->success=false;
                        $response->message="";
                    }
                }
                else
                {
                    $response->success=false;
                    $response->message=$verify["status"]["description"];
                }
            }
            else
            {
                $response->success=false;
                $response->message="ออโต้ถอนปิดใช้งาน";
            }
            return $response;
        }
        catch(Exception $e)
        {
            $response = new stdClass();
            $response->success=false;
            $response->message=$e->getMessage();
            return $response;
        }
    }

    function withdraw($api_key, $bank_account_number_to, $bank_code_to, $amount)
    {
        if ($bank_account_number_to == "")
        {
            $response = new stdClass();
            $response->success=false;
            $response->message='bank account null';
            return $response;
        }
        if ($amount < 0)
        {
            $response = new stdClass();
            $response->success=false;
            $response->message='amount less than 0';
            return $response;
        }
        if ($amount == 0)
        {
            $response = new stdClass();
            $response->success=true;
            $response->message='amount equals 0';
            return $response;
        }

        try
        {
            require_once 'config.php';
            require_once 'scbClass.php';
            $response = new stdClass();

            $q_1 = dd_q('SELECT * FROM autobank_tb WHERE a_bank_code = ? AND a_bank_status = ?', ['scb', 1]);
            if ($q_1->rowCount() > 0)
            {
                $row = $q_1->fetch(PDO::FETCH_ASSOC);

                $_deviceid = $row['a_bank_username'];
                $_pin = $row['a_bank_password'];
                $_acc_num = str_replace("-", "", $row['a_bank_acc_number']);
                $apiSCB = new SCB($_deviceid, $_pin, $_acc_num);
                $apiSCB->login();

                $confrim = $apiSCB->transfer_confrim($bank_account_number_to, $bank_code_to, $amount);
                if(array_key_exists('data', $confrim) && array_key_exists('status', $confrim))
                {
                    if(array_key_exists('code', $confrim["status"]) && array_key_exists('description', $confrim["status"]))
                    {
                        if($confrim["status"]["code"] == 1000)
                        {
                            $response->success=true;
                            $response->data=$confrim["data"];
                        }
                        else
                        {
                            $response->success=false;
                            $response->message=$confrim["status"]["description"];
                        }
                    }
                    else
                    {
                        $response->success=false;
                        $response->message="";
                    }
                }
                else
                {
                    $response->success=false;
                    $response->message=$confrim["status"]["description"];
                }
            }
            else
            {
                $response->success=false;
                $response->message="ออโต้ถอนปิดใช้งาน";
            }
            return $response;
        }
        catch(Exception $e)
        {
            $response = new stdClass();
            $response->success=false;
            $response->message=$e->getMessage();
            return $response;
        }
    }
}
?>