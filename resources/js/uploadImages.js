$(document).ready(function() {
    $('#pictures').change(function(e) {
        var folder_id = $("#folder_id").val();
        var l = $('#pictures')[0].files.length;
        console.log('number of files: ' + l)
        repeat(folder_id,-1,l)
    });
});
function repeat(folder_id,i,l) {
    i++;
    console.log($('#pictures')[0].files[i])
    var fd = new FormData();
    fd.append('folder_id', folder_id)
    fd.append('_token', $('[name="_token"]').val())
    fd.append("file[]", $('#pictures')[0].files[i]);
    image_names(fd).then(v => {
        var getUrl = window.location;
        var baseUrl = getUrl.protocol + "//" + getUrl.host + "/uploads/" + folder_id + '/' + v[0];
        let check = false;
        check = is_busy(v[1]);
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
$("body").on("click", "a.linky", function() {
    alert($(this).attr("contentID"));
});

$(document).ready(function () {
    $("body").on("click", ".timesicon", function() {
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
function is_busy(l){
    let images = $('.uploadImage').length;
    if(l>images&&l<11){
        for(let i=0;i<l-images;i++){
            $('.onlyFour').append(`<li><i class="fa fa-times timesicon d-none" id="${images+i+1}" aria-hidden="true"></i> <img src="" id="img${images+i+1}" class="uploadImage d-none"></li>`);
        }
    }
    for (let i=1;i<l+1;i++){
        if($(`#img${i}`).attr('src')==''){
            return i;
        }
    }
    return false;

}

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
