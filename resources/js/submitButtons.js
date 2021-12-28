$(document).ready(function() {
    $("#submitSettings").on('click', function(){
        $( ".main__setting" ).append( $( "<input type='hidden'  name = 'submitSettings'>" ) );
        document.getElementById("settings").submit();
    });
});
$(document).ready(function() {
    $("#submitEmail").on('click', function(){
        $( ".main__setting" ).append( $( "<input type='hidden'  name = 'confirmEmail'>" ) );
        document.getElementById("settings").submit();
    });
});
$(document).ready(function() {
    $("#submitPhone").on('click', function(){
        $( ".main__setting" ).append( $( "<input type='hidden'  name = 'confirmPhone'>" ) );
        document.getElementById("settings").submit();
    });
})

$(document).ready(function() {
    $("#submitPromo").on('click', function(){
        $( ".main__setting" ).append( $( "<input type='hidden'  name = 'confirmPromo'>" ) );
        document.getElementById("settings").submit();
    });
})

