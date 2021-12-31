

$("#flexCheckUniquePayment").change(function() {

    let uniquePayment = false;
    let userId = $('div.main_user-name').data('user');
    console.log(userId)
    if(this.checked) {
        //I am checked
        uniquePayment = true;
        $('div.uniquePayment').removeClass('d-none');
        console.log('checked')
    }else{
        //I'm not checked
        uniquePayment = false;
        $('div.uniquePayment').addClass('d-none');
        console.log('unchecked')
    }
    changePayment(userId,uniquePayment,function(output){
        console.log(output)
    });
});

function  changePayment(id,toggle,handleData) {
    $.ajax({
        url: `/admin/user/paymentype`,
        data:{
            _token: $('[name="_token"]').val(),
            user: id,
            switch:toggle
        },
        type: 'post'
    })
        .done(function (data) {

            handleData(data);

        })
        .fail(function (data) {
            console.log(data);
        });
}
