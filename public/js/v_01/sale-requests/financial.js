
jQuery(function () {
    $("#btnSubmitAddBillReceive").on('click', function (event) {
        const price = $('#price').val();
        const dueDate = $('#dueDate').val();
        const saleRequestId = $('#saleRequestId').val();
        const formPaymentId = $('#formPaymentId').val();
        const valueReference = $('#valueReference').val();
        const costCenter = $("input#id_cost_center").val();
        const numberOfInstallments = $('#numberOfInstallments').val();
        const commission = $('#commissionTrue').val();
        var saleBrokerId = $('#purchaseBrokerId').val();

        if (costCenter == '' || costCenter == 0) {
            $('div#generic-message-modal').modal('show');
            $('h4.modal-title').html("Aviso!");
            $('.modal-body').html("Você deve selecionar um Centro de Custo!");
            $('button#btn-confirm').html("Ok");
            $('button#btn-confirm').removeClass(['btn-danger', 'btn-success', 'btn-warning', 'btn-info', 'btn-primary']);
            $('button#btn-confirm').addClass('btn-primary');

            event.preventDefault();
            return;
        }

        if(saleBrokerId == ''){
            saleBrokerId = $('#saleBrokerId').val();
        }

        if (dueDate == '' || saleRequestId == '' || price == '' || formPaymentId == '' || valueReference == '' || numberOfInstallments == '') {
            Toast.fire({
                icon: 'warning',
                title: 'Por favor, preencha todos os campos obrigatórios.'
            });

            event.preventDefault();
            return;
        }

        $.ajax({
            url: url + 'ajax/SaleRequests/handleSubmitAddBillReceive',
            method: 'POST',
            dataType: 'json',
            data: {
                price: unmaskMoney(price),
                dueDate: dueDate,
                costCenter: costCenter,
                saleRequestId: saleRequestId,
                formPaymentId: formPaymentId,
                valueReference: valueReference,
                saleBrokerId: saleBrokerId,
                numberOfInstallments: numberOfInstallments,
                commission: commission
            },

            success: function (response) {
                const { error, message } = response;

                if (!error) {
                    Toast.fire({
                        icon: 'success',
                        title: message
                    });

                    setTimeout(function () {
                        location.reload();
                    }, 1000);
                } else {
                    Toast.fire({
                        icon: 'warning',
                        title: message
                    });
                }
            }
        });
    });

    $('#btn-submit').on('click', function () {
        if (!validateRequiredFields()) {
            Toast.fire({
                icon: 'warning',
                title: 'Por favor, preencha todos os campos obrigatórios.'
            });
            return;
        }

        $.ajax({
            url: url + 'ajax/SaleRequests/addBillReceiveInstallment',
            method: 'POST',
            dataType: 'json',
            data: {
                dueDate: $('#due_date').val(),
                saleRequestId: $('#saleRequestId').val(),
                description: $('#description').val(),
                statusPayment: $('#status_payment').val(),
                formPaymentId: $('#id_form_of_payment').val(),
                valueInstallment: unmaskMoney($('#value_installment').val()),
                commission: $('#commissionTrue').val(),
                saleBrokerId: $('#saleBrokerId').val()
            },

            success: function (response) {
                const { error, message, lastInstallment, billReceiveInstallment, totalInstallments } = response;

                if (!error) {
                    $('#price').val(totalInstallments);
                    $('#numberOfInstallments').val(lastInstallment);

                    var newRow = '<tr>' +
                        '<td class="text-center" style="vertical-align: middle;">' + billReceiveInstallment.id + '</td>' +
                        '<td class="text-center" style="vertical-align: middle;">' + billReceiveInstallment.number_portion + '/' + lastInstallment + '</td>' +
                        '<td class="text-center" style="vertical-align: middle;">' + billReceiveInstallment.due_date + '</td>' +
                        '<td style="vertical-align: middle;">' + billReceiveInstallment.customer_name + '</td>' +
                        '<td class="text-center" style="vertical-align: middle;">' + billReceiveInstallment.cost_center_name + '</td>' +
                        '<td class="text-center" style="vertical-align: middle;">' + billReceiveInstallment.form_of_payment_name + '</td>' +
                        '<td class="text-center" style="vertical-align: middle;">' + (billReceiveInstallment.status_payment == 2 ? billReceiveInstallment.amount_paid : billReceiveInstallment.value_installment) + '</td>' +
                        '<td class="text-center" style="vertical-align: middle;">' +
                        '<span class="label label-' + billReceiveInstallment.bgtr + '">' + billReceiveInstallment.label + '</span>' +
                        '</td>' +
                        '<td class="text-center" style="vertical-align: middle;">' +
                        '<a title="Acessar Parcela" class="btn btn-sm btn-primary" href="' + URL + 'bill-receive-installment/edit/' + billReceiveInstallment.id + '" target="_blank"><i class="fas fa-file-invoice"></i></a>' +
                        '</td>' +
                        '</tr>';

                    $(newRow).insertBefore('.totalLine');

                    $('#add-installment-modal').modal('hide');

                    Toast.fire({
                        icon: 'success',
                        title: message
                    });
                } else {
                    Toast.fire({
                        icon: 'warning',
                        title: message
                    });
                }
            },
            error: function (xhr, status, error) {
                console.error('Erro na requisição AJAX: ' + error);
            }
        });
    });

    function validateRequiredFields() {
        var mandatoryFieldsFilledIn = true;

        $('.form-control[required]').each(function () {
            if ($(this).val() === '') {
                mandatoryFieldsFilledIn = false;
                return false;
            }
        });

        return mandatoryFieldsFilledIn;
    }
});
