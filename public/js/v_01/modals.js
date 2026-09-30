$('.btn-disable-item').on("click", function () {
    if ($(this).attr('disabled')) {
        return
    }

    $('#disable-item-modal').modal('show');

    const id = $(this).attr('id');
    const sendTo = $(this).attr('sendTo');
    const html = $(this).attr('bodyHtml');
    const page = $("#page").val();

    $('.form-disable-item').attr('action', url + sendTo + (id != undefined ? id : "") + (page != undefined ? "/" + page : ""));

    if (html) {
        $('.modal-body').html(html);
    }
});

$('.btn-enable-item').click(function () {
    $('#enable-item-modal').modal('show');

    const id = $(this).attr('id');
    const sendTo = $(this).attr('sendTo');
    const html = $(this).attr('bodyHtml');
    const page = $("#page").val();

    $('.form-enable-item').attr('action', url + sendTo + (id != undefined ? id : "") + (page != undefined ? "/" + page : ""));

    if (html) {
        $('.modal-body').html(html);
    }
});

$('.btn-generic-item').on("click", function () {
    $('#generic-item-modal').modal('show');

    const id = $(this).attr('id');
    const sendTo = $(this).attr('sendTo');
    const bodyHtml = $(this).attr('bodyHtml');
    const headerHtml = $(this).attr('headerHtml');
    const footerHtml = $(this).attr('footerHtml');
    const btnFooter = $(this).attr('btnFooter');
    const page = $("#page").val();

    $('.form-generic-item').attr('action', url + sendTo + (id != undefined ? id : "") + (page != undefined ? "/" + page : ""));

    if (headerHtml) {
        $('h4.modal-title').html(headerHtml);
    } else {
        $('h4.modal-title').html("Aviso!");
    }

    if (bodyHtml) {
        $('.modal-body').html(bodyHtml);
    } else {
        $('.modal-body').html("Você realmente deseja fazer isso?");
    }

    if (footerHtml) {
        $('button#btn-submit').html(footerHtml);
    } else {
        $('button#btn-submit').html("Confirmar");
    }

    if (btnFooter) {
        $('button#btn-submit').removeClass(['btn-danger', 'btn-success', 'btn-warning', 'btn-info', 'btn-primary']);
        $('button#btn-submit').addClass(btnFooter);
    }
});
