<footer class="main-footer">
<?=$_CONFIG['footer']?>
</footer>

<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
  <!-- Control sidebar content goes here -->
</aside>

   <!-- Histats.com  START  (aync)-->
<script type="text/javascript">var _Hasync= _Hasync|| [];
_Hasync.push(['Histats.start', '1,4630446,4,0,0,0,00010000']);
_Hasync.push(['Histats.fasi', '1']);
_Hasync.push(['Histats.track_hits', '']);
(function() {
var hs = document.createElement('script'); hs.type = 'text/javascript'; hs.async = true;
hs.src = ('//s10.histats.com/js15_as.js');
(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(hs);
})();</script>
<noscript><a href="/" target="_blank"><img  src="//sstatic1.histats.com/0.gif?4630446&101" alt="" border="0"></a></noscript>
<!-- Histats.com  END  -->
<script src="<?=base_url()?>/assets/plugins/jquery/jquery.min.js"></script>
<script src="<?=base_url()?>/assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?=base_url()?>/assets/plugins/datatables/jquery.dataTables.js"></script>
<script src="<?=base_url()?>/assets/plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
<script src="<?=base_url()?>/assets/plugins/bootstrap-tagsinput/tagsinput.js?v=1"></script>
<script src="<?=base_url()?>/assets/plugins/select2/js/select2.full.min.js"></script>
<script src="<?=base_url()?>/assets/dist/js/adminlte.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>/assets/plugins/bootstrap-datepicker-thai/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="<?=base_url()?>/assets/plugins/bootstrap-datepicker-thai/js/bootstrap-datepicker-thai.js"></script>
<script type="text/javascript" src="<?=base_url()?>/assets/plugins/bootstrap-datepicker-thai/js/locales/bootstrap-datepicker.th.js"></script>
<script src="<?=base_url()?>/js/script.inc.51yua5qtehpwryzsgxoh.js" type="text/javascript"></script>
<script src="<?=base_url()?>/js/js.js" type="text/javascript"></script>

<div id="loading" style="display: none">
  <div class="loading-content">
    <div class="sk-wave">
      <div class="sk-rect sk-rect1"></div>
      <div class="sk-rect sk-rect2"></div>
      <div class="sk-rect sk-rect3"></div>
      <div class="sk-rect sk-rect4"></div>
      <div class="sk-rect sk-rect5"></div>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(function(){
    getDateTime();
    setInterval(function(){
      getDateTime();
    },1000);
  });

  function getDateTime()
  {
    var currentdate = new Date();
    var date = "";
    var month = "";
    var hours = "";
    var minutes = "";
    var seconds = "";

    if(currentdate.getDate() < 10) { date = "0" + currentdate.getDate(); }
    else { date = currentdate.getDate(); }

    if((currentdate.getMonth()+1) < 10) { month = "0" + (currentdate.getMonth()+1); }
    else { month = (currentdate.getMonth()+1); }

    if(currentdate.getHours() < 10) { hours = "0" + currentdate.getHours(); }
    else { hours = currentdate.getHours(); }

    if(currentdate.getMinutes() < 10) { minutes = "0" + currentdate.getMinutes(); }
    else { minutes = currentdate.getMinutes(); }

    if(currentdate.getSeconds() < 10) { seconds = "0" + currentdate.getSeconds(); }
    else { seconds = currentdate.getSeconds(); }

    var datetime = date + "/" + month  + "/" + currentdate.getFullYear() + " " + hours + ":" + minutes + ":" + seconds;
    document.getElementById('lbl_datetime').innerHTML = datetime;
  }
</script>