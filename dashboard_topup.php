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
    $q_1 = dd_q('SELECT t_id, t_date_create, t_time_create, t_u_id, t_amount, t_status FROM topup_db WHERE t_status = ? ORDER BY t_id DESC', ['2']);
    if ($q_1->rowCount() > 0)
    {
    ?>
    <script type="text/javascript">
      $(function(){
        playSound_topup();
      });
      function playSound_topup()
      { //เสียงแจ้งฝากผิดพลาด
        //var audio = new Audio('<?=base_url()?>/assets/sound/topup_sound.mp3?ver=1');
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
          <?=date('d/m/Y', strtotime($row['t_date_create']))?>
        </td>
        <td>
          <?=$row['t_time_create']?>
        </td>
        <td>
          <a href="./deposit/<?=$row['t_id']?>" target="_blank">unknown</a>
        </td>
        <td>
          <?=$row['t_amount']?>
        </td>
        <td>
          <span class="text-danger">ระบบผิดพลาด</span>
        </td>
      </tr>
    <?php
    }
    ?>
  </tbody>
</table>