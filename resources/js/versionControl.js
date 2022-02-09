console.log(5555)
//setCookie('maksat','updated',1);


let js;
$('script').filter(function(){
    if(typeof $(this).attr('src') !== "undefined"&&$(this).attr('src').includes("/js/app.js"))
    {
        // console.log($(this).attr('src'))
        js = $(this).attr('src');
        //alert("Hi. Variable is defined.");
    }

})
let css;
$('link').filter(function(){
    if(typeof $(this).attr('href') !== "undefined"&&$(this).attr('href').includes("/css/app.css"))
    {
        // console.log($(this).attr('src'))
        css = $(this).attr('href');
        //alert("Hi. Variable is defined.");
    }

})
console.log(getCookie('js'))
console.log(getCookie('css'))

if(getCookie('js')===null||getCookie('js')!=js){
    setCookie('js',js,5);
    console.log(getCookie('js'))
    location.reload(true);
}

if(getCookie('css')===null||getCookie('css')!=css){
    setCookie('css',css,5);
   location.reload(true);
}
console.log(js)
console.log(css)

function setCookie(name,value,days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days*24*60*60*1000));
        expires = "; expires=" + date.toUTCString();
    }
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
