import {trim} from "lodash/string";
import CreditCardInputMask from "credit-card-input-mask";

import IMask from 'imask';



$(document).ready(function(){
    var element = document.getElementById('phone');
    var maskOptions = {
        mask: '+{7}(000)000-00-00'
    };
    var mask = IMask(element, maskOptions);
})

$(document).ready(function(){
    var element = document.getElementById('number');
    var maskOptions = {
        mask: '+{7}(000)000-00-00'
    };
    var mask = IMask(element, maskOptions);
})

$(document).ready(function(){
    var element = document.getElementById('numberRegister');
    var maskOptions = {
        mask: '+{7}(000)000-00-00'
    };
    var mask = IMask(element, maskOptions);
})





$(document).ready(function(){

    $('#bankcardnumber').on('input', function(){

        const formattedCreditCardInput = new CreditCardInputMask({
            element: document.querySelector("#bankcardnumber"),
            pattern: "{{9999}} {{9999}} {{9999}} {{9999}}",
        });
        // document.getElementById("mask").focus()
        // let str = $('#mask').val();
        // console.log(str)
        // var lastChar = last_char(str);
        //
        // if(str.length<20){
        //     if($('#bankcardnumber').val().length > str.length){
        //         let str2 = $('#bankcardnumber').val();
        //         str2 = delete_last(str2);
        //         $('#bankcardnumber').val(str2);
        //
        //        let strmask = mask(delete_last(str2)).concat(last_char(str2));
        //
        //         $('#mask').val(strmask);
        //         return true;
        //     }
        //     $('#mask').val(mask($('#bankcardnumber').val()));
        //
        //     $('#bankcardnumber').val($('#bankcardnumber').val().concat(lastChar));
        //     $('#mask').val($('#mask').val().concat(lastChar));
        //     const formattedCreditCardInput = new CreditCardInputMask({
        //         element: document.querySelector("#bankcardnumber"),
        //         pattern: "{{9999}} {{9999}} {{9999}} {{9999}}",
        //     });
        // }else{
        //     str = str.substring(0, str.length - 1);
        //     $('#mask').val(str);
        // }
        // if(!(/[\d]{1}/g.test(lastChar))){
        //     $('#mask').val(delete_last(str));
        //     return
        // }
    })
});
function last_char(str){
    if(str.length>1){
        return str.substr(str.length - 1);
    }else{
        return str;
    }

}

function delete_last(str){
    if(str.length==1||str.length==0){
        return "";
    }
    return  str.substring(0, str.length - 1);
}

function mask(str){
    var re = /[\d]{1}/g;
    return  str.replace(re, '*');
}




$('.angle_toggle').click(function (){
    $(this).parent().find('.paid').removeClass('d-none');
    $(this).parent().find('.paid').slideToggle();
    $(this).find('.fa-angle-up').toggleClass('fa-angle-down');

});

$('.payment_amount').click(function () {
    $(this).parent().prev().val($(this).text());
    console.log($(this).text())
});
$('.pay_button').click(function () {
    // $('#invitation').css('display', 'none');
    // $('#invitation-inpup').css('display', 'block');
    let button_id = $(this).attr('id');
    let id = $(this).data('id');
    if(trim($('#'+button_id).text())!='Выплачено'){
        $.confirm({
            scrollToPreviousElement: true, // add this line
            scrollToPreviousElementAnimate: false, // add this line
            title: '',
            content: 'Вы уже произвели расчет?',
            buttons: {
                Да: function () {
                    pay_to_partner(id).then(data => {
                        if(data){
                            $('#'+button_id).html('Выплачено');
                            $('#'+button_id).removeClass('header__btn');
                            $('#'+button_id).addClass('green');
                        }else{
                            $.alert('меньше чем минимальная сумма для вывода');
                        }
                    });
                },
                heyThere: {
                    text: 'Отмена', // With spaces and symbols
                    action: function () {

                    }
                }
            }
        });

    }
});

async function pay_to_partner(paid){
    const result = await $.ajax({
        type: 'GET',
        url: `/admin/pay/${paid}`
    })
    return result;
}



$(document).ready(function () {
    $('#click').click(function () {
        console.log($('#pictures')[0].files)
    });
});










$('#forgotPass').click(function () {
    $('#forgotPass').css('display', 'none');
    $('.restorepass').css('display', 'inline-block');
})




$('#addQuestion').click(function () {


    $( `
               <div class="row mrg-top-40 ">
                <div class="col-md-4">
                    <label class="new-satings-input-wrap">
                        Вопрос:
                        <input type="text" name="questions[]"  class="form-control">
                    </label>
                </div>
                <div class="col-md-8">
                    <div class="title-field">Ответ:</div>
                    <textarea name="answers[]" id="" class="setting-textar">
                    </textarea>
                </div>
            </div>
    `



    ).appendTo( "#question" );
    console.log('sdfasdf');
})

$('.btn--copy').click(function () {
    var copyText = document.getElementById("reference");
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    document.execCommand("copy");
    console.log('copy');
});




$(document).ready(function() {

    $('select[name=car_vendor]').on('change', function(){
        var car_vendor = $(this).val();
        console.log(car_vendor);
        $('select[name=car_vendor]').val(car_vendor); //подставляем выбранное значение во все списки выбора марки авто
        if(car_vendor!="") {
            $('select[name=car_model]').prop('disabled', true);
            $.ajax({
                url: '/car',
                data: 'car_vendor='+car_vendor+'&action=get_car_models',
                dataType: 'html',
                success: function(response) {
                    //console.log(response);
                    $('select[name=car_model]').html(response);
                    $('select[name=car_model]').val('');
                    $('select[name=car_model]').prop('disabled', false);
                },
                error: function(errorThrown){
                    console.log(errorThrown);
                }
            });
        } else {
            $('select[name=car_model]').html('<option value="">Модель авто</option>');
            $('select[name=car_model]').val('');
        }
    });

});

$(document).ready(function () {
    $('#exampleModalCenter').modal('toggle')
    $( ".agreed" ).click(function() {
        $('#exampleModalCenter').modal('toggle')
    });
    //your code here
});
function submitForm(btn) {
    // disable the button
    console.log('creating')
    btn.disabled = true;
    // submit the form
    btn.form.submit();
}



