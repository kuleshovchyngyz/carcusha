$('#gallModalCenter').on('hidden.bs.modal', function () {
  let folder = $('#current_folder_id').val();
  let images = $(`.${folder}`).data('image-names');
//  $('#myElementID').data('myvalue',38);
    console.log('closed');
    console.log(images);
})


const imageLoad = {
    image_names:[],
    folder:'',
    lead_id:'',
    init() {

        $(`.gall-upload`).on('click', function (e) {
            console.log('gall clicked');
            $(`#pictures`).trigger('click');
        });
        $(`.leadPoto`).on('click', {self:this}, function (e) {
            $('.lead__name').text('');
            $('.modalphotos').find('*').not('.const').remove();
            let self = e.data.self;
            self.image_names = $(this).data('image-names').split('||');
            self.folder = $(this).data('lead-folder');
            self.lead_id = $(this).data('lead-id');
            $('.lead__name').text($(this).data('lead-name'));

            console.log(self)

            $('#sendToBitrixButton').attr("data-lead-id",self.lead_id);
            self.getImages();

            $('#current_folder_id').val(self.folder);
        });



    },

    getImages() {
        this.image_names.forEach((item) => {
            this.getImage(this.folder,item).then( v => {
              this.writeHtml(v);
            });
        });


    },
    writeHtml(file){
        let html = `
        <a class="gall__item example-image-link"   href="${file}" data-lightbox="example-set" data-title="Click the right half of the image to move forward.">
                        <img class="example-image" src="${file}" alt="">
                   </a>`;
        $(`.modalphotos`).prepend(html);

    },
    async getImage(folder,file){
        const result = await $.ajax({
            type: 'POST',
            url: `/leads/photo`,
            data: {
                _token: $('[name="_token"]').val(),
                folder:folder,
                file:file
            },
        })
        return result;
    },

}
imageLoad.init();
