



<meta charset="utf-8">

<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title><?= $_CONFIG['title'] ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" />
<link rel="icon" type="image/png" href="/images/logo.png">

<link rel="stylesheet" href="<?= base_url() ?>/assets/plugins/fontawesome/css/all.min.css">
<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/plugins/datatables-bs4/css/dataTables.bootstrap4.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/dist/css/adminlte.min.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

<!-- Google Font: Source Sans Pro -->
<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Kanit:400" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Sarabun:400" rel="stylesheet">

<link href="<?= base_url() ?>/assets/plugins/bootstrap-tagsinput/tagsinput.css?v=11" rel="stylesheet" type="text/css">
<link href="<?= base_url() ?>/assets/plugins/bootstrap-datepicker-thai/css/datepicker.css" rel="stylesheet" type="text/css">
<script src="<?= base_url() ?>/assets/adminlte/bower_components/ckeditor/ckeditor.js"></script>

<style>
  body {
    font-family: 'Sarabun', sans-serif;

    /*font-size: 14px;*/
  }

  .card-scb.card-outline {
    border-top: 3px solid #4e2e7f;
  }

  .btn-scb {
    color: #fff;
    background-color: #4e2e7f;
    border-color: #4e2e7f;
    box-shadow: none;
  }

  .btn-scb:hover {
    color: #fff;
    background-color: #291842;
    border-color: #291842;
    box-shadow: none;
  }

  .card-bay.card-outline {
    border-top: 3px solid #fec43b;
  }

  .btn-bay {
    color: #fff;
    background-color: #fec43b;
    border-color: #fec43b;
    box-shadow: none;
  }

  .btn-bay:hover {
    color: #fff;
    background-color: #f1a900;
    border-color: #f1a900;
    box-shadow: none;
  }

  .card-tmw.card-outline {
    border-top: 3px solid #FF9800;
  }

  .btn-tmw {
    color: #fff;
    background-color: #FF9800;
    border-color: #FF9800;
    box-shadow: none;
  }

  .btn-tmw:hover {
    color: #fff;
    background-color: #e28700;
    border-color: #e28700;
    box-shadow: none;
  }

  #loading {
    height: 100%;
    width: 100%;
    position: fixed;
    z-index: 99999;
    left: 0;
    top: 0;
    color: white;
    background-color: rgb(0, 0, 0);
    background-color: rgba(0, 0, 0, 0.9);
    overflow-x: hidden;
    transition: 0.5s;
  }

  #loading .loading-content {
    position: relative;
    top: 30%;
    width: 100%;
    text-align: center;
  }

  .sk-wave {
    margin: 40px auto;
    width: 50px;
    height: 40px;
    text-align: center;
    font-size: 10px;
  }

  .sk-wave .sk-rect {
    background-color: #fff;
    height: 100%;
    width: 6px;
    display: inline-block;
    -webkit-animation: sk-waveStretchDelay 1.2s infinite ease-in-out;
    animation: sk-waveStretchDelay 1.2s infinite ease-in-out;
  }

  .sk-wave .sk-rect1 {
    -webkit-animation-delay: -1.2s;
    animation-delay: -1.2s;
  }

  .sk-wave .sk-rect2 {
    -webkit-animation-delay: -1.1s;
    animation-delay: -1.1s;
  }

  .sk-wave .sk-rect3 {
    -webkit-animation-delay: -1s;
    animation-delay: -1s;
  }

  .sk-wave .sk-rect4 {
    -webkit-animation-delay: -0.9s;
    animation-delay: -0.9s;
  }

  .sk-wave .sk-rect5 {
    -webkit-animation-delay: -0.8s;
    animation-delay: -0.8s;
  }

  @-webkit-keyframes sk-waveStretchDelay {

    0%,
    40%,
    100% {
      -webkit-transform: scaleY(0.4);
      transform: scaleY(0.4);
    }

    20% {
      -webkit-transform: scaleY(1);
      transform: scaleY(1);
    }
  }

  @keyframes sk-waveStretchDelay {

    0%,
    40%,
    100% {
      -webkit-transform: scaleY(0.4);
      transform: scaleY(0.4);
    }

    20% {
      -webkit-transform: scaleY(1);
      transform: scaleY(1);
    }
  }
</style>