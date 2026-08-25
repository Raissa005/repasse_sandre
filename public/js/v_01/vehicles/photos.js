$(document).on('click', '.img-carousel', function () {
    const img = $(this).data('img');
    $('.carousel').carousel({
        interval: false
    });

    $("ol.carousel-indicators li").removeClass('active');
    $("div.carousel-inner div").removeClass('active');

    $(`.carousel-indicators > li.${img}`).addClass('active');
    $(`.carousel-inner > div.${img}`).addClass('active');
    $("#image-item-modal").modal('show');
});
