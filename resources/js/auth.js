

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


$('.type-field-select').on("click", function () {
console.log(this)
    if ($('input', this).is(":checked")) {
        console.log('clicked 1')
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
