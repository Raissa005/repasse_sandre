jQuery(function () {

    $(".btn-attendance-status").on("click", function () {
        $('#attendance-status-modal').modal('show');

        const id = $(this).attr('id');

        $("#attendance-status-modal input#id_status").val(id);
    })
});
