jQuery(function () {
    $("#btn-submit-entry").on('click', function (event) {
        const costCenter = $("input#id_cost_center").val();
        if (costCenter == '' || costCenter == 0) {
            $('div#generic-message-modal').modal('show');

            $('h4.modal-title').html("Aviso!");
            $('.modal-body').html("Você deve selecionar um Centro de Custo!");
            $('button#btn-confirm').html("Ok");
            $('button#btn-confirm').removeClass(['btn-danger', 'btn-success', 'btn-warning', 'btn-info', 'btn-primary']);
            $('button#btn-confirm').addClass('btn-primary');

            event.preventDefault();
        }

        const status = $("#status").val();
        if (status == 0) {
            const itemId = $("#itemID").val();

            $.post({
                url: url + "ajax/ajax/getAndFilterAllBillReceive/",
                dataType: 'json',
                data: { status: 1, id_bill_receive: itemId },
                async: false,

                success: function (response) {
                    const { error, installments } = response;

                    if (!error) {

                        const paidInstallments = installments.filter(p => {
                            return p.status_payment == 2;
                        });

                        if (paidInstallments) {
                            $('div#generic-message-modal').modal('show');

                            $('h4.modal-title').html("Aviso!");
                            $('.modal-body').html("Você tem parcelas <strong>PAGAS</strong> neste lançamento, ao inativar todas as parcelas serão <strong>Canceladas</strong>!<br> Deseja realmente inativar essa lançamento?");
                            $('button#btn-confirm').html("Inativar");
                            $('button#btn-confirm').removeClass(['btn-danger', 'btn-success', 'btn-warning', 'btn-info', 'btn-primary']);
                            $('button#btn-confirm').addClass('btn-danger');

                            event.preventDefault();

                            $('#generic-message-modal .modal-dialog .modal-footer button#btn-confirm').on("click", function () {
                                $('form#form-edit-entry').submit();
                            })
                        }
                    }
                }
            });
        }
    });

});
