jQuery(() => {
    $("#btn-pay").on('click', () => {
        $("#box-pay").fadeIn(250);
        $("#btn-pay").fadeOut(250);
    });

    $("#id_form_of_payment").on("change", async function () {
        const statusPayment = $('#status_payment').val();
        const formPaymentId = $(this).val();

        $(".amount_paid").show()
        $(".input_amount_paid").hide();
        const valueInstallments = $("#value_of_installments").val();

        const customerId = $("#customerId").val();

        const response = await post("customer/calculateCustomerCredit", {
            customer_id: customerId
        });

        if (response.data.credit <= 0) {
            $("#id_form_of_payment option[value='9']").remove();
        }

        switch (formPaymentId) {
            case '5':
            /**Pix */
            case '6':
                /**Dinheiro */
                $(".own_check, .bank, .account, .owner_check, .cpfcnpj_check, .agency, .number_account, .number_check, .due_date_check, .customerCheck, .btnAddCheck").hide();
                $("#own_check, #bank, #account, #owner_check, #cpfcnpj_check, #agency, #number_account, #number_check, #due_date_check").removeAttr("required").attr("disabled", true);

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

                $(".own_check, .bank, .owner_check, .cpfcnpj_check, .number_check, .due_date_check, .customerCheck, .btnAddCheck").hide();
                $("#bank, #owner_check, #cpfcnpj_check, #number_check, #agency, #number_account, #due_date_check").removeAttr("required").attr("disabled", true);

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
                if (statusPayment == 1) {
                    $(".own_check").show();

                    /**Aguardando pagamento */
                    $("#amount_paid").removeAttr("disabled").attr("required", true).val(valueInstallments);

                    $("#own_check_yes").trigger("click");

                    $("#own_check_yes").on("click", function () {
                        $(".bank, .owner_check, .cpfcnpj_check, .due_date_check, .customerCheck, .btnAddCheck").hide();
                        $("#bank, #owner_check, #cpfcnpj_check, #agency, #number_account, #due_date_check").removeAttr("required").attr("disabled", true);

                        $(".account, .agency, .number_account, .number_check").show();
                        $("#account, #number_check").removeAttr("disabled").attr("required", true);

                        $("[for='agency'] span, [for='number_account'] span").html("");

                        $("#account, #own_check_yes").on("change, click", () => {
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

                        $('#amount_paid').prop('readonly', false);
                        $('#number_check').prop('readonly', false);

                        $('.addCheck').attr('hidden', true);
                        $('#customerCheck').removeAttr('disabled');

                        updateTableVisibility();
                    }).trigger("click");

                    $("#own_check_no").on("click", function () {
                        $(".account").hide();
                        $("#account").removeAttr("required").attr("disabled", true);

                        $(".bank, .owner_check, .cpfcnpj_check, .agency, .number_account, .number_check, .due_date_check, .customerCheck, .btnAddCheck").show();
                        $("#bank, #owner_check, #cpfcnpj_check, #number_check, #due_date_check").removeAttr("disabled").attr("required", true);

                        $("#agency, #number_account").removeAttr("disabled").attr("required", true).val("");
                        $("[for='agency'] span, [for='number_account'] span").html("*");

                        $('#agency').val('');
                        $('#account').val('');
                        $('#checkId').val('');
                        $('#check_value').val('');
                        $('#owner_check').val('');
                        $('#amount_paid').val('');
                        $('#number_check').val('');
                        $('#cpfcnpj_check').val('');
                        $('#number_account').val('');
                        $('#due_date_check').val('');

                        $('#bank').prop('disabled', false);
                        $('#agency').prop('readonly', false);
                        $('#owner_check').prop('readonly', false);
                        $('#amount_paid').prop('readonly', false);
                        $('#number_check').prop('readonly', false);
                        $('#cpfcnpj_check').prop('readonly', false);
                        $('#number_account').prop('readonly', false);
                        $('#due_date_check').prop('readonly', false);

                        $('.addCheck').attr('hidden', true);
                        $('#customerCheck').removeAttr('disabled');

                        updateTableVisibility();
                    });


                } else if (statusPayment == 2) {
                    /**Pago */
                    $("#amount_paid").removeAttr("required").attr("disabled", true);

                    //     if ($("#own_check_yes").prop("checked")) {

                    //         $(".bank, .owner_check, .cpfcnpj_check, .due_date_check, .customerCheck, .btnAddCheck").hide();
                    //         $("#bank, #owner_check, #cpfcnpj_check, #agency, #number_account, #due_date_check").removeAttr("required").attr("disabled", true);

                    //         $(".account, .agency, .number_account, .number_check").show();
                    //         $("#account, #number_check").removeAttr("required").attr("disabled", true);

                    //         $("[for='agency'] span, [for='number_account'] span").html("");

                    //         $("#account").on("change", () => {
                    //             const accountId = $(this).val();

                    //             $.post({
                    //                 url: url + "ajax/global/getGenericoById/",
                    //                 dataType: 'json',
                    //                 data: { table: 'bank_accounts', id: accountId },
                    //                 async: false,

                    //                 success: function (response) {
                    //                     const { error, message, data } = response;

                    //                     if (!error) {
                    //                         $("#agency").val(data.agency);
                    //                         $("#number_account").val(data.account_number);
                    //                     }
                    //                 }
                    //             });
                    //         }).trigger("change");

                    //     } else if ($("#own_check_no").prop("checked")) {

                    //         $(".account").hide();
                    //         $("#account").removeAttr("required").attr("disabled", true);

                    //         $(".bank, .owner_check, .cpfcnpj_check, .agency, .number_account, .number_check, .customerCheck").show();
                    //         $("#bank, #owner_check, #cpfcnpj_check, #number_check").removeAttr("required").attr("disabled", true);

                    //         $("#agency, #number_account").removeAttr("required").attr("disabled", true);
                    //         $("[for='agency'] span, [for='number_account'] span").html("");
                    //     }
                }

                break;

            case '9':
                /**Crédito Fornecedor */
                $(".own_check, .bank, .account, .owner_check, .cpfcnpj_check, .agency, .number_account, .number_check, .due_date_check, .customerCheck, .btnAddCheck").hide();
                $("#bank, #account, #owner_check, #cpfcnpj_check, #agency, #number_account, #number_check, #due_date_check").removeAttr("required").attr("disabled", true);
                $("[for='agency'] span, [for='number_account'] span").html("");

                if (statusPayment == 1) {
                    /**Aguardando pagamento */
                    $(".amount_paid").hide();
                    $(".input_amount_paid").show();
                    $("#amount_paid").removeAttr("disabled").attr("required", true);

                    const customerId = $("#customerId").val();
                    const response = await post("customer/calculateCustomerCredit", {
                        customer_id: customerId
                    });

                    const creditInput = formatMoney(response.data.credit);
                    $("#input_amount_paid").val(creditInput);
                    $("#amount_paid").val(creditInput);

                } else if (statusPayment == 2) {
                    /**Pago */
                    $("#amount_paid").removeAttr("required").attr("disabled", true);
                }

                break;

            default:
                $(".amount_paid").show();
                $(".own_check, .bank, .account, .owner_check, .cpfcnpj_check, .agency, .number_account, .number_check, .input_amount_paid, .due_date_check, .customerCheck, .btnAddCheck").hide();
                $("#own_check, #bank, #account, #owner_check, #cpfcnpj_check, #agency, #number_account, #number_check, #due_date_check").removeAttr("required").attr("disabled", true);
                $("[for='agency'] span, [for='number_account'] span").html("");

                if (statusPayment == 2) {
                    /**Pago */
                    $("#amount_paid").removeAttr("required").attr("disabled", true);
                }

                break;
        }

        if (statusPayment != 3) {
            $('#agency').val('');
            $('#account').val('');
            $('#checkId').val('');
            $('#check_value').val('');
            $('#owner_check').val('');
            //$('#amount_paid').val('');
            $('#number_check').val('');
            $('#cpfcnpj_check').val('');
            $('#number_account').val('');
            $('#due_date_check').val('');

            $('#bank').prop('disabled', false);
            $('#agency').prop('readonly', false);
            $('#owner_check').prop('readonly', false);
            $('#amount_paid').prop('readonly', false);
            $('#number_check').prop('readonly', false);
            $('#cpfcnpj_check').prop('readonly', false);
            $('#number_account').prop('readonly', false);
            $('#due_date_check').prop('readonly', false);

            $('#tableSelectedChecks').empty();
            $('.addCheck').attr('hidden', true);
            $('#customerCheck').removeAttr('disabled');
            $('.divTableSelectedChecks').attr('hidden', true);

            updateTableVisibility();
        }
    }).trigger("change");

    $("#btn-submit-pay").on("click", function (event) {
        let amountPaid = $("#amount_paid").val();
        const itemId = $("#itemId").val();
        amountPaid = unmaskMoney(amountPaid);

        $('input[type="hidden"].dynamic-input').remove();

        $('#tableSelectedChecks tr').each(function (index, tr) {
            var numberCheck = $(tr).find('td').eq(0).text();

            if (numberCheck.toLowerCase() === 'total') {
                return true;
            }

            var checkId = $(tr).data('id');
            var bankId = $('#bankIdCheck').val();
            var agencyCheck = $('#agencyCheck').val();
            var ownerCheck = $(tr).find('td').eq(1).text();
            var cpfCnpjCheck = $(tr).find('td').eq(2).text();
            var value = unmaskMoney($(tr).find('td').eq(4).text());
            var numberAccountCheck = $('#numberAccountCheck').val();
            var dueDate = moment($(tr).find('td').eq(5).text().trim(), 'DD/MM/YYYY').format('YYYY-MM-DD');

            if (!isNaN(value)) {
                amountPaidByCheck += parseFloat(value);
            }

            var inputs = [
                { name: 'checks[' + index + '][id]', value: checkId },
                { name: 'checks[' + index + '][value]', value: value },
                { name: 'checks[' + index + '][id_bank]', value: bankId },
                { name: 'checks[' + index + '][due_date]', value: dueDate },
                { name: 'checks[' + index + '][agency]', value: agencyCheck },
                { name: 'checks[' + index + '][owner_check]', value: ownerCheck },
                { name: 'checks[' + index + '][number_check]', value: numberCheck },
                { name: 'checks[' + index + '][cpf_cnpj_check]', value: cpfCnpjCheck },
                { name: 'checks[' + index + '][number_account]', value: numberAccountCheck }
            ];

            inputs.forEach(function (input) {
                $('<input>').attr({
                    type: 'hidden',
                    name: input.name,
                    value: input.value,
                    class: 'dynamic-input'
                }).appendTo('#form-payment');
            });
        });

        if (amountPaid != '' && amountPaid != 0) {

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
                    data: { table: 'bills_to_pay_installments', id: itemId },
                    async: false,

                    success: function (response) {
                        const { error, message, data } = response;

                        if (!error) {
                            $("#payment-transaction").val("0");

                            if (Number(data.value_of_installments) != Number(amountPaid)) {
                                $("#payment-transaction").val("-1");
                                let html = "<div>O valor da Parcela é de " + formatMoney(data.value_of_installments) + " e o valor do pagamento foi de " + formatMoney(amountPaid) + ".</div>";
                                html += "<div>Qual operação será realizada?</div>"
                                if (Number(data.value_of_installments) > Number(amountPaid)) {
                                    html += `<div class="radio">
                                                <label class="radio" style="margin-bottom: 0.625rem;">
                                                    <input type="radio" class="radio option-transaction-click" name="option-payment" id="option-payment-1" value="1">Gerar uma NOVA parcela no o valor de ` + formatMoney(data.value_of_installments - amountPaid) + `.
                                                </label>
                                                <label class="radio">
                                                    <input type="radio" class="radio option-transaction-click" name="option-payment" id="option-payment-2" value="2">Aplicar um Desconto de ` + formatMoney(data.value_of_installments - amountPaid) + `.
                                                </label>
                                            </div>`;
                                } else {
                                    html += `<div class="radio">
                                                <label class="radio" style="margin-bottom: 0.625rem;">
                                                    <input type="radio" class="radio option-transaction-click" name="option-payment" id="option-payment-1" value="3">Deixar como crédito do cliente o valor de ` + formatMoney(amountPaid - data.value_of_installments) + `.
                                                </label>
                                                <label class="radio">
                                                    <input type="radio" class="radio option-transaction-click" name="option-payment" id="option-payment-2" value="4">Aplicar como Juros de conta o valor de ` + formatMoney(amountPaid - data.value_of_installments) + `.
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
    });

    // $('.modal-page-link').on('click', function () {
    //     const page = parseInt($(this).attr('page'));
    //     $('#pageCustomerCheckJax').val(page);
    // });

    let deleteItems = [];
    let selectedItems = [];
    let amountPaidByCheck = 0;

    $('#customerCheck, #searchCustomerCheck, .modal-page-link').on('click', async function () {
        const rows = 10;
        let searchName = $('#searchNameCustomerCheck').val();
        const page = parseInt($('#pageCustomerCheckJax').val());

        const responseCheck = await post('CheckControl/getAllChecks', {
            page: page,
            limit: rows,
            name: searchName,
            deleteItems: deleteItems
        });

        if (responseCheck.data.length <= 0) {
            $('#addSelecionados').addClass('hidden');
        }

        $('#customerCheckTable').html(
            responseCheck.data.map((check) => {
                const isChecked = selectedItems.includes(check.id.toString()) ? 'checked' : '';
                return `<tr>
                        <td class="text-center"><input type="checkbox" class="check-checkbox" value="${check.id}" ${isChecked}></td>
                        <td class="text-center">${check.number_check}</td>
                        <td>${check.owner_check}</td>
                        <td class="text-center" cpfcnpj>${check.cpf_cnpj_check}</td>
                        <td class="text-center">${check.bank_name}</td>
                        <td>${formatMoney(check.value)}</td>
                        <td class="text-center">
                            <span class='label label-${check.due_date.color}'>${check.due_date.value}</span>
                        </td>
                        <td class="text-center">
                            <a target="_blank" href="${url + 'check-control/editItem/' + check.id}" class="btn btn-warning btn-sm" title="Visualizar"><i class="fa fa-eye"></i></a>
                            <button type="button" class="btn btn-success btn-sm check" checkId="${check.id}" title="Selecionar"><i class="fa fa-check"></i></button>
                        </td>
                    </tr>`;
            }).join('')
        );

        $('[cpfcnpj]').inputmask({ mask: ['999.999.999-99', '99.999.999/9999-99'], keepStatic: true });

        let pagination = page > 1 ? `<li class="page-item"><a class="page-link modal-page-link" page="${page - 1}">&laquo;</a></li>` : "";

        for (let index = responseCheck.pagination.min; index <= responseCheck.pagination.max; index++) {
            pagination += `<li class="page-item ${page == index ? "active" : ""}"><a class="page-link modal-page-link" page="${index}">${index}</a></li>`;
        }

        pagination += page < responseCheck.pagination.max ? `<li class="page-item"><a class="page-link modal-page-link" page="${page + 1}">&raquo;</a></li>` : '';

        $('.modal-pagination-seller-customer').html(pagination);

        $('.check-checkbox').on('change', function () {
            const itemId = $(this).val();

            if (this.checked) {
                if (!selectedItems.includes(itemId)) {
                    selectedItems.push(itemId);
                }
            } else {
                selectedItems = selectedItems.filter(id => id !== itemId);
            }

            if (selectedItems.length > 0) {
                $('.check').addClass('hidden');
                $('#addSelecionados').removeClass('hidden');
            } else {
                $('.check').removeClass('hidden');
                $('#addSelecionados').addClass('hidden');
            }
        }).change();

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

                        $('.addCheck').removeAttr('hidden');
                        $('#customerCheckModal').modal('hide');
                        $('#customerCheck').attr('disabled', true);
                    } else {
                        Toast.fire({
                            icon: 'warning',
                            title: message
                        });
                    }
                },
            });
        });

        $('#addSelecionados').on('click', function () {
            const selectedChecks = selectedItems.map(id => {
                let check = responseCheck.data.find(check => check.id.toString() === id);
                if (check && !deleteItems.includes(check.id)) {
                    deleteItems.push(check.id);
                }
                return check;
            });

            $('#tableSelectedChecks').append(
                selectedChecks.map(check => {
                    return `<tr data-id="${check.id}">
                            <input type="hidden" id="agencyCheck" name="agencyCheck" value="${check.agency}">
                            <input type="hidden" id="bankIdCheck" name="bankIdCheck" value="${check.id_bank}">
                            <input type="hidden" id="numberAccountCheck" name="numberAccountCheck" value="${check.number_account}">
                            <td class="text-center">${check.number_check}</td>
                            <td>${check.owner_check}</td>
                            <td class="text-center">${check.cpf_cnpj_check}</td>
                            <td class="text-center">${check.bank_name}</td>
                            <td>${formatMoney(check.value)}</td>
                            <td class="text-center">${check.due_date.value}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-danger btn-sm remove-check" title="Remover"><i class="fa fa-trash"></i></button>
                            </td>
                        </tr>`;
                }).join('')
            );

            $('#customerCheckModal').modal('hide');
            $('.divTableSelectedChecks').removeAttr('hidden');

            $('.check-checkbox').prop('checked', false);
            selectedItems = [];

            updateTableVisibility();

            $('.remove-check').on('click', function () {
                const row = $(this).closest('tr');
                const itemId = row.attr('data-id');

                row.remove();
                deleteItems = deleteItems.filter(id => id.toString() !== itemId.toString());
                selectedItems = selectedItems.filter(id => id.toString() !== itemId.toString());

                $(`#customerCheckTable .check-checkbox[value="${itemId}"]`).prop('checked', false);

                updateTableVisibility();
            });
        });
    });

    $('#agency, #account, #number_check, #number_account').on('change', function () {
        let agency = $('#agency').val();
        let account = $('#account').val();
        let numberCheck = $('#number_check').val();
        let numberAccount = $('#number_account').val();

        if (agency != '' && account != '' && numberCheck != '' && numberAccount != '') {
            $('.addCheck').removeAttr('hidden');
        } else {
            $('.addCheck').attr('hidden', true);
        }
    }).change();

    $('#addCheck').on('click', function () {
        let checkId = $('#checkId').val();
        let ownerCheck = $('#owner_check').val();
        let cpfcnpjCheck = $('#cpfcnpj_check').val();
        let selectedOwnCheck = $('input[name="own_check"]:checked').val();

        if (selectedOwnCheck == false) {
            if (ownerCheck === '' || cpfcnpjCheck === '') {
                Toast.fire({
                    icon: 'warning',
                    title: 'Por favor, preencha todos os campos.'
                });
                return;
            }
        }

        if (checkId) deleteItems.push(checkId);

        let bank = $('#bank').val();
        let agency = $('#agency').val();
        let account = $('#account').val();
        let dueDate = $('#due_date_check').val();
        let checkValue = $('#amount_paid').val();
        let numberCheck = $('#number_check').val();
        let numberAccount = $('#number_account').val();

        if (bank === '' || agency === '' || account === '' || dueDate === '' || checkValue === '' || numberCheck === '' || numberAccount === '') {
            Toast.fire({
                icon: 'warning',
                title: 'Por favor, preencha todos os campos.'
            });
            return;
        }

        if (bank) {
            $.ajax({
                url: url + 'ajax/CheckControl/getBankById/',
                dataType: 'json',
                method: 'POST',
                data: {
                    bankId: bank
                },
                async: false,
                success: function (response) {
                    const { error, message, bank } = response;

                    if (!error) {
                        bankName = bank.name;
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: message
                        });
                    }
                }
            });
        }

        let newCheckId = checkId ? checkId : 'temp_' + Date.now();

        let newRow = `<tr data-id="${newCheckId}">
                    <input type="hidden" id="bankIdCheck" name="bankIdCheck" value="${bank}">
                    <input type="hidden" id="agencyCheck" name="agencyCheck" value="${agency}">
                    <input type="hidden" id="numberAccountCheck" name="numberAccountCheck" value="${numberAccount}">
                    <td class="text-center">${numberCheck}</td>
                    <td>${selectedOwnCheck == 1 ? 'Proprio' : ownerCheck}</td>
                    <td class="text-center">${cpfcnpjCheck}</td>
                    <td class="text-center">${bankName}</td>
                    <td>${checkValue}</td>
                    <td class="text-center">${moment(dueDate).format('DD/MM/YYYY')}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm remove-check" title="Remover"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>`;

        $('#tableSelectedChecks').append(newRow);
        $('.divTableSelectedChecks').removeAttr('hidden');

        $('#agency').val('');
        $('#account').val('');
        $('#checkId').val('');
        $('#check_value').val('');
        $('#owner_check').val('');
        $('#amount_paid').val('');
        $('#number_check').val('');
        $('#cpfcnpj_check').val('');
        $('#number_account').val('');
        $('#due_date_check').val('');

        $('#bank').prop('disabled', false);
        $('#agency').prop('readonly', false);
        $('#owner_check').prop('readonly', false);
        $('#amount_paid').prop('readonly', false);
        $('#number_check').prop('readonly', false);
        $('#cpfcnpj_check').prop('readonly', false);
        $('#number_account').prop('readonly', false);
        $('#due_date_check').prop('readonly', false);

        $('.addCheck').attr('hidden', true);
        $('#customerCheck').removeAttr('disabled');

        updateTableVisibility();

        $('.remove-check').off('click').on('click', function () {
            let row = $(this).closest('tr');
            let dataId = row.attr('data-id');

            row.remove();
            deleteItems = deleteItems.filter(id => id.toString() !== dataId.toString());
            updateTableVisibility();
        });
    });

    function updateTableVisibility() {
        $('#totalRow').remove();

        if ($('#tableSelectedChecks tr').length == 0) {
            $('.divTableSelectedChecks').attr('hidden', true);
        } else {
            $('.divTableSelectedChecks').removeAttr('hidden');
            $("#own_check, #bank, #account, #owner_check, #cpfcnpj_check, #agency, #number_account, #number_check, #due_date_check").removeAttr("required");

            amountPaidByCheck = 0;
            $('#tableSelectedChecks tr').each(function () {
                let checkValue = unmaskMoney($(this).find('td').eq(4).text());

                if (!isNaN(checkValue)) {
                    amountPaidByCheck += parseFloat(checkValue);
                }
            });

            if (amountPaidByCheck > 0) {
                $('#amount_paid').val(formatMoney(amountPaidByCheck));

                let totalRow = `
                <tr id="totalRow">
                    <td colspan="4" class="text-right"><strong>Total</strong></td>
                    <td colspan="3">${formatMoney(amountPaidByCheck)}</td>
                </tr>`;

                $('#tableSelectedChecks').append(totalRow);
            }
        }
    }
});
