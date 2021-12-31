$(".t-row:odd").each(function () {
    $(this).addClass('bg-gray');
});

$('.btn-show').click(function () {
    $(this).toggleClass('active');
    $(this).parent().toggleClass('active');
    let tabid = $(this).data("id");
    $('#' + tabid).slideToggle();

});
