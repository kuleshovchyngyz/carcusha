
$(document).ready(function () {
    $('.password-checkbox').click(function () {
        let type = $('#password').attr("type")=='password' ? 'text' : 'password';
        $('#password').attr("type", type);
        $('#password-confirm').attr("type", type);
    });
});

$(document).ready(function () {
    $('.reset-password').click(function () {
        //console.log('reset')
        var password = $("#password").val();
        var user_id = $("#user_id").val();
        var password_confirmation = $("#password-confirm").val();
        var fd = new FormData();
        fd.append('_token', $('[name="_token"]').val())
        fd.append("password", password);
        fd.append("user_id", user_id);
        fd.append("password_confirmation", password_confirmation);

        submit_form(fd).then(result => {

            if(result.errors)
            {
                jQuery('.alert-danger').html('');

                jQuery.each(result.errors, function(key, value){
                    jQuery('.alert-danger').show();
                    jQuery('.alert-danger').append('<li>'+value+'</li>');
                });
            }
            else
            {

                jQuery('.alert-danger').hide();
                $('#open').hide();
                $('#resetPassword').modal('hide');
            }
        });
    });
});



async function submit_form(fd){
    const result = await $.ajax({
        type: 'post',
        url:'/admin/reset-password-admin',
        data:fd,
        contentType: false,
        processData: false,
    })
    return result;
}
