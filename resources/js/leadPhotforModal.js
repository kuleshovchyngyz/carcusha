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
        $(`#images`).on('click', `.transfer__delete`, {self:this}, function (e) {
            let self = e.data.self;
            let parent = $(this).parents(`.transfer`);
            let i = parent.index() - (($(`.page-file-item:not(.transfer)`).length > 0) ? ($(`.page-file-item:not(.transfer)`).length +1) : 1);

            self.dt.items.remove(i);
            parent.remove();
            self.updateBlopFiles(self.dt.files);
        } );
    },
    updateBlopFiles(files) {
        $(`#uploader`).prop('files', files);
    },
    writeHtml(file){

        let html = `<div class="page-file-item transfer">
                    <img src="${URL.createObjectURL(file)}" alt="">
                    <div class="page-file__option">
                        <button type="button" class="page-file__zoom"></button>
                        <button type="button" class="page-file__delete transfer__delete"></button>
                    </div>
                </div>`;
        $(`#images`).append(html);

    }

}
imageUpload.init();
