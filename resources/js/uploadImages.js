$(document).ready(function() {
    $('#pictures').change(function(e) {
      var folder_id = $("#folder_id").val();
       if($('.modalphotos').length){
         folder_id = $('#current_folder_id').val();
         console.log('folderrrrrr '+folder_id);
       }

        var l = $('#pictures')[0].files.length;
        console.log('number of files: ' + countBusy())
        repeat(folder_id,-1,l)
    });
});

 async function putImage(file,check,name) {
   if($('.modalphotos').length){
     let html = `
     <a class="gall__item example-image-link"   href="${file}" data-lightbox="example-set" data-title="Click the right half of the image to move forward.">
                     <img class="example-image" src="${file}" alt="">
                </a>`;
     $(`.modalphotos`).prepend(html);
   }else{
     $(`#img${check}`).attr("src", file);
     $(`#img${check}`).attr('data-name', name);
     $(`#img${check}`).removeClass('d-none');
     $(`#${check}`).removeClass('d-none');
     $(`#img${check}`).addClass('d-block');
   }
        // $('.onlyFour').append('<li><i class="fa fa-times timesicon" id="1" aria-hidden="true"></i><img src = "'+reader.result+'" class="uploadImage d-block"></li>');

}
function repeat(folder_id,i,l) {
    i++;
    var reader = new FileReader();
    console.log($('#pictures')[0].files[i])
    reader.readAsDataURL($('#pictures')[0].files[i]);
    reader.onload = function () {
        minifyImg(reader.result, 1600, 'image/jpeg', (data)=> {

            var fd = new FormData();
            console.log('n=busy : '+countBusy());
            fd.append('folder_id', folder_id)
            fd.append('number',$('#pictures')[0].files[i].name )
            fd.append('_token', $('[name="_token"]').val())
            fd.append("file[]", data);
            console.log(fd);
            image_names(fd).then(v => {
                console.log('sdfsdf: '+v[1]);
                console.log(v);
                let check = false;
                check = is_busy(v[1]);

                if(check!=false || $('.modalphotos').length){
                    addImageNames($('#pictures')[0].files[i].name);
                    putImage(data,check,$('#pictures')[0].files[i].name);
                }
                if(i<l-1){
                    repeat(folder_id,i,l);
                }

            });
        });

    };
    reader.onerror = function (error) {
        console.log('Error: ', error);
    };


}

function addImageNames(file){
  if($('.modalphotos').length){
    let folder = $('#current_folder_id').val();
    let images = $(`.${folder}`).data('image-names')+'||'+file+'.txt';
    $(`.${folder}`).data('image-names',images);
  }
}
function preview_pic(i,l)
{

    i++;
    //console.log($(`#img1`).attr('src'));
    var reader = new FileReader();
    var filedata = '';
    if(i<l){
        reader.onload = function(){
            filedata = reader.result;

            $(`#img${i+1}`).attr("src", filedata);
            $(`#img${i+1}`).removeClass('d-none');
        };

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
        // let url = $(this).next().attr('src');
        // let name = url.substring(url.lastIndexOf('/')+1);
        let id = $(this).next().data('name');
        let name = id+'.txt';
        var folder_id = $("#folder_id").val();
        var fd = new FormData();
        fd.append('_token', $('[name="_token"]').val())
        fd.append("name", name);
        fd.append("folder", folder_id);
        console.log(name);
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
    if(l>images&&l<21){
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
function countBusy(){
    let images = $('.uploadImage').length;
    let count = 0;
    for (let i=1;i<images+1;i++){
        if($(`#img${i}`).attr('src')!=''){
           count++;
        }
    }

    return count;
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
var minifyImg = function(dataUrl,newWidth,imageType="image/jpeg",resolve,imageArguments=0.7){
    var image, oldWidth, oldHeight, newHeight, canvas, ctx, newDataUrl;
    (new Promise(function(resolve){
        image = new Image(); image.src = dataUrl;
        setTimeout(() => {
            resolve('Done : ');
        }, 1000);

    })).then((d)=>{
        oldWidth = image.width; oldHeight = image.height;
        //console.log([oldWidth,oldHeight]);
        newHeight = Math.floor(oldHeight / oldWidth * newWidth);
        //console.log(d+' '+newHeight);

        canvas = document.createElement("canvas");
        canvas.width = newWidth; canvas.height = newHeight;
        //console.log(canvas);
        ctx = canvas.getContext("2d");
        ctx.drawImage(image, 0, 0, newWidth, newHeight);
        // console.log(ctx);

        newDataUrl = canvas.toDataURL(imageType, imageArguments);
        resolve(newDataUrl);
        // console.log(newDataUrl);
        //
    });
};
