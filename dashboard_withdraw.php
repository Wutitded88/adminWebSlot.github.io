<?php
require_once './api/config.php';
?>
<table class="table" style="width: 100%;">
  <thead class="thead-dark text-center">
    <tr>
      <th scope="col">วัน</th>
      <th scope="col">เวลา</th>
      <th scope="col">ยูสเซอร์</th>
      <th scope="col">จำนวนเงิน</th>
      <th scope="col">สถานะ</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $q_1 = dd_q('SELECT w_id, w_date_create, w_time_create, w_u_id, w_user, w_amount, w_status FROM withdraw_tb WHERE (w_status = ? OR w_status = ?) ORDER BY w_id DESC', ['0', '99']);
    if ($q_1->rowCount() > 0)
    {
    ?>
    <script type="text/javascript">
      $(function(){
        playSound_withdraw();
      });
      function playSound_withdraw()
      {//เสียงแจ้งถอน
        //var audio = new Audio('<?=base_url()?>/assets/sound/withdraw_sound.mp3?ver=1');
        //audio.play();
      }
    </script>
    <?php
    }
    while($row = $q_1->fetch(PDO::FETCH_ASSOC))
    {
    ?>
      <tr>
        <td>
          <?=date('d/m/Y', strtotime($row['w_date_create']))?>
        </td>
        <td>
          <?=$row['w_time_create']?>
        </td>
        <td>
          <a href="./customer/detail/<?=$row['w_u_id']?>" target="_blank"><?=$row['w_user']?></a>
        </td>
        <td>
          <?=$row['w_amount']?>
        </td>
        <td>
          <a href="./withdraw/detail/<?=$row['w_id']?>" target="_blank">
            <?php
            if($row['w_status'] == "0")
            {
            ?>
              <span class="text-warning"><u>รอโอนเงิน</u></span>
            <?php
            }
            elseif($row['w_status'] == "1")
            {
            ?>
              <span class="text-success"><u>โอนเงินสำเร็จ</u></span>
            <?php
            }
            elseif($row['w_status'] == "2")
            {
            ?>
              <span class="text-danger"><u>ยกเลิกรายการ</u></span>
            <?php
            }
            elseif($row['w_status'] == "3")
            {
            ?>
              <span class="text-primary"><u>คืนเครดิต</u></span>
            <?php
            }
            elseif($row['w_status'] == "99")
            {
            ?>
              <span class="text-warning"><u>รอแอดมินตรวจสอบ</u></span>
            <?php 
            }
            ?>
          </a>
        </td>
      </tr>
    <?php
    }
    ?>
  </tbody>
</table>