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
// require("fs").writeFile("demo.txt", "Foo bar!");


//
// $(document).ready(function() {
//     $('#updateProjectModal').modal('show');
//     var blob = new Blob(["Welcome to Websparrow.org."],
//         { type: "text/plain;charset=utf-8" });
//     saveAs(blob, "static.txt");
//     $("#rejectUpadateButton").on('click', function(){
//         location.reload(true);
//         $('#updateProjectModal').removeClass('fade show');
//         $('#updateProjectModal').toggle();
//         $(".modal-backdrop").removeClass('modal-backdrop fade show');
//     });
// })
//

