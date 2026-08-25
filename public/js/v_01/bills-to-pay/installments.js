jQuery(function () {
    let arrIds = [];
    $(".btn-cancel-installment").on('click', function () {
        let val = parseInt($(this).val());

        if ($(this).hasClass('btn-danger')) {
            arrIds.push(val);
            $(this).attr('title', 'Ativar Parcela');
            $(this).removeClass('btn-danger');
            $(this).addClass('btn-success');
            $(this).html('<i class="fa fa-check"></i>');
        } else {
            arrIds.splice(arrIds.indexOf(val), 1);
            $(this).attr('title', 'Cancelar Parcela');
            $(this).removeClass('btn-success');
            $(this).addClass('btn-danger');
            $(this).html('<i class="fa fa-times"></i>');
        }

        $("#id_installments").val(arrIds);

        if (arrIds.length > 0) {
            $(".btn-cancel-installments").removeClass('hidden');

            let plural = ``;
            (arrIds.length > 1) ? plural = `s` : "";

            $(".btn-cancel-installments").html(`Cancelar ${arrIds.length} parcela` + plural);
            return;
        }

        $(".btn-cancel-installments").addClass('hidden');
    });

    $(".btn-cancel-installments").on('click', function () {
        $("#form-cancel-installments").submit();
    });
});
