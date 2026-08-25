jQuery(function () {
    $('#btnSave').click(function (event) {
        var comment = $('#comment').val().trim();
        var statusCheck = $('#statusCheck').val();
        var statusCheckId = $('#statusCheckId').val();

        if (comment === '' && statusCheck == statusCheckId) {
            event.preventDefault();

            Toast.fire({
                icon: 'warning',
                title: 'O campo de comentário não pode estar vazio.'
            });
        }
    });

    $('#statusCheck').on('change', function () {
        var statusCheck = $(this).val();
        var statusCheckId = $('#statusCheckId').val();
        var existInstallment = $('#existInstallment').val();

        if (statusCheck == 1 && statusCheck != statusCheckId) {

            $('#divComment').removeAttr('hidden')
        }

        if (statusCheck == 2 && statusCheck != statusCheckId) {
            $.ajax({
                url: url + 'ajax/CheckControl/getAccountsById',
                method: 'POST',
                dataType: 'json',
                data: {
                    checkId: $('#checkId').val()
                },
                ansyc: false,

                success: function (response) {
                    const { error, message, check } = response;

                    if (!error) {
                        $('#divAccount').show();
                        $('#divComment').attr('hidden', true);
                    } else {
                        $('#statusCheck').select2().val(check.status_check).trigger('change');

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
        } else {

            $('#divAccount').hide();
        }

        if (statusCheck == 4 && !existInstallment) {
            $('#add-installment-modal').modal('show');

            $('#btnSubmit').on('click', function () {
                const price = $("#price").val();
                const checkId = $("#checkId").val();
                const dueDate = $("#dueDate").val();
                const formPaymentId = $("#formPaymentId").val();
                const costCenter = $("input#id_cost_center").val();
                const checkForwardedBy = $("#checkForwardedBy").val();
                const checkBillsToPayId = $("#checkBillsToPayId").val();
                const checkBillReceiveId = $("#checkBillReceiveId").val();

                if (costCenter == '' || costCenter == 0) {
                    Toast.fire({
                        icon: 'warning',
                        title: "Selecione um Centro de Custo para continuar!"
                    });

                    return;
                }

                if (dueDate == '' || price == '' || formPaymentId == '') {
                    Toast.fire({
                        icon: 'warning',
                        title: 'Por favor, preencha todos os campos obrigatórios.'
                    });

                    return;
                }

                $.ajax({
                    url: url + "ajax/CheckControl/addNewInstallment",
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        price: price,
                        checkId: checkId,
                        dueDate: dueDate,
                        costCenter: costCenter,
                        formPaymentId: formPaymentId,
                        checkForwardedBy: checkForwardedBy,
                        checkBillsToPayId: checkBillsToPayId,
                        checkBillReceiveId: checkBillReceiveId
                    },
                    ansyc: false,

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
                                icon: 'error',
                                title: message
                            });
                        }
                    },

                    error: function (xhr, status, error) {
                        console.error('Erro na requisição AJAX: ' + error);
                    }
                });
            });
        } else if (statusCheck == 4 && existInstallment) {
            var item = $('li[data-id="' + existInstallment + '"]');
            var windowHeight = $(window).height();
            var scrollTop = $(window).scrollTop();
            var itemTop = item.offset().top;
            var itemHeight = item.outerHeight();
            var scrollTo = itemTop - (windowHeight / 2) + (itemHeight / 2) - scrollTop;

            $('html, body').animate({
                scrollTop: scrollTo
            }, 500);

            Toast.fire({
                icon: 'warning',
                title: 'Já exite uma parcela em aberto para este cheque.'
            });
        }
    });
});