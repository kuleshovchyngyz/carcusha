

$('#forgotPass').click(function () {
    $('#forgotPass').css('display', 'none');
    $('.restorepass').css('display', 'inline-block');
})

// let linkToggle = document.querySelectorAll(".pseudo_link");
// for (let i = 0; i < linkToggle.length; i++) {
//     linkToggle[i].addEventListener('click', function (e) {
//         let link = this.querySelector(".main__table-link");
//         link.click();
//     }, false);
// }

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


$(document).ready(function() {
    // if(getCookie('checked')!==null)
    //setCookie('checked',false)


    $(".registertab.type-field-select").on('click', function(event){
        if($(this).prev().hasClass('active')){
            console.log(88888)
            setCookie('checkedregister',false);
        }else{
            $('span.form-subbtitle').empty();
        }
    event.preventDefault();
    console.log(getCookie('checkedregister'))

    setCookie('checkedregister',!getCookie('checkedregister'))


    if (getCookie('checkedregister')) {
        $('input', this).parents('form').find($('.type-mail')).removeClass('disabled').removeAttr('disabled');
        $('input', this).parents('form').find($('.type-phone')).addClass('disabled').attr('disabled', 'disabled');
        $('input', this).parents('form').find($('.form-subbtitle')).addClass('disabled');
        $(this).addClass('mail');
        $(this).siblings('.phone').removeClass('active');
        $(this).siblings('.mail').addClass('active');
        $('span.form-subbtitle').empty();

    } else {
        $('span.form-subbtitle').text('В формате +7');
        $('input', this).parents('form').find($('.type-mail')).addClass('disabled').attr('disabled', 'disabled');
        $('input', this).parents('form').find($('.type-phone')).removeClass('disabled').removeAttr('disabled');
        $('input', this).parents('form').find($('.form-subbtitle')).removeClass('disabled');
        $(this).removeClass('mail');
        $(this).siblings('.phone').addClass('active');
        $(this).siblings('.mail').removeClass('active');
    }
})

    if($( ".phone" ).hasClass( "active" )){
        setCookie('checkedlogin',false)
    }else{
        $('span.form-subbtitle').empty();
    }
    $(".logintab.type-field-select").on('click', function(event){
        event.preventDefault();
        console.log(getCookie('checkedlogin'))

        setCookie('checkedlogin',!getCookie('checkedlogin'))


        if (getCookie('checkedlogin')) {
            $('input', this).parents('form').find($('.type-mail')).removeClass('disabled').removeAttr('disabled');
            $('input', this).parents('form').find($('.type-phone')).addClass('disabled').attr('disabled', 'disabled');
            $('input', this).parents('form').find($('.form-subbtitle')).addClass('disabled');
            $(this).addClass('mail');
            $(this).siblings('.phone').removeClass('active');
            $(this).siblings('.mail').addClass('active');
            $('span.form-subbtitle').empty();

        } else {
            $('span.form-subbtitle').text('В формате +7');
            $('input', this).parents('form').find($('.type-mail')).addClass('disabled').attr('disabled', 'disabled');
            $('input', this).parents('form').find($('.type-phone')).removeClass('disabled').removeAttr('disabled');
            $('input', this).parents('form').find($('.form-subbtitle')).removeClass('disabled');
            $(this).removeClass('mail');
            $(this).siblings('.phone').addClass('active');
            $(this).siblings('.mail').removeClass('active');
        }
    })
})

function setCookie(name,value) {
    var expires = "";

        var date = new Date();
        date.setTime(date.getTime() + (60*1000));
        expires = "; expires=" + date.toUTCString();

    document.cookie = name + "=" + (value || "")  + expires + "; path=/";
}
function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}
