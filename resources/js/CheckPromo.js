var pathname = window.location.pathname; // Returns path only (/path/example.html)
var url      = window.location.href;
let result = url.includes("login");
console.log(result)

$(document).ready(function(){
    var pathname = window.location.pathname; // Returns path only (/path/example.html)
    var url      = window.location.href;
    let result = url.includes("login");
    if(result){
        let value = $('#invitation-inpup').val();
        if(value!=''){
            var fd = new FormData();
            fd.append('_token', $('[name="_token"]').val())
            fd.append("promo", value);
            check_promo(fd).then(v => {
                if(v=='yes'){
                    $('div.invitation-code').addClass('success')
                    $('div.invitation-code input').addClass('success_code')
                    $('p.error').addClass('d-none')
                }
                if(v=='no'){
                    $('div.invitation-code').removeClass('success')
                    $('div.invitation-code input').removeClass('success_code')
                    $('p.error').removeClass('d-none')
                }
            });
        }
    }


    $("#invitation-inpup").on('keyup', function(){
        let value = $(this).val();
        if(value!='') {
            console.log(value)
            var fd = new FormData();

            fd.append('_token', $('[name="_token"]').val())
            fd.append("promo", value);
            check_promo(fd).then(v => {
                if (v == 'yes') {
                    $('div.invitation-code').addClass('success')
                    $('div.invitation-code input').addClass('success_code')
                    $('p.error').addClass('d-none')
                    $('.activatePromo').removeClass('d-none')
                }
                if (v == 'no') {
                    $('div.invitation-code').removeClass('success')
                    $('div.invitation-code input').removeClass('success_code')
                    $('p.error').removeClass('d-none')
                    $('.activatePromo').addClass('d-none')
                }
            });
        }
        if(value==''){
            $('p.error').addClass('d-none')
        }
    });

});

$(document).ready(function() {
    $("#registerButton").on('click', function(){
        if (!$('.checkbox-agree').is(':checked')) {
            $('#publicOfferWindow').modal('toggle');
        }else{
            $('#registration').submit();
        }
    });

});

$(document).ready(function(){

    $(".submitRegisterForm").on('click', function(){
        if($('.checkbox-agree').is(':checked')){
            $('#registration').submit();
            console.log($('#registration'))
        }

    });
});
$(document).ready(function(){

    $(".checkbox-agree").on('change', function(){

        if($('.checkbox-agree').is(':checked')){
            $('.submitRegisterForm').disabled = true;
            $('.submitRegisterForm').removeClass('btn-disabled');
            $('.submitRegisterForm').addClass('btn-blue');

        }
        if(!$('.checkbox-agree').is(':checked')){
            $('.submitRegisterForm').disabled = false;
            $('.submitRegisterForm').removeClass('btn-blue');
            $('.submitRegisterForm').addClass('btn-disabled');
        }
    });


});

async function check_promo(fd){
    const result = await $.ajax({
        type: 'post',
        url:'/check-promo',
        data:fd,
        contentType: false,
        processData: false,
    })
    return result;
}
