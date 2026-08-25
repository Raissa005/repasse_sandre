$('.btn-export-item').on("click", function () {
    $('#export-item-modal').modal('show');

    $("#export").on("change", function () {
        $('.form-export-item').attr('action', $(this).val());
    }).trigger("change");
});