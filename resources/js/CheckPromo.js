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
            $('#invitation-code').addClass('invitation-code');
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
            $('#invitation-code').addClass('invitation-code');
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
                    $('div.invitation-code').removeClass('error')
                    $('div.invitation-code input').removeClass('error_code')
                }
                if (v == 'no') {
                    $('div.invitation-code').addClass('error')
                    $('div.invitation-code input').addClass('error_code')
                    $('p.error').addClass('d-none')
                    $('div.invitation-code').removeClass('success')
                    $('div.invitation-code input').removeClass('success_code')
                    $('p.error').removeClass('d-none')
                    $('.activatePromo').addClass('d-none')
                }
            });
        }
        if(value==''){
            $('#invitation-code').removeClass('invitation-code');
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
    startTimer();

    function startTimer() {
      var presentTime = document.getElementById('timer').innerHTML;
      presentTime = presentTime.replace('Подтвердить по SMS ', '')
      var timeArray = presentTime.split(/[:]+/);
      var m = timeArray[0];
      var s = checkSecond((timeArray[1] - 1));
      if(s==59){m=m-1}
      if((m + '').length == 1){
        m = '0' + m;
      }
      console.log(m)
      if(m < 0){
        $('#timer').prop('disabled', false);
        // $(':input[type="submit"]').prop('disabled', true);
        $('#timer').removeClass('btn-disabled-sms');
         document.getElementById('timer').innerHTML = 'Подтвердить по SMS';
      }else{
        // $('#timer').disabled = true;
        $('#timer').prop('disabled', true);
        document.getElementById('timer').innerHTML =  'Подтвердить по SMS ' +m + ":" + s;
        setTimeout(startTimer, 1000);
      }
      
    }
    
    function checkSecond(sec) {
      if (sec < 10 && sec >= 0) {sec = "0" + sec}; // add zero in front of numbers < 10
      if (sec < 0) {sec = "59"};
      return sec;
    }
    

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
