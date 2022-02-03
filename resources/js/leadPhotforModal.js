

const imageLoad = {
    image_names:[],
    folder:'',
    init() {

        $(`.gall-upload`).on('click', function (e) {
            $(`#pictures`).trigger('click');
        });
        $(`.leadPoto`).on('click', {self:this}, function (e) {
            $('.modalphotos').find('*').not('.gall-upload').remove();
            let self = e.data.self;
            self.image_names = $(this).data('image-names').split('||');
            self.folder = $(this).data('lead-folder');
            self.getImages();
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
    }

}
imageLoad.init();

