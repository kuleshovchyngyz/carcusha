console.log('checking promocode')
console.log($('#invitation-inpup').text())

$(document).ready(function(){
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

    $("#invitation-inpup").on('keyup', function(){
        let value = $(this).val();
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
            $('.submitRegister').disabled = true;
        }
        if(!$('.checkbox-agree').is(':checked')){
            $('.submitRegister').disabled = false;
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
