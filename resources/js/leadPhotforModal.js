const imageUpload = {
    dt: new DataTransfer(),
    init() {
        $(`#images`).on('click', `.page-add-file`, function (e) {
            $(`#uploader`).trigger('click');
        });
        $(`#uploader`).on('change',{self:this}, function (e) {
            let self = e.data.self;
            for (let i = 0; i < this.files.length; i++) {
                let file = this.files.item(i);
                self.dt.items.add(file);
                self.writeHtml(file);
            }
            self.updateBlopFiles(self.dt.files);

        });
     
    },
    updateBlopFiles(files) {
        $(`#uploader`).prop('files', files);
    },
    writeHtml(file){

        let html = `<li class="gall__item" data-src="./assets/image/gall.jpg">
                        <img src="${file}" alt="">
                   </li>`;
        $(`.modalphotos`).append(html);

    },
    async pay_to_partner(folder){
        const result = await $.ajax({
            type: 'GET',
            url: `/lead/photo/${folder}`
        })
        return result;
    }

}
imageUpload.init();
