// เข้าสู่ระบบสมาชิก
$("#btn_login").click(function(e) {
    e.preventDefault();
    var formData = new FormData();
    formData.append('username', $("#username").val());
    formData.append('password', $("#password").val());
    formData.append('ip', $("#ip").val());
    $('#btn_login').attr('disabled', 'disabled');

    $.ajax({
        type: 'POST',
        url: 'system/login',
        data:formData,
        contentType: false,
        processData: false,
    }).done(function(res){
        result = res;
       
      alert(result.message);
       window.location = './dashboard';
       console.clear();
       $('#btn_login').removeAttr('disabled');
    }).fail(function(jqXHR){
        res = jqXHR.responseJSON;
         
        alert(res.message);
        console.clear();
        $('#btn_login').removeAttr('disabled');
    });
});
