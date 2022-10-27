<style>
.sidebar::-webkit-scrollbar-thumb {
    background: #464646;
    border-radius: 10px;
}
.sidebar::-webkit-scrollbar {
    width: 3px;
}
</style>
<aside class="main-sidebar sidebar-light-primary elevation-4">
  <a href="" class="brand-link bg-primary">
    <img class=" " width="150px" src="./images/logov1.png">
  </a>
  <div class="sidebar">
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="info">
        <span>ชื่อผู้ใช้งาน: <?=get_session()?></span>
        <br>
        <span>สิทธิ์: <?php if(get_admin("a_role") == "1"){echo "ผู้ดูแลระบบ";}else{echo "พนักงาน";}?></span>
        <br>
        <hr style="border-top: 1px dashed #5858588a;">
        <?php 
          $q_depositscb = dd_q('SELECT * FROM autobank_tb WHERE (a_bank_code = ?)', ['scb']);
          $row_bankdepositscb = $q_depositscb->fetch(PDO::FETCH_ASSOC);
        ?>
        <span><img src="<?=base_url()?>/images/bank/scb.png" width="20px" style="border-radius:20px;"> ระบบ : <?php if ($row_bankdepositscb['a_bank_status'] == "1"){echo "ฝากออโต้ : <span style='color:#00ff08'>เปิด</span>";}else{echo "ฝากออโต้ : <span style='color:red;'>ปิด</span>";}?></span>
        <?php 
          $q_depositkbank = dd_q('SELECT * FROM autobank_tb WHERE (a_bank_code = ?)', ['kbank']);
          $row_bankdepositkbank = $q_depositkbank->fetch(PDO::FETCH_ASSOC);
        ?>
        <br>
        <span><img src="<?=base_url()?>/images/bank/kbank.png" width="20px" style="border-radius:20px;"> ระบบ : <?php if ($row_bankdepositkbank['a_bank_status'] == "1"){echo "ฝากออโต้ : <span style='color:#00ff08'>เปิด</span>";}else{echo "ฝากออโต้ : <span style='color:red;'>ปิด</span>";}?></span>
        <br>
        <hr style="border-top: 1px dashed #5858588a;">
        <?php 
          $q_bankon = dd_q('SELECT * FROM website_tb WHERE (id = ?)', [1]);
          $row_bankon = $q_bankon->fetch(PDO::FETCH_ASSOC);
        ?>
        <span>ระบบ : <?php if ($row_bankon['withdraw_auto'] == "1"){echo "ถอนออโต้ : <span style='color:#00ff08'>เปิด</span>";}else{echo "ถอนออโต้ : <span style='color:red;'>ปิด</span>";}?></span>
        <br>
        <?php 
          $q_bankonauto = dd_q('SELECT * FROM autobank_tb WHERE (a_withdraw_auto = ?)', [1]);
          while($row_banka = $q_bankonauto->fetch(PDO::FETCH_ASSOC))
          {
        ?>
        <span>ธนาคารถอน : <?php echo $row_banka['a_bank_name']; ?></span>
        <?php } ?>
      </div>
    </div>
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar nav-child-indent flex-column" data-widget="treeview" role="menu" data-accordion="false">

        <li class="nav-item">
          <a href="<?=base_url()?>/dashboard" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''?>">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>หน้าแรก</p>
          </a>
        </li>

        <?php
          $menu_open = '';
          $setting_active = '';

          $deposit_auto = '';
          if (
            basename($_SERVER['PHP_SELF']) == 'deposit-auto.php' ||
            basename($_SERVER['PHP_SELF']) == 'deposit-auto.php' ||
            basename($_SERVER['PHP_SELF']) == 'deposit-auto.php'
          ) {
            $deposit_auto = 'active';
            $setting_active = 'active';
            $menu_open = 'menu-open';
          }

          $deposit_manual = '';
          if (
            basename($_SERVER['PHP_SELF']) == 'deposit-manual.php' ||
            basename($_SERVER['PHP_SELF']) == 'deposit-manual.php' ||
            basename($_SERVER['PHP_SELF']) == 'deposit-manual.php'
          ) {
            $deposit_manual = 'active';
            $setting_active = 'active';
            $menu_open = 'menu-open';
          }
        ?>
        <li class="nav-item has-treeview <?=$menu_open?>">
          <a href="#" class="nav-link <?=$setting_active?>">
            <i class="nav-icon fas fa-dollar-sign"></i>
            <p>รายการฝาก <i class="fas fa-angle-left right"></i></p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="<?=base_url()?>/deposit-auto" class="nav-link <?=$deposit_auto?>">
                <i class="nav-icon fas fa-dollar-sign"></i>
                <p>อัตโนมัติ</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?=base_url()?>/deposit-manual" class="nav-link <?=$deposit_manual?>">
                <i class="nav-icon fas fa-dollar-sign"></i>
                <p>ปรับมือ</p>
              </a>
            </li>
          </ul>
        </li>

        <li class="nav-item">
          <a href="<?=base_url()?>/withdraw" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'withdraw.php' ? 'active' : ''?>">
            <i class="nav-icon fas fa-university"></i>
            <p>รายการถอน</p>
          </a>
        </li>
		 <?php
        // ===== get permission admin =====
        if(get_admin("a_role") < 2)
        {
        ?>
        <li class="nav-item">
          <a href="<?=base_url()?>/withdraw_auto" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'withdraw_auto.php' ? 'active' : ''?>">
            <i class="nav-icon fas fa-robot"></i>
            <p>ตั้งค่าถอนออโต้</p>
          </a>
        </li>
		<?php
        }
        // ===== get permission =====
        ?>
        <li class="nav-item">
          <a href="<?=base_url()?>/winloss" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'winloss.php' ? 'active' : ''?>">
            <i class="nav-icon fas fa-hand-holding-usd"></i>
            <p>คืนยอดเสีย</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="<?=base_url()?>/register-user" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'register-user.php' ? 'active' : ''?>">
            <i class="nav-icon fas fa-users"></i>
            <p>ยูสสมัคร</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="<?=base_url()?>/bonus" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'bonus.php' ? 'active' : ''?>">
            <i class="nav-icon fas fa-gift"></i>
            <p>โบนัสเครดิต</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="<?=base_url()?>/freecredit" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'freecredit.php' ? 'active' : ''?>">
            <i class="nav-icon fas fa-coins"></i>
            <p>แจกเครดิตฟรี</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="<?=base_url()?>/daily_report" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'daily_report.php' ? 'active' : ''?>">
            <i class="nav-icon fas fa-chart-bar"></i>
            <p>รายงานประจำวัน</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="<?=base_url()?>/user" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'user.php' ? 'active' : ''?>">
            <i class="nav-icon fas fa-user-friends"></i>
            <p>ข้อมูลผู้เล่น</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="<?=base_url()?>/user_block" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'user_block.php' ? 'active' : ''?>">
            <i class="nav-icon fas fa-user-slash"></i>
            <p>ผู้เล่นที่ถูกบล็อค</p>
          </a>
        </li>
		

      <!--  <li class="nav-item">
          <a target="_blank" href="<?=$_CONFIG['backoffice']?>" class="nav-link">
            <i class="nav-icon fas fa-crown"></i>
            <p>จัดการเอเย่น</p>
          </a>
        </li> -->

        <?php
        // ===== get permission admin =====
        if(get_admin("a_role") < 2)
        {
        ?>
		    <li class="nav-item">
          <a href="<?=base_url()?>/bank_config" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'bank_config.php' ? 'active' : ''?>">
            <i class="nav-icon fas fa-robot"></i>
            <p>ตั้งค่าบัญชีธนาคาร</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?=base_url()?>/config_promotion" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'config_promotion.php' ? 'active' : ''?>">
            <i class="nav-icon fas fa-tools"></i>
            <p>จัดการโปรโมชั่น</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?=base_url()?>/config_checkin" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'config_checkin.php' ? 'active' : ''?>">
            <i class="nav-icon fas fa-tools"></i>
            <p>ตั้งค่ารางวัลเช็คอิน</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?=base_url()?>/config_fixed_deposit" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'config_fixed_deposit.php' ? 'active' : ''?>">
            <i class="nav-icon fas fa-tools"></i>
            <p>จัดการฝากประจำ <span class="badge badge-success">New</span></p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?=base_url()?>/config_staff" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'config_staff.php' ? 'active' : ''?>">
            <i class="nav-icon fas fa-tools"></i>
            <p>จัดการข้อมูลพนักงาน</p>
          </a>
        </li>
		    <li class="nav-item">
          <a href="<?=base_url()?>/config_website" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'config_website.php' ? 'active' : ''?>">
            <i class="nav-icon fas fa-cog"></i>
            <p>จัดการข้อมูลเว็บไซต์ <span class="badge badge-success">New</span></p>
          </a>
        </li>
        <?php
        }
        // ===== get permission =====
        ?>
        <?php
        // ===== get permission admin =====
        if(get_admin("a_role") == "0")
        {
        ?>
        <li class="nav-item">
          <a href="<?=base_url()?>/log" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'log.php' ? 'active' : ''?>">
          <i class="nav-icon fad fa-user-shield"></i>
            <p>ตรวจสอบการทำงานระบบ</p>
          </a>
        </li>
        <?php
        }
        // ===== get permission =====
        ?>
        <li class="nav-item">
          <a href="<?=base_url()?>/config_game" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'config_game.php' ? 'active' : ''?>">
            <i class="nav-icon fas fa-gamepad-alt"></i>
            <p>ตั้งค่าเกม</p>
          </a>
        </li>
		    <li class="nav-item">
          <a href="<?=base_url()?>/howto" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'howto.php' ? 'active' : ''?>">
            <i class="nav-icon fad fa-question-circle"></i>
            <p>วิธีใช้งานหลังบ้าน</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?=base_url()?>/system/logout" class="nav-link text-danger">
            <i class="nav-icon fas fa-power-off"></i>
            <p>ออกจากระบบ</p>
          </a>
        </li>

      </ul>
    </nav>
  </div>
</aside>