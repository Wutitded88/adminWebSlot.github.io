<?php
require_once 'api/config.php';
if(empty(get_session()))
{
  echo "<SCRIPT LANGUAGE='JavaScript'>
          window.location.href = '".base_url()."/login';
        </SCRIPT>";
  exit();
}
else
{
  if(isset($_GET['type']) && isset($_GET['id']) && $_GET['type'] != "" && $_GET['id'] != "")
  {
    $q_1 = dd_q('SELECT * FROM user_tb WHERE (u_id = ?)', [$_GET['id']]);
    if ($q_1->rowCount() >= 1)
    {
      $row = $q_1->fetch(PDO::FETCH_ASSOC);
    }
    else
    {
      echo "<SCRIPT LANGUAGE='JavaScript'>
            window.location.href = '".base_url()."/dashboard';
          </SCRIPT>";
      exit();
    }
  }
  else
  {
    echo "<SCRIPT LANGUAGE='JavaScript'>
          window.location.href = '".base_url()."/dashboard';
        </SCRIPT>";
    exit();
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <?php include("master/MasterPages.php"); ?>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed text-sm">
  <input type="hidden" id="username" value="<?=get_session()?>">
  <input type="hidden" id="ip" value="<?=get_client_ip()?>">
  <input type="hidden" id="u_user" value="<?=$row['u_user']?>">
  <div class="wrapper">
    <?php include ("partials/_navbar.php"); ?>
    <?php include ("partials/_sidebar.php"); ?>
    <div class="content-wrapper">
      <section class="content-header pt-4 pb-4">
        <h1 style="font-size: 30px;">รายละเอียดผู้เล่น</h1>
      </section>
      <section class="content">
        <div class="row">
          <div class="col-sm-5">
            <div class="card card-outline">
              <div class="card-body box-profile">
                <div class="card-body">
                  <div class="text-center mb-3">
                    <img class="img-fluid" style="width: 25%" src="<?=base_url()?>/images/logov1.png">
                  </div>
                  <h3 class="text-center">
                    <?=$row['u_fname']?> <?=$row['u_lname']?>
                  </h3>
                  <p class="text-center" style="margin-bottom: 0px;">
                    <code class="text-dark" style="font-size: 14px;">
                      <?php echo $_CONFIG['prefixfortest']."".$row['u_agent_id']; ?>
                    </code>
                  </p>
                  <?php
                  if($row['u_aff'] != null && $row['u_aff'] != "")
                  {
                  ?>
                  <p class="text-center text-dark" style="font-size: 14px;">
                    แนะนำโดย: <?=$row['u_aff']?>
                  </p>
                  <?php
                  }
                  ?>
                  <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                      <b>สถานะ</b>
                      <?php
                      if($row['u_block_login'] == "0")
                      {
                      ?>
                      <span class="float-right text-success text-bold">เปิดใช้งาน</span>
                      <?php
                      }
                      elseif($row['u_block_login'] == "1")
                      {
                      ?>
                      <span class="float-right text-danger text-bold">ปิดใช้งาน</span>
                      <?php
                      }
                      ?>
                    </li>
                    <li class="list-group-item">
                      <b>สมัครสมาชิกเมื่อ</b>
                      <span class="float-right text-primary"><?=date('d/m/Y H:i:s', strtotime($row['u_create_on']))?></span>
                    </li>
                    <li class="list-group-item">
                      <b>IP Address</b>
                      <span class="float-right text-danger"><?=$row['u_ip']?></span>
                    </li>
                    <li class="list-group-item">
                      <b>ธนาคาร</b>
                      <span class="float-right"><?=$row['u_bank_name']?></span>
                    </li>
                    <li class="list-group-item">
                      <b>เลขที่บัญชี</b>
                      <span class="float-right">
                        <?php
                        $chars = array("\r\n", '\\n', '\\r', "\n", "\r", "\t", "\0", "\x0B");
                        $u_bank_number = str_replace($chars, "-มี enter", $row['u_bank_number']);
                        echo $u_bank_number;
                        ?>
                      </span>
                    </li>
                    <li class="list-group-item">
                      <b>เบอร์โทรศัพท์</b>
                      <span class="float-right"><?=$row['u_phone']?></span>
                    </li>
                    <li class="list-group-item">
                      <b>พาสเวิร์ด</b>
                      <span class="float-right">
                        <code class="text-dark" style="font-size: 14px;">
                          <?=password_decode($row['u_password'])?>
                        </code>
                      </span>
                    </li>
                    <li class="list-group-item">
                      <b>LINE ID</b>
                      <span class="float-right"><?=$row['u_line']?></span>
                    </li>
                    <li class="list-group-item">
                      <b>ยอดเงินคงเหลือ</b>
                      <span class="float-right text-warning text-bold" id="sp_jokercredit">0.00</span>
                    </li>
                    <li class="list-group-item">
                      <b>ยอดถอนสูงสุดต่อวัน</b>
 
                      <?php $u_users = $row['u_user']; $q_topup = dd_q('SELECT SUM(w_amount) AS total FROM withdraw_tb WHERE w_date_create = ? AND w_user = ?', [date("Y-m-d"),$u_users])->fetchColumn(); ?>
                      <span class="float-right text-info">วันนี้ถอนไปแล้ว : <?=$q_topup?>/<?=$row['u_limitcredit']; ?></span>
                    </li>
                    <?php
                    $q_transfergame_tb = dd_q('SELECT * FROM transfergame_tb WHERE t_user = ? AND t_active = ? ORDER BY t_id DESC LIMIT 1', [
                      $row['u_user'],
                      'Y'
                    ]);
                    if ($q_transfergame_tb->rowCount() > 0)
                    {
                      $row_transfergame_tb = $q_transfergame_tb->fetch(PDO::FETCH_ASSOC);
                      $_winloss = 0;

                      if($row_transfergame_tb['t_promotion_turntype'] == "w")
                      {
                        $q_topup_tb = dd_q('SELECT * FROM topup_db WHERE t_user = ? AND t_status = ? ORDER BY t_id DESC LIMIT 1', [
                          $row['u_user'],
                          '1'
                        ]);
                        if ($q_topup_tb->rowCount() > 0)
                        {
                          require_once 'api/Service/amb.php';
                          $apiAMB = new AMBAPI();
                          while($row_topup_tb = $q_topup_tb->fetch(PDO::FETCH_ASSOC))
                          {
                            if($row_topup_tb['t_transaction_id'] != "")
                            {
                              $winloss = $apiAMB->GetWinLose($row['u_agent_id'], $row_topup_tb['t_transaction_id']);
                              if ($winloss->success == true)
                              {
                                $winloss = json_decode(json_encode($winloss->data), true);

                                $q_turnover_tb = dd_q('SELECT * FROM turnover_tb WHERE t_active = ?', [
                                  '1'
                                ]);
                                while($row_turnover_tb = $q_turnover_tb->fetch(PDO::FETCH_ASSOC))
                                {
                                  foreach ($winloss["data"] as $val)
                                  {
                                    if(array_key_exists('game', $val) && $val['game'] == $row_turnover_tb['t_code'])
                                    {
                                      $_winloss = $_winloss + ($val['wlTurnAmount']);
                                    }
                                  }
                                }
                              }
                              else
                              {
                                echo json_encode($winloss);
                              }
                            }
                          }
                        }
                      }
                      else
                      {
                        require_once 'api/Service/amb.php';
                        $apiAMB = new AMBAPI();
                        $data = $apiAMB->getUserCredit($row['u_agent_id']);
                        if ($data->success == true)
                        {
                          $data = json_decode(json_encode($data->data), true);
                          $_winloss = $data['credit'];
                        }
                        else
                        {
                          $_winloss = 0;
                        }
                      }
                    ?>
                    <li class="list-group-item">
                      <b>ยอดเทิร์นที่ทำได้/ยอดเทิร์นที่กำหนด</b>
                      <span class="float-right text-warning text-bold"><?=number_format($_winloss, 2, '.', ',')?>/<?=number_format($row_transfergame_tb['t_turnover'], 2, '.', ',')?></span>
                    </li>
                    <?php
                    }
                    ?>
                    <li class="list-group-item">
                      <b>แนะนำเพื่อน</b>
                      <?php
                      $_affCount = 0;
                      $q_aff= dd_q('SELECT * FROM user_tb WHERE u_aff = ?', [$row['u_user']]);
                      $_affCount = $q_aff->rowCount();
                      ?>
                      <span class="float-right">
                        <a class="ml-1 mr-1" style="cursor: pointer;" data-toggle="modal" data-target="#modal-view-aff">
                          <i class="fas fa-search"></i>
                        </a>
                        <?=$_affCount?> คน
                      </span>
                    </li>
                    <li class="list-group-item">
                      <b>ยอดแนะนำเพื่อนรวมทั้งหมด</b>
                      <?php
                      $_affAmount = 0;
                      $q_2= dd_q('SELECT * FROM aff_percent_tb WHERE aff_u_user_ref = ?', [$row['u_user']]);
                      while($row_2 = $q_2->fetch(PDO::FETCH_ASSOC))
                      {
                        $_affAmount = $_affAmount + $row_2["aff_amount"];
                      }
                      ?>
                      <span class="float-right text-success text-bold"><?=number_format($_affAmount, 2)?></span>
                    </li>
                    <li class="list-group-item">
                      <b>คุณรู้จักเราได้อย่างไร</b>
                      <span class="float-right"><?=$row['u_refer_name']?></span>
                    </li>
                    <li class="list-group-item">
                      <b>ล็อคอินล่าสุด</b>
                      <span class="float-right"><?=date('d/m/Y H:i:s', strtotime($row['u_last_login']))?></span>
                    </li>
                  </ul>

                  <div class="row text-center">
                    <div class="col-md-12">
                      <input type="hidden" class="form-control" id="u_block_login" value="<?=$row['u_block_login']?>">
                      <button type="button" id="btn_b_login" class="btn btn-block <?php if($row['u_block_login'] == '0'){echo "btn-danger";}else{echo "btn-success";} ?>"><?php if($row['u_block_login'] == '0'){echo "บล็อคการล็อคอิน";}else{echo "ปลดบล็อคการล็อคอิน";} ?></button>
                    </div>
                    <!-- <div class="col-md-6">
                      <input type="hidden" class="form-control" id="u_block_agent" value="<?=$row['u_block_agent']?>">
                      <button type="button" id="btn_b_game" class="btn btn-block <?php if($row['u_block_agent'] == '0'){echo "btn-danger";}else{echo "btn-success";} ?>"><?php if($row['u_block_agent'] == '0'){echo "บล็อคการเล่นเกม";}else{echo "ปลดบล็อคการเล่นเกม";} ?></button>
                    </div> -->
                    <div class="col-md-12 mt-2">
                      <button type="button" class="btn btn-block btn-warning" data-toggle="modal" data-target="#modal-edit-player">แก้ไขข้อมูล</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-sm-7">
            <div class="card card-outline">
              <div class="card-body box-profile p-3">
                <h3>
                  ยอดฝาก-ถอน ทั้งหมด <span class="text-success">ฝาก : </span>
                  <?php
                  $sum_topup = 0.00;
                  $q_1_1 = dd_q('SELECT SUM(t_amount) AS sum_amount FROM topup_db WHERE t_user = ? AND t_status = ?', [$row['u_user'], "1"]);
                  $row_1_1 = $q_1_1->fetch(PDO::FETCH_ASSOC);
                  $sum_topup = $row_1_1['sum_amount'];
                  ?>
                  <small class="text-success"><?=number_format($sum_topup, 2)?></small>

                  <span class="text-danger">ถอน : </span>
                  <?php
                  $sum_withdraw = 0.00;
                  $q_3_1 = dd_q('SELECT SUM(w_amount) AS sum_amount FROM withdraw_tb WHERE w_status=? AND w_user=?', [
                    '1',
                    $row['u_user']
                  ]);
                  $row_3_1 = $q_3_1->fetch(PDO::FETCH_ASSOC);
                  $sum_withdraw = $row_3_1['sum_amount'];

                  $sum_all = 0.00;
                  $sum_all = $sum_topup-$sum_withdraw;

                  ?>
                  <small class="text-danger"><?=number_format($sum_withdraw, 2)?></small>
                  <small class="text-<?php if($sum_all > 0) { echo "success"; } else { echo "danger"; } ?>"> = <?php if($sum_all > 0) { echo "กำไร +"; } else { echo "ติดลบ "; }?><?=number_format($sum_all, 2)?></small>
                </h3>
              </div>
              <div class="card-body box-profile p-3">
                <h3>
                  รายการฝากเงิน
                  <?php
                  $sum_topup = 0.00;
                  $q_1_1 = dd_q('SELECT SUM(t_amount) AS sum_amount FROM topup_db WHERE t_user = ? AND t_status = ?', [$row['u_user'], "1"]);
                  $row_1_1 = $q_1_1->fetch(PDO::FETCH_ASSOC);
                  $sum_topup = $row_1_1['sum_amount'];
                  ?>
                  <small class="text-success">(<?=number_format($sum_topup, 2)?>)</small>
                </h3>
                <hr class="border-success">
                <div class="table-responsive">
                  <table class="table" id="tb_d" style="width: 100%;">
                    <thead class="text-center">
                      <tr>
                        <th scope="col">วันสร้าง</th>
                        <th scope="col">เวลาสร้าง</th>
                        <th scope="col">จำนวนเงินฝาก</th>
                        <th scope="col">เงินก่อนทำรายการ</th>
                        <th scope="col">เงินหลังทำรายการ</th>
                        <th scope="col">สถานะ</th>
                        <th scope="col">สร้างโดย</th>
                      </tr>
                    </thead>
                  </table>
                </div>
              </div>

              <div class="card-body box-profile p-3">
                <h3>
                  โบนัสแนะนำเพื่อน
                  <?php
                  $sum_amount = 0.00;
                  $q_5_1 = dd_q('SELECT SUM(aff_amount) AS sum_amount FROM aff_receive WHERE aff_u_user = ?', [$row['u_user']]);
                  $row_5_1 = $q_5_1->fetch(PDO::FETCH_ASSOC);
                  $sum_amount_aff = $row_5_1['sum_amount'];
                  ?>
                  <small class="text-primary">(<?=number_format($sum_amount_aff, 2)?>)</small>
                </h3>
                <hr class="border-primary">
                <div class="table-responsive">
                  <table class="table" id="tb_aff" style="width: 100%;">
                    <thead class="text-center">
                      <tr>
                        <th scope="col">วันสร้าง</th>
                        <th scope="col">จำนวนเงิน</th>
                      </tr>
                    </thead>
                  </table>
                </div>
              </div>

              <div class="card-body box-profile p-3">
                <h3>
                  คืนยอดเสีย
                  <?php
                  $sum_amount = 0.00;
                  $q_4_1 = dd_q('SELECT SUM(wl_cashback) AS sum_amount FROM winloss_receive_tb WHERE wl_user = ?', [$row['u_user']]);
                  $row_4_1 = $q_4_1->fetch(PDO::FETCH_ASSOC);
                  $sum_amount_cashback = $row_4_1['sum_amount'];
                  ?>
                  <small class="text-primary">(<?=number_format($sum_amount_cashback, 2)?>)</small>
                </h3>
                <hr class="border-primary">
                <div class="table-responsive">
                  <table class="table" id="tb_winloss" style="width: 100%;">
                    <thead class="text-center">
                      <tr>
                        <th scope="col">คืนยอดเสียวันที่</th>
                        <th scope="col">จำนวนเงิน</th>
                        <th scope="col">วันที่กดรับ</th>
                        <th scope="col">เวลาที่กดรับ</th>
                      </tr>
                    </thead>
                  </table>
                </div>
              </div>

              <div class="card-body box-profile p-3">
                <h3>
                  โบนัสเครดิต
                  <?php
                  $sum_amount = 0.00;
                  $q_2_1 = dd_q('SELECT SUM(t_amount) AS sum_amount FROM transfergame_tb WHERE t_user = ?', [$row['u_user']]);
                  $row_2_1 = $q_2_1->fetch(PDO::FETCH_ASSOC);
                  $sum_amount = $row_2_1['sum_amount'];
                  ?>
                  <small class="text-primary">(<?=number_format($sum_amount, 2)?>)</small>
                </h3>
                <hr class="border-primary">
                <div class="table-responsive">
                  <table class="table" id="tb_t" style="width: 100%;">
                    <thead class="text-center">
                      <tr>
                        <th scope="col">วันสร้าง</th>
                        <th scope="col">เวลาสร้าง</th>
                        <th scope="col">จำนวนเงิน</th>
                        <th scope="col">โบนัสที่รับ</th>
                        <th scope="col">จำนวนโบนัส</th>
                        <th scope="col">เครดิตที่ได้</th>
                        <th scope="col">ยอดเทิร์น</th>
                      </tr>
                    </thead>
                  </table>
                </div>
              </div>

              <div class="card-body box-profile p-3">
                <h3>
                  รายการถอนเงิน
                  <?php
                  $sum_withdraw = 0.00;
                  $q_3_1 = dd_q('SELECT SUM(w_amount) AS sum_amount FROM withdraw_tb WHERE w_status=? AND w_user=?', [
                    '1',
                    $row['u_user']
                  ]);
                  $row_3_1 = $q_3_1->fetch(PDO::FETCH_ASSOC);
                  $sum_withdraw = $row_3_1['sum_amount'];
                  ?>
                  <small class="text-danger">(<?=number_format($sum_withdraw, 2)?>)</small>
                </h3>
                <hr class="border-danger">
                <div class="table-responsive">
                  <table class="table" id="tb_w" style="width: 100%;">
                    <thead class="text-center">
                      <tr>
                        <th scope="col">วันสร้าง</th>
                        <th scope="col">เวลาสร้าง</th>
                        <th scope="col">ถอนเงิน</th>
                        <th scope="col">คืนเครดิต</th>
                        <th scope="col">สถานะ</th>
                        <th scope="col">สร้างโดย</th>
                        <th scope="col">พนักงาน</th>
                      </tr>
                    </thead>
                  </table>
                </div>
              </div>

            </div>
          </div>
        </div>
      </section>
    </div>
  </div>

  <!-- modal edit -->
  <div class="modal fade" id="modal-edit-player" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">แก้ไขข้อมูล</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>รหัสผู้เล่น</label>
            <div class="input-group">
              <input type="text" class="form-control" value="<?=$row['u_user']?>" disabled>
            </div>
          </div>
          <div class="form-group">
            <label>ยูสเซอร์</label>
            <div class="input-group">
              <input type="text" class="form-control" id="u_agent_id" placeholder="ยูสเซอร์" value="<?=$row['u_agent_id']?>" required <?php if(!empty($row['u_agent_id'])){echo "disabled";} ?>>
            </div>
          </div>
          <div class="form-group">
            <label>รหัสผ่านเข้าเล่น</label>
            <div class="input-group">
              <input type="text" class="form-control" id="u_password" placeholder="รหัสผ่านเข้าเล่น" value="<?=password_decode($row['u_password'])?>" required>
            </div>
          </div>
          <div class="form-group">
            <label>ชื่อ - นามสกุล</label>
            <div class="row">
              <div class="col-md-12">
                <input type="text" class="form-control" id="u_fname" placeholder="ชื่อ-นามสกุล" value="<?=$row['u_fname']?>" required>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label>ช่องทางที่รู้จัก</label>
            <div class="input-group">
              <select class="form-control" id="u_refer" required>
                <option value="">รู้จักเราจากที่ไหน</option>
                <option value="1:Google" <?php if($row['u_refer_id']=="1"){echo "selected";}?>>Google</option>
                <option value="2:Facebook" <?php if($row['u_refer_id']=="2"){echo "selected";}?>>Facebook</option>
                <option value="3:SMS" <?php if($row['u_refer_id']=="3"){echo "selected";}?>>SMS</option>
                <option value="4:เพื่อนแนะนำมา" <?php if($row['u_refer_id']=="4"){echo "selected";}?>>เพื่อนแนะนำมา</option>
                <option value="5:พนักงานชวน" <?php if($row['u_refer_id']=="5"){echo "selected";}?>>พนักงานชวน</option>
                <option value="6:อื่นๆ" <?php if($row['u_refer_id']=="6"){echo "selected";}?>>อื่นๆ</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label>ธนาคารสำหรับถอน</label>
            <div class="input-group">
              <select class="form-control" id="u_bank">
                <option value="">กรุณาเลือกธนาคาร</option>
                <?php
                $bank_tb = dd_q("SELECT * FROM bank_tb");
                while($row_1 = $bank_tb->fetch(PDO::FETCH_ASSOC))
                {
                  $selected = "";
                  if($row['u_bank_code'] == $row_1['b_short_name'])
                  {
                    $selected = "selected";
                  }
                ?>
                <option value="<?=$row_1['b_short_name']?>:<?=$row_1['b_official_name_th']?>" <?=$selected?>><?=$row_1['b_official_name_th']?></option>
                <?php
                }
                ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label>เลขบัญชี</label>
            <div class="input-group">
              <input type="text" class="form-control" id="u_bank_number" placeholder="เลขบัญชี" value="<?=$row['u_bank_number']?>">
            </div>
          </div>
          <div class="form-group">
            <label>LINE ID</label>
            <input type="text" class="form-control" id="u_line" placeholder="LINE ID" value="<?=$row['u_line']?>">
          </div>
          <div class="form-group">
            <label>ถอนสูงสุดต่อวัน</label>
            <input type="text" class="form-control" id="u_limitcredit" placeholder="100000" value="<?=$row['u_limitcredit']?>">
          </div>
  
          <div class="text-right">
            <button type="button" class="btn btn-primary btn-sm" id="btn_save">
              <i class="mdi mdi-content-save mr-1"></i>
              บันทึก
            </button>
            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
              <i class="mdi mdi-close mr-1"></i>
              ปิด
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modal-view-aff" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">ข้อมูลเพื่อนที่แนะนำ</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="table-responsive">
            <table class="table" id="tb_aff_detail" style="width: 100%;">
              <thead class="text-center">
                <tr>
                  <th scope="col">เบอร์</th>
                  <th scope="col">ชื่อ-นามสกุล</th>
                  <th scope="col">ส่วนแบ่ง</th>
                  <th scope="col">สถานะ</th>
                </tr>
              </thead>
            </table>
          </div>
          <div class="text-right mt-3">
            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
              <i class="mdi mdi-close mr-1"></i>
              ปิด
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- end modal edit -->
</body>
<?php include('partials/_footer.php'); ?>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/data.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script type="text/javascript">
  $(function () {
    getBalance();
    $('#tb_d').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": false,
        "info": true,
        "autoWidth": false,
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "ทั้งหมด"]],
        "language": {
          "emptyTable":     "ไม่พบข้อมูล",
          "info":           "แสดงข้อมูลแถวที่ _START_ ถึง _END_ จากทั้งหมด _TOTAL_ แถว",
          "infoEmpty":      "ไม่พบข้อมูล",
          "infoFiltered":   "(ค้นหาจากข้อมูลทั้งหมด _MAX_ แถว)",
          "infoPostFix":    "",
          "thousands":      ",",
          "lengthMenu":     "การแสดงผล _MENU_ แถว",
          "search":         "ค้นหา:",
          "zeroRecords":    "ไม่พบข้อมูลที่ค้นหา",
          "paginate": {
              "first":      "หน้าแรก",
              "last":       "หน้าสุดท้าย",
              "next":       "ถัดไป",
              "previous":   "ก่อนหน้า"
          }
        },
        "processing": true,
        "serverSide": true,
        "ajax": "<?=base_url()?>/server-side/server_user_deposit.php?userid=<?=$row['u_user']?>"
    });
    $('#tb_t').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": false,
        "info": true,
        "autoWidth": false,
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "ทั้งหมด"]],
        "language": {
          "emptyTable":     "ไม่พบข้อมูล",
          "info":           "แสดงข้อมูลแถวที่ _START_ ถึง _END_ จากทั้งหมด _TOTAL_ แถว",
          "infoEmpty":      "ไม่พบข้อมูล",
          "infoFiltered":   "(ค้นหาจากข้อมูลทั้งหมด _MAX_ แถว)",
          "infoPostFix":    "",
          "thousands":      ",",
          "lengthMenu":     "การแสดงผล _MENU_ แถว",
          "search":         "ค้นหา:",
          "zeroRecords":    "ไม่พบข้อมูลที่ค้นหา",
          "paginate": {
              "first":      "หน้าแรก",
              "last":       "หน้าสุดท้าย",
              "next":       "ถัดไป",
              "previous":   "ก่อนหน้า"
          }
        },
        "processing": true,
        "serverSide": true,
        "ajax": "<?=base_url()?>/server-side/server_user_transfer.php?userid=<?=$row['u_user']?>"
    });
    $('#tb_w').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": false,
        "info": true,
        "autoWidth": false,
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "ทั้งหมด"]],
        "language": {
          "emptyTable":     "ไม่พบข้อมูล",
          "info":           "แสดงข้อมูลแถวที่ _START_ ถึง _END_ จากทั้งหมด _TOTAL_ แถว",
          "infoEmpty":      "ไม่พบข้อมูล",
          "infoFiltered":   "(ค้นหาจากข้อมูลทั้งหมด _MAX_ แถว)",
          "infoPostFix":    "",
          "thousands":      ",",
          "lengthMenu":     "การแสดงผล _MENU_ แถว",
          "search":         "ค้นหา:",
          "zeroRecords":    "ไม่พบข้อมูลที่ค้นหา",
          "paginate": {
              "first":      "หน้าแรก",
              "last":       "หน้าสุดท้าย",
              "next":       "ถัดไป",
              "previous":   "ก่อนหน้า"
          }
        },
        "processing": true,
        "serverSide": true,
        "ajax": "<?=base_url()?>/server-side/server_user_withdraw.php?userid=<?=$row['u_user']?>"
    });
    $('#tb_aff').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": false,
        "info": true,
        "autoWidth": false,
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "ทั้งหมด"]],
        "language": {
          "emptyTable":     "ไม่พบข้อมูล",
          "info":           "แสดงข้อมูลแถวที่ _START_ ถึง _END_ จากทั้งหมด _TOTAL_ แถว",
          "infoEmpty":      "ไม่พบข้อมูล",
          "infoFiltered":   "(ค้นหาจากข้อมูลทั้งหมด _MAX_ แถว)",
          "infoPostFix":    "",
          "thousands":      ",",
          "lengthMenu":     "การแสดงผล _MENU_ แถว",
          "search":         "ค้นหา:",
          "zeroRecords":    "ไม่พบข้อมูลที่ค้นหา",
          "paginate": {
              "first":      "หน้าแรก",
              "last":       "หน้าสุดท้าย",
              "next":       "ถัดไป",
              "previous":   "ก่อนหน้า"
          }
        },
        "processing": true,
        "serverSide": true,
        "ajax": "<?=base_url()?>/server-side/server_user_aff.php?userid=<?=$row['u_user']?>"
    });
    $('#tb_aff_detail').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": false,
        "info": true,
        "autoWidth": false,
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "ทั้งหมด"]],
        "language": {
          "emptyTable":     "ไม่พบข้อมูล",
          "info":           "แสดงข้อมูลแถวที่ _START_ ถึง _END_ จากทั้งหมด _TOTAL_ แถว",
          "infoEmpty":      "ไม่พบข้อมูล",
          "infoFiltered":   "(ค้นหาจากข้อมูลทั้งหมด _MAX_ แถว)",
          "infoPostFix":    "",
          "thousands":      ",",
          "lengthMenu":     "การแสดงผล _MENU_ แถว",
          "search":         "ค้นหา:",
          "zeroRecords":    "ไม่พบข้อมูลที่ค้นหา",
          "paginate": {
              "first":      "หน้าแรก",
              "last":       "หน้าสุดท้าย",
              "next":       "ถัดไป",
              "previous":   "ก่อนหน้า"
          }
        },
        "processing": true,
        "serverSide": true,
        "ajax": "<?=base_url()?>/server-side/server_user_aff_detail.php?userid=<?=$row['u_user']?>"
    });
    $('#tb_winloss').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": false,
        "info": true,
        "autoWidth": false,
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "ทั้งหมด"]],
        "language": {
          "emptyTable":     "ไม่พบข้อมูล",
          "info":           "แสดงข้อมูลแถวที่ _START_ ถึง _END_ จากทั้งหมด _TOTAL_ แถว",
          "infoEmpty":      "ไม่พบข้อมูล",
          "infoFiltered":   "(ค้นหาจากข้อมูลทั้งหมด _MAX_ แถว)",
          "infoPostFix":    "",
          "thousands":      ",",
          "lengthMenu":     "การแสดงผล _MENU_ แถว",
          "search":         "ค้นหา:",
          "zeroRecords":    "ไม่พบข้อมูลที่ค้นหา",
          "paginate": {
              "first":      "หน้าแรก",
              "last":       "หน้าสุดท้าย",
              "next":       "ถัดไป",
              "previous":   "ก่อนหน้า"
          }
        },
        "processing": true,
        "serverSide": true,
        "ajax": "<?=base_url()?>/server-side/server_user_winloss.php?userid=<?=$row['u_user']?>"
    });
  });

  $("#btn_save").click(function(e) {
      e.preventDefault();

      var formData = new FormData();
      formData.append('u_user', $("#u_user").val());
      formData.append('u_agent_id', $("#u_agent_id").val());
      formData.append('u_password', $("#u_password").val());
      formData.append('u_fname', $("#u_fname").val());
      formData.append('u_refer_id', $("#u_refer").val().split(":")[0]);
      formData.append('u_refer_name', $("#u_refer").val().split(":")[1]);
      formData.append('u_bank_code', $("#u_bank").val().split(":")[0]);
      formData.append('u_bank_name', $("#u_bank").val().split(":")[1]);
      formData.append('u_bank_number', $("#u_bank_number").val());
      formData.append('u_line', $("#u_line").val());
      formData.append('u_limitcredit', $("#u_limitcredit").val());

      formData.append('type', "edit");
      formData.append('username', $("#username").val());
      formData.append('ip', $("#ip").val());

      $('#loading').show();

      $.ajax({
          type: 'POST',
          url: '<?=base_url()?>/system/api_customer',
          data:formData,
          contentType: false,
          processData: false,
      }).done(function(res){
          result = res;
          alert(result.message);
          window.location = '<?=base_url()?>/customer/<?=$_GET['type']?>/<?=$_GET['id']?>';
          console.clear();
          $('#loading').hide();
      }).fail(function(jqXHR){
          res = jqXHR.responseJSON;
          alert(res.message);
          console.clear();
          $('#loading').hide();
      });
  });

  $("#btn_b_login").click(function(e) {
      e.preventDefault();

      var formData = new FormData();
      formData.append('u_user', $("#u_user").val());
      formData.append('u_block_login', $("#u_block_login").val());

      formData.append('type', "b_login");
      formData.append('username', $("#username").val());
      formData.append('ip', $("#ip").val());

      $.ajax({
          type: 'POST',
          url: '<?=base_url()?>/system/api_customer',
          data:formData,
          contentType: false,
          processData: false,
      }).done(function(res){
          result = res;
          alert(result.message);
          window.location = '<?=base_url()?>/customer/<?=$_GET['type']?>/<?=$_GET['id']?>';
          console.clear();
      }).fail(function(jqXHR){
          res = jqXHR.responseJSON;
          alert(res.message);
          console.clear();
      });
  });

  function getBalance()
  {
        var formData = new FormData();
        formData.append('username', '<?=$row['u_user']?>');
        formData.append('ip', $("#ip").val());

        $.ajax({
            type: 'POST',
            url: '<?=base_url()?>/system/getbalance',
            data:formData,
            contentType: false,
            processData: false,
        }).done(function(res){
            result = res;
            $("#sp_jokercredit").text(parseFloat(result.message).toFixed(2));
        }).fail(function(jqXHR){
            res = jqXHR.responseJSON;
            alert(res.message);
        });
  }
</script>
</html>