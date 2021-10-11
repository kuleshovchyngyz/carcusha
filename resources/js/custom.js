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

$(document).ready(function() {
    $('#pictures').change(function(e) {
      //  var l = $('#pictures')[0].files.length;
       // preview_pic(-1,l);
         //console.log($('#pictures')[0].files.length)
        //
        // console.log($('#pictures')[0].files.length)
        var folder_id = $("#folder_id").val();
        var l = $('#pictures')[0].files.length;
        repeat(folder_id,-1,l)


    });
});

$(document).ready(function () {
$('#click').click(function () {
    console.log($('#pictures')[0].files)
});
});
$(document).ready(function () {
    $('.timesicon').click(function () {
        $(this).addClass('d-none');
        let url = $(this).next().attr('src');
        let name = url.substring(url.lastIndexOf('/')+1);
        console.log(name);
        var folder_id = $("#folder_id").val();
        var fd = new FormData();
        fd.append('_token', $('[name="_token"]').val())
        fd.append("name", name);
        fd.append("folder", folder_id);
        delete_image(fd).then(v => {
            console.log(v);
        });
        $(`#img${$(this).attr('id')}`).attr("src", '');
        $(`#img${$(this).attr('id')}`).addClass('d-none');
        $(`#img${$(this).attr('id')}`).removeClass('d-block');

    });
});

async function delete_image(fd){
    const result = await $.ajax({
        type: 'post',
        url:'/deleteimage',
        data:fd,
        contentType: false,
        processData: false,
    })
    return result;
}
function preview_pic(i,l)
{

    i++;
    console.log($(`#img1`).attr('src'));
    var reader = new FileReader();
    var filedata = '';
    if(i<l){
        reader.onload = function(){
            filedata = reader.result;

            $(`#img${i+1}`).attr("src", filedata);
            $(`#img${i+1}`).removeClass('d-none');
        };
        console.log(`#img${i+1}`);
        reader.readAsDataURL($('#pictures')[0].files[i]);

        preview_pic(i,l);
    }
}

function repeat(folder_id,i,l) {
    i++;
    console.log($('#pictures')[0].files[i])
    var fd = new FormData();
    fd.append('folder_id', folder_id)
    fd.append('_token', $('[name="_token"]').val())
    fd.append("file[]", $('#pictures')[0].files[i]);
    image_names(fd).then(v => {
        var getUrl = window.location;
        var baseUrl = getUrl.protocol + "//" + getUrl.host + "/uploads/" + folder_id + '/' + v;

        let check = false;
        check = is_busy();
        if(check!=false){
            console.log(check);
            $(`#img${check}`).attr("src", baseUrl);
            $(`#img${check}`).removeClass('d-none');
            $(`#${check}`).removeClass('d-none');
            $(`#img${check}`).addClass('d-block');
        }

        if(i<l-1){
            repeat(folder_id,i,l);
        }

    });

}
function is_busy(){

    for (let i=1;i<5;i++){
        if($(`#img${i}`).attr('src')==''){
            return i;
        }
    }
    return false;

}
async function image_names(fd){
    const result = await $.ajax({
        type: 'post',
        url:'/testimage',
        data:fd,
        contentType: false,
        processData: false,
    })
    return result;
}


$('#invitation').click(function () {
    $('#invitation').css('display', 'none');
    $('#invitation-inpup').css('display', 'block');
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


$('.main__dd-btn').click(function () {
    $(this).parent().find('.aside-dd').addClass('active');
    $(this).parents().find('.main__aside').addClass('active');
    $('body').addClass('overflow-hidden');
    if (
        $(this).hasClass("active")
    ) {
        $('body').removeClass('overflow-hidden');
        $(this).removeClass("active");
        $(this).parent().find('.aside-dd').removeClass('active');
        $(this).parents().find('.main__aside').removeClass('active');
    } else {
        $(this).addClass("active");
        $('body').addClass('overflow-hidden');
    }
});

