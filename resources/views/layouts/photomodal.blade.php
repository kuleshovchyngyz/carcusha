
<div class="modal fade show" id="gallModalCenter" tabindex="-1" aria-labelledby="gallModalCenterTitle" style="display: none;" aria-modal="true" role="dialog">
    @csrf
        <div class="modal-dialog modal-dialog-centered" style="max-width: 880px">
            <div class="modal-gall">
                <div class="modal-head">
                    <h5 class="modal-title modal__title__blue lead__name"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4.4107 4.4107C4.73614 4.08527 5.26378 4.08527 5.58921 4.4107L9.99996 8.82145L14.4107 4.4107C14.7361 4.08527 15.2638 4.08527 15.5892 4.4107C15.9147 4.73614 15.9147 5.26378 15.5892 5.58921L11.1785 9.99996L15.5892 14.4107C15.9147 14.7361 15.9147 15.2638 15.5892 15.5892C15.2638 15.9147 14.7361 15.9147 14.4107 15.5892L9.99996 11.1785L5.58921 15.5892C5.26378 15.9147 4.73614 15.9147 4.4107 15.5892C4.08527 15.2638 4.08527 14.7361 4.4107 14.4107L8.82145 9.99996L4.4107 5.58921C4.08527 5.26378 4.08527 4.73614 4.4107 4.4107Z" fill="#A4AFC1"></path>
                        </svg>
                    </button>
                </div>
                <ul class="gall__list gallery modalphotos" id="test">
                   <a class="gall-upload const">
                   </a>
                    <input type="file" class="const" name="image[]" id="pictures" multiple hidden>

                </ul>
                  <input type="hidden"  id="current_folder_id" value="">
                  <input type="hidden"  id="new_images" value="">

                <div class="text-center">
                    <button class="btn btn-blue" id="sendToBitrixButton" data-lead-id="0">Сохранить</button>
                </div>
            </div>
        </div>
    </div>
