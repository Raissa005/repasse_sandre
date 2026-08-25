jQuery(function () {
    $("#btnSubmitAddBillsToPay").on('click', function (event) {
        const dueDate = $('#dueDate').val();
        const purchaseId = $('#purchaseId').val();
        const competence = $('#competence').val();
        const price = unmaskMoney($('#price').val());
        const formPaymentId = $('#formPaymentId').val();
        const valueReference = $('#valueReference').val();
        const costCenter = $("input#id_cost_center").val();
        const commission = $("#commissionTrue").val() ?? 0;
        const purchaseBrokerId = $('#purchaseBrokerId').val();
        const numberOfInstallments = $('#numberOfInstallments').val();

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

        if (dueDate == '' || purchaseId == '' || competence == '' || price == '' || formPaymentId == '' || valueReference == '' || numberOfInstallments == '' || (commission == 1 && purchaseBrokerId == '')) {
            Toast.fire({
                icon: 'warning',
                title: 'Por favor, preencha todos os campos obrigatórios.'
            });

            event.preventDefault();
            return;
        }

        $.ajax({
            url: url + 'ajax/PurchaseRequests/handleSubmitAddBillsToPay',
            method: 'POST',
            dataType: 'json',
            data: {
                price: price,
                dueDate: dueDate,
                commission : commission,
                competence: competence,
                costCenter: costCenter,
                purchaseId: purchaseId,
                formPaymentId: formPaymentId,
                valueReference: valueReference,
                purchaseBrokerId: purchaseBrokerId,
                numberOfInstallments: numberOfInstallments
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
            url: url + 'ajax/PurchaseRequests/addBillsToPayInstallment',
            method: 'POST',
            dataType: 'json',
            data: {
                dueDate: $('#due_date').val(),
                purchaseId: $('#purchaseId').val(),
                description: $('#description').val(),
                costCenter: $('#cost_center').val(),
                formPaymentId: $('#id_form_of_payment').val(),
                valueInstallments: unmaskMoney($('#value_of_installments').val()),
                commission: $("#commissionTrue").val() ?? 0,
                purchaseBrokerId: $("#purchaseBrokerId").val()
            },

            success: function (response) {
                const { error, message, lastInstallment, billsToPayInstallment, totalInstallments } = response;

                if (!error) {
                    $('#price').val(totalInstallments);
                    $('#numberOfInstallments').val(lastInstallment);

                    var newRow = '<tr>' +
                        '<td class="text-center" style="vertical-align: middle;">' + billsToPayInstallment.id + '</td>' +
                        '<td class="text-center" style="vertical-align: middle;">' + billsToPayInstallment.number_portion + '/' + lastInstallment + '</td>' +
                        '<td class="text-center" style="vertical-align: middle;">' + billsToPayInstallment.due_date + '</td>' +
                        '<td style="vertical-align: middle;">' + billsToPayInstallment.customer_name + '</td>' +
                        '<td class="text-center" style="vertical-align: middle;">' + billsToPayInstallment.cost_center_name + '</td>' +
                        '<td class="text-center" style="vertical-align: middle;">' + billsToPayInstallment.form_of_payment_name + '</td>' +
                        '<td class="text-center" style="vertical-align: middle;">' + (billsToPayInstallment.status_payment == 2 ? billsToPayInstallment.amount_paid : billsToPayInstallment.value_of_installments) + '</td>' +
                        '<td class="text-center" style="vertical-align: middle;">' +
                        '<span class="label label-' + billsToPayInstallment.bgtr + '">' + billsToPayInstallment.label + '</span>' +
                        '</td>' +
                        '<td class="text-center" style="vertical-align: middle;">' +
                        '<a title="Acessar Parcela" class="btn btn-sm btn-primary" href="' + URL + 'bills-to-pay-installment/editItem/' + billsToPayInstallment.id + '" target="_blank"><i class="fas fa-file-invoice"></i></a>' +
                        '</td>' +
                        '</tr>';

                    $('#installmentsPurchaseTableTbody').append(newRow);

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
