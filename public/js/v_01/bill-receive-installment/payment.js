jQuery(async () => {
    $("#btn-pay").on('click', () => {
        $("#box-pay").fadeIn(250);
        $("#btn-pay").fadeOut(250);

        $("#id_form_of_payment").on("change", async function () {
            const statusPayment = $('#payment_status_id').val();
            const formPaymentId = $(this).val();

            $(".amount_paid").show()
            $(".input_amount_paid").hide();
            const valueInstallments = $("#value_installment").val();

            switch (formPaymentId) {
                case '5':
                /**Pix */
                case '6':
                    /**Dinheiro */
                    $(".own_check, .bank, .account, .owner_check, .cpfcnpj_check, .agency, .number_account, .number_check, .customerCheck").hide();
                    $("#own_check, #bank, #account, #owner_check, #cpfcnpj_check, #agency, #number_account, #number_check").removeAttr("required").attr("disabled", true);

                    if (statusPayment == 1) {
                        /**Aguardando pagamento */
                        $("#amount_paid").removeAttr("disabled").attr("required", true).val(valueInstallments);
                    } else if (statusPayment == 2) {
                        /**Pago */
                        $("#amount_paid").removeAttr("required").attr("disabled", true);
                    }

                    break;
                case '1':
                /**Boleto */
                case '2':
                /**Transferência Bancária */
                case '7':
                /**Cartão de Crédito */
                case '8':
                    /**Cartão de Débito */

                    $(".own_check, .bank, .owner_check, .cpfcnpj_check, .number_check, .customerCheck").hide();
                    $("#bank, #owner_check, #cpfcnpj_check, #number_check, #agency, #number_account").removeAttr("required").attr("disabled", true);

                    $(".account, .agency, .number_account").show();

                    if (statusPayment == 1) {
                        /**Aguardando pagamento */
                        $("#account").removeAttr("disabled").attr("required", true);
                        $("#amount_paid").removeAttr("disabled").attr("required", true).val(valueInstallments);
                    } else if (statusPayment == 2) {
                        /**Pago */
                        $("#account").removeAttr("required").attr("disabled", true);
                        $("#amount_paid").removeAttr("required").attr("disabled", true);
                    }

                    $("[for='agency'] span, [for='number_account'] span").html("");

                    $("#account").on("change", function () {
                        const accountId = $(this).val();
                        $.post({
                            url: url + "ajax/global/getGenericoById/",
                            dataType: 'json',
                            data: { table: 'bank_accounts', id: accountId },
                            async: false,

                            success: function (response) {
                                const { error, message, data } = response;

                                if (!error) {
                                    $("#agency").val(data.agency);
                                    $("#number_account").val(data.account_number);
                                }
                            }
                        });
                    }).trigger("change");

                    break;

                case '3':
                    /**Cheque */

                    $(".own_check").show();

                    if (statusPayment == 1) {
                        /**Aguardando pagamento */
                        $("#amount_paid").removeAttr("disabled").attr("required", true).val(valueInstallments);

                        $(".account").hide();
                        $("#account").removeAttr("required").attr("disabled", true);

                        $(".bank, .owner_check, .cpfcnpj_check, .agency, .number_account, .number_check, .due_date, .customerCheck").show();
                        $("#bank, #owner_check, #cpfcnpj_check, #number_check").removeAttr("disabled").attr("required", true);

                        $("#agency, #number_account").removeAttr("disabled").attr("required", true).val("");
                        $("[for='agency'] span, [for='number_account'] span").html("*");
                    } else if (statusPayment == 2) {
                        /**Pago */
                        $("#amount_paid").removeAttr("required").attr("disabled", true);

                        if ($("#own_check_yes").prop("checked")) {

                            $(".bank, .owner_check, .cpfcnpj_check, .customerCheck").hide();
                            $("#bank, #owner_check, #cpfcnpj_check, #agency, #number_account").removeAttr("required").attr("disabled", true);

                            $(".account, .agency, .number_account, .number_check").show();
                            $("#account, #number_check").removeAttr("required").attr("disabled", true);

                            $("[for='agency'] span, [for='number_account'] span").html("");

                            $("#account").on("change", () => {
                                const accountId = $(this).val();

                                $.post({
                                    url: url + "ajax/global/getGenericoById/",
                                    dataType: 'json',
                                    data: { table: 'bank_accounts', id: accountId },
                                    async: false,

                                    success: function (response) {
                                        const { error, message, data } = response;

                                        if (!error) {
                                            $("#agency").val(data.agency);
                                            $("#number_account").val(data.account_number);
                                        }
                                    }
                                });
                            }).trigger("change");

                        } else if ($("#own_check_no").prop("checked")) {

                            $(".account").hide();
                            $("#account").removeAttr("required").attr("disabled", true);

                            $(".bank, .owner_check, .cpfcnpj_check, .agency, .number_account, .number_check, .due_date").show();
                            $("#bank, #owner_check, #cpfcnpj_check, #number_check").removeAttr("required").attr("disabled", true);

                            $("#agency, #number_account").removeAttr("required").attr("disabled", true);
                            $("[for='agency'] span, [for='number_account'] span").html("");
                        }

                    }

                    break;

                case '9':
                    /**Crédito Fornecedor */
                    $(" .own_check, .bank, .account, .owner_check, .cpfcnpj_check, .agency, .number_account, .number_check, .customerCheck").hide();
                    $("#bank, #account, #owner_check, #cpfcnpj_check, #agency, #number_account, #number_check").removeAttr("required").attr("disabled", true);
                    $("[for='agency'] span, [for='number_account'] span").html("");

                    if (statusPayment == 1) {
                        /**Aguardando pagamento */
                        $(".amount_paid").hide();
                        $(".input_amount_paid").show();
                        $("#amount_paid").removeAttr("disabled").attr("required", true);

                        const customerId = $("#customerId").val();
                        $("#input_amount_paid").val($("#value_installment").val());

                        $("#input_amount_paid").on('change', () => {
                            const value = $("#input_amount_paid").val();
                            $("#amount_paid").val(value);
                        });

                    } else if (statusPayment == 2) {
                        /**Pago */
                        $("#amount_paid").removeAttr("required").attr("disabled", true);
                    }

                    break;

                default:
                    $(".amount_paid").show();
                    $(".own_check, .bank, .account, .owner_check, .cpfcnpj_check, .agency, .number_account, .number_check, .input_amount_paid, .customerCheck").hide();
                    $("[for='agency'] span, [for='number_account'] span").html("");

                    break;
            }
        }).trigger("change");
    });

    $("#btn-submit-pay").on("click", async function (event) {
        const itemId = $("#itemId").val();
        const numberCheck = $('#number_check').val();
        let amountPaid = unmaskMoney($("#amount_paid").val());
        const balance = $("#customer_balance").data('value');

        if (numberCheck <= 0 && numberCheck != '') {
            Toast.fire({
                icon: 'error',
                title: 'Número do cheque não pode ser igual a "0"!'
            });

            event.preventDefault();
        }

        if (amountPaid != '' && amountPaid != 0) {
            if ($("#id_form_of_payment").val() == 9) {
                if (amountPaid > balance) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Saldo insuficiente',
                        confirmButtonColor: '#367FA9',
                        text: 'Cliente não tem saldo suficiente para completar o pagamento da parcela!',
                    })
                    event.preventDefault();
                    return;
                }
            }

            if ($("#id_form_of_payment").val() == 3 && $("#number_paymentOrder").val() == "") {

                $('#generic-form-modal').modal('show');

                $('h4.modal-title').html("Aviso");
                $('.modal-body').html("Você deve preencher o Nº Cheque");
                $('button#btn-confirm').removeClass(['btn-danger', 'btn-success', 'btn-warning', 'btn-info', 'btn-primary']);
                $('button#btn-confirm').html("Ok");
                $('button#btn-confirm').addClass('btn-primary');
                event.preventDefault();

            } else {
                /**Parcela */
                
                $.post({
                    url: url + "ajax/global/getGenericoById/",
                    dataType: 'json',
                    data: { table: 'bill_receive_installment', id: itemId },
                    async: false,

                    success: function (response) {
                        const { error, message, data } = response;

                        if (!error) {
                            $("#payment-transaction").val("0");

                            if (Number(data.value_installment) != Number(amountPaid)) {
                                $("#payment-transaction").val("-1");
                                let html = "<div>O valor da Parcela é de " + formatMoney(data.value_installment) + " e o valor do pagamento foi de " + formatMoney(amountPaid) + ".</div>";
                                html += "<div>Qual operação será realizada?</div>"
                                if (Number(data.value_installment) > Number(amountPaid)) {
                                    html += `<div class="radio">
                                                <label class="radio" style="margin-bottom: 0.625rem;">
                                                    <input type="radio" class="radio option-transaction-click" name="option-payment" id="option-payment-1" value="1">Gerar uma NOVA parcela no o valor de ` + formatMoney(data.value_installment - amountPaid) + `.
                                                </label>
                                                <label class="radio" style="margin-bottom: 0.625rem;">
                                                    <input type="radio" class="radio option-transaction-click" name="option-payment" id="option-payment-2" value="2">Aplicar um Desconto de ` + formatMoney(data.value_installment - amountPaid) + `.
                                                </label>
                                                <label class="radio">
                                                    <input type="radio" class="radio option-transaction-click" name="option-payment" id="option-payment-3" value="0">Ignorar.
                                                </label>
                                            </div>`;
                                } else {
                                    html += `<div class="radio">
                                                <label class="radio" style="margin-bottom: 0.625rem;">
                                                    <input type="radio" class="radio option-transaction-click" name="option-payment" id="option-payment-1" value="3">Deixar como crédito do cliente o valor de ` + formatMoney(amountPaid - data.value_installment) + `.
                                                </label>
                                                <label class="radio" style="margin-bottom: 0.625rem;">
                                                    <input type="radio" class="radio option-transaction-click" name="option-payment" id="option-payment-2" value="4">Aplicar como Juros de conta o valor de ` + formatMoney(amountPaid - data.value_installment) + `.
                                                </label>
                                                <label class="radio">
                                                    <input type="radio" class="radio option-transaction-click" name="option-payment" id="option-payment-3" value="0">Ignorar.
                                                </label>
                                            </div>`;
                                }

                                $('#generic-form-modal').modal('show');

                                $('h4.modal-title').html("Selecione a operação");
                                $('.modal-body').html(html);
                                $('button#btn-confirm').removeClass(['btn-danger', 'btn-success', 'btn-warning', 'btn-info', 'btn-primary']);
                                $('button#btn-confirm').html("Confirmar");
                                $('button#btn-confirm').addClass('btn-primary').attr('disabled', "");

                                /**Selecionou a operação */
                                $(".option-transaction-click").on("click", function () {
                                    $("#payment-transaction").val($(this).val());
                                });

                                $(".option-transaction-click").on("click", function () {
                                    $('button#btn-confirm').removeAttr("disabled");

                                    $('button#btn-confirm').on("click", function () {
                                        $('#form-payment').submit();
                                    })
                                })

                                if ($("#payment-transaction").val() == "-1") {
                                    event.preventDefault();
                                }
                            }
                        }
                    }
                });
            }
        } else {
            $('#generic-form-modal').modal('show');

            $('h4.modal-title').html("Aviso");
            $('.modal-body').html("Você deve preencher o valor do pagamento");
            $('button#btn-confirm').removeClass(['btn-danger', 'btn-success', 'btn-warning', 'btn-info', 'btn-primary']);
            $('button#btn-confirm').html("Ok");
            $('button#btn-confirm').addClass('btn-primary');
            event.preventDefault();
        }

        $('#generic-form-modal').on('hidden.bs.modal', function () {
            location.reload();

            event.preventDefault();
            
            return;
        });
    });
});

$(document).on('click', '.modal-page-link', function () {
    const page = parseInt($(this).attr('page'));
    $('#pageCustomerCheckJax').val(page);
});

$(document).on('click', '#customerCheck, #searchCustomerCheck, .modal-page-link', async function () {
    const rows = 10;

    let customerId = $('#customer_id').val();
    let searchName = $('#searchNameCustomerCheck').val();
    const page = parseInt($('#pageCustomerCheckJax').val());

    const responseCustomer = await post('CheckControl/getCustomersChecks', {
        page: page,
        limit: rows,
        name: searchName,
        customerId: customerId
    });

    $('#customerCheckTable').html(
        responseCustomer.data.map((customer) => {
            return `<tr>
                        <td class="text-center">${customer.number_check}</td>
                        <td>${customer.owner_check}</td>
                        <td class="text-center" cpfcnpj>${customer.cpf_cnpj_check}</td>
                        <td class="text-center">${customer.bank_name}</td>
                        <td>${formatMoney(customer.value)}</td>
                        <td class="text-center">
                        <span class='label label-${customer.due_date.color}'>${customer.due_date.value}</span>
                        </td>
                        <td class="text-center">
                            <a target="_blank" href="${url + 'check-control/editItem/' + customer.id}" class="btn btn-warning btn-sm" title="Visualizar"><i class="fa fa-eye"></i></a>
                            <button type="button" class="btn btn-success btn-sm check" checkId="${customer.id}" title="Selecionar"><i class="fa fa-check"></i></button>
                        </td>
                    </tr>`;
        })
    );

    $('[cpfcnpj]').inputmask({ mask: ['999.999.999-99', '99.999.999/9999-99',], keepStatic: true });

    let pagination = page > 1 ? `<li class="page-item"><a class="page-link modal-page-link" page="${page - 1}">&laquo;</a></li>` : "";

    for (let index = responseCustomer.pagination.min; index <= responseCustomer.pagination.max; index++) {
        pagination += `<li class="page-item ${page == index ? "active" : ""}"><a class="page-link modal-page-link" page="${index}">${index}</a></li>`;
    }

    pagination += page < responseCustomer.pagination.max ? `<li class="page-item"><a class="page-link modal-page-link" page="${page + 1}">&raquo;</a></li>` : '';

    $('.modal-pagination-seller-customer').html(pagination);

    $('.check').on('click', function () {
        const checkId = $(this).attr('checkId');

        $.ajax({
            url: url + 'ajax/CheckControl/getCheckById/',
            dataType: 'json',
            method: 'POST',
            data: {
                checkId: checkId
            },
            async: false,

            success: function (response) {
                const { error, message, check } = response;

                if (!error) {
                    $('#checkId').val(checkId);

                    $('#bank').prop('disabled', true);
                    $('#bank').val(check.id_bank).select2();

                    $('#owner_check').prop('readonly', true);
                    $('#owner_check').val(check.owner_check);

                    $('#cpfcnpj_check').prop('readonly', true);
                    $('#cpfcnpj_check').val(check.cpf_cnpj_check);

                    $('#agency').prop('readonly', true);
                    $('#agency').val(check.agency);

                    $('#number_account').prop('readonly', true);
                    $('#number_account').val(check.number_account);

                    $('#number_check').prop('readonly', true);
                    $('#number_check').val(check.number_check);

                    $('#due_date_check').prop('readonly', true);
                    $('#due_date_check').val(check.due_date);

                    $('#amount_paid').prop('readonly', true);
                    $('#amount_paid').val(formatMoney(check.value));

                    $('#customerCheckModal').modal('hide');
                } else {
                    Toast.fire({
                        icon: 'warning',
                        title: message
                    });
                }
            },
        });
    });
});
