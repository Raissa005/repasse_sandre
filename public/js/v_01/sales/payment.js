jQuery(function () {
    $("#id_form_of_payment").on("change", function () {
        const form_payment = $(this).val();
        switch (form_payment) {
            case '1': //Boleto
                $(".finance").hide();
                $(".payment_order").hide();
                $(".transf").hide();

                $("#number_paymentOrder").removeAttr("required");
                $("#owner_paymentOrder").removeAttr("required");
                $("#cpfcnpj_paymentOrder").removeAttr("required");
                $("#agency_paymentOrder").removeAttr("required");
                $("#number_account_paymentOrder").removeAttr("required");

                break;
            case '2': //Transferencia
                $(".transf").show();
                $(".finance").hide();
                $(".payment_order").hide();

                $("#owner_paymentOrder").removeAttr("required");
                $("#cpfcnpj_paymentOrder").removeAttr("required");
                $("#agency_paymentOrder").removeAttr("required");
                $("#number_account_paymentOrder").removeAttr("required");
                $("#number_paymentOrder").removeAttr("required");

                $.ajax({
                    url: url + "ajax/ajax/getAndFilterAccountsPortion/",
                    dataType: 'json',
                    method: 'POST',
                    data: {
                        status: 1,
                    },
                    async: false,

                    success: function (response) {
                        const { error, accounts } = response;

                        if (!error) {
                            let html = "";
                            const accountsSelect = accounts.map(account => {
                                html += `<option value="${account.id_Account}">${account.name}</option>`;

                                return {
                                    id: account.id_Account,
                                    agency: account.agency,
                                    account_number: account.account_number,
                                };
                            });

                            $("#id_account").html(html);

                            $("#id_account").on("change", function () {
                                const idAccount = $(this).val();
                                const account = accountsSelect.find(accountSelect => accountSelect.id == idAccount);

                                $("#agency").val(account.agency);
                                $("#account_number").val(account.account_number);
                            }).trigger("change");
                        }
                    }
                });
                break;
            case '3': //Cheque
                $(".payment_order").show();
                $(".finance").hide();
                $(".transf").hide();

                $("#number_paymentOrder").attr("required", "");
                $("#agency_paymentOrder").attr("required", "");
                $("#number_account_paymentOrder").attr("required", "");

                $("#own_paymentOrder_yes").trigger("change");

                break;
            case '4': //Financiamento
                const finance_bank = 1;
                $(".finance").show();
                $(".transf").hide();
                $(".payment_order").hide();

                $("#owner_paymentOrder").removeAttr("required");
                $("#cpfcnpj_paymentOrder").removeAttr("required");
                $("#agency_paymentOrder").removeAttr("required");
                $("#number_account_paymentOrder").removeAttr("required");
                $("#number_paymentOrder").removeAttr("required");

                $.ajax({
                    url: url + "ajax/ajax/getAndFilterBanksPortion/",
                    dataType: 'json',
                    method: 'POST',
                    data: {
                        finance_bank: finance_bank,
                    },
                    async: false,

                    success: function (response) {
                        const { error, banks } = response;

                        if (!error) {

                            const html = banks.map(bank => {
                                return `<option value="${bank.id}">${bank.name}</option>`;
                            });
                            $("#id_bank_finance").html(html);
                        }
                    }
                });
                $(".payment_order").hide();
                $("#number_paymentOrder").removeAttr("required");
                break;

            default:
                $(".finance").hide();
                $(".payment_order").hide();
                $(".transf").hide();

                $("#number_paymentOrder").removeAttr("required");
                $("#owner_paymentOrder").removeAttr("required");
                $("#cpfcnpj_paymentOrder").removeAttr("required");
                $("#agency_paymentOrder").removeAttr("required");
                $("#number_account_paymentOrder").removeAttr("required");
                break;
        }
    }).trigger("change");

    $("#own_paymentOrder_no, #own_paymentOrder_yes").on("change", function () {
        $.ajax({
            url: url + "ajax/ajax/getAllBanksPortion/",
            dataType: 'json',
            method: 'POST',
            data: {},
            async: false,

            success: function (response) {
                const { error, banks } = response;

                if (!error) {

                    const html = banks.map(bank => {
                        return `<option value="${bank.id}">${bank.name}</option>`;
                    });
                    $("#bank_paymentOrder").html(html);
                }
            }
        });

        const own_paymentOrder = $("#own_paymentOrder_yes").is(':checked');

        if (own_paymentOrder) {
            const idCustomer = $("#id_customer").val();

            $.ajax({
                url: url + "ajax/ajax/getCustomerById/",
                dataType: 'json',
                method: 'POST',
                data: {
                    id_customer: idCustomer,
                },
                async: false,

                success: function (response) {
                    const { error, customer } = response;

                    if (!error) {
                        $("#owner_paymentOrder").attr("disabled", "");
                        $("#owner_paymentOrder").val(customer.name)

                        $("#cpfcnpj_paymentOrder").attr("disabled", "");
                        $("#cpfcnpj_paymentOrder").val(customer.person_registration);
                    }
                }
            });

        } else {
            $("#owner_paymentOrder").removeAttr("disabled");
            $("#owner_paymentOrder").attr("required", "");

            $("#cpfcnpj_paymentOrder").removeAttr("disabled");
            $("#cpfcnpj_paymentOrder").attr("required", "");

            const idPortion = $("#id_portion").val();
            if (idPortion == "") {
                $("#cpfcnpj_paymentOrder").val("");
                $("#owner_paymentOrder").val("");
            }
        }
    }).trigger("change");

    $(".btn-get-portion").on("click", async function () {
        const idPortion = $(this).attr("id_portion");
        const id_sale = $("#id_sale").val();
        const response = await post('ajax/getPortionById', { id: idPortion });
        const { error, portion } = response;
        if (error) return;


        $("html").animate({ scrollTop: 50 }, 50);

        $('#id_portion').val(idPortion);
        $('#formPortion').attr("action", url + 'sales/handleEditPayment/' + id_sale + '/' + idPortion);

        $("#portion_number").val(portion.portion_number);
        $("#id_portion").val(portion.id);
        $("#due_date").val(portion.due_date);
        $("#value").val(formatDecimal(unmaskMoney(portion.value), 2));

        if (portion.type_of_payment == 1) {
            $("#type_of_payment").val(portion.type_of_payment).trigger("change");
            $("#id_form_of_payment").val(portion.id_form_of_payment).trigger("change");

            switch (portion.id_form_of_payment) {
                case '1': //Boleto

                    break;
                case '2': //Transferencia
                    $("#id_account").val(portion.id_account).trigger("change");
                    break;
                case '3': //Cheque
                    const own_paymentOrder = portion.own_paymentOrder;

                    if (own_paymentOrder == 0) {
                        $("#own_paymentOrder_yes").prop("checked", false);
                        $("#own_paymentOrder_no").prop("checked", true);

                        $("#owner_paymentOrder").removeAttr("disabled");
                        $("#owner_paymentOrder").attr("required", "");
                        $("#owner_paymentOrder").val(portion.owner_paymentOrder);

                        $("#cpfcnpj_paymentOrder").removeAttr("disabled");
                        $("#cpfcnpj_paymentOrder").attr("required", "");
                        $("#cpfcnpj_paymentOrder").val(portion.cpfcnpj_paymentOrder);

                        $("#agency_paymentOrder").val(portion.agency_paymentOrder);
                        $("#number_account_paymentOrder").val(portion.number_account_paymentOrder);
                    } else {
                        $("#own_paymentOrder_no").prop("checked", false);
                        $("#own_paymentOrder_yes").prop("checked", true);

                        $("#owner_paymentOrder").attr("disabled", "");
                        $("#owner_paymentOrder").val(portion.owner_paymentOrder);

                        $("#cpfcnpj_paymentOrder").attr("disabled", "");
                        $("#cpfcnpj_paymentOrder").val(portion.cpfcnpj_paymentOrder);

                        $("#agency_paymentOrder").val(portion.agency_paymentOrder);
                        $("#number_account_paymentOrder").val(portion.number_account_paymentOrder);
                    }

                    $("#bank_paymentOrder").val(portion.bank_paymentOrder).select2('destroy').select2();
                    $("#number_paymentOrder").val(portion.number_paymentOrder);
                    break;
                case '4': //Financiamento

                    $("#id_bank_finance").val(portion.id_bank_finance).trigger("change");
                    break;
                default:
                    break;
            }

            $("#status_payment").val(portion.status_portion).trigger("change");
            $("#amount_paid").val(formatDecimal(portion.amount_paid, 2));
            $("#observation").val(portion.observation);

            $("#btn-edit-portion").show();
            $("#btn-back-to-add").show();
            $("#btn-add-portion").hide();
        }

        if (portion.type_of_payment == 2) {
            $("#type_of_payment").val(portion.type_of_payment).trigger("change");
            $("#property-val").val(portion.id_property);
            $("#amount_paid").val(formatDecimal(portion.amount_paid, 2));
        }

        if (portion.type_of_payment == 3) {
            $("#type_of_payment").val(portion.type_of_payment).trigger("change");
            $("#vehicle").val(portion.vehicle);
            $("#license_plate").val(portion.license_plate);
            $("#amount_paid").val(formatDecimal(portion.amount_paid, 2));
        }
    });

    $("#btn-back-to-add").on("click", function () {
        const idSale = $('#id_sale').val();
        window.location.href = url + "sales/payment/" + idSale;
    });

    $(".observationPortion").on("click", function () {
        const portionId = $(this).attr("data-id-portion");

        $.ajax({
            url: url + "ajax/ajax/getPortionById/",
            dataType: 'json',
            method: 'POST',
            data: {
                "id": portionId,
                "table": "payments_of_sales",
            },
            async: false,

            success: function (response) {
                const { error, portion } = response;

                if (!error) {
                    $("#observation_text").html(portion.observation);
                }
            }
        });
    });

    $("#status_payment").on("change", function () {
        const status = $(this).val();

        if (status == 1) {
            $("#pay").removeClass("hidden");
            $("#amount_paid").attr("required", "");
        } else {
            $("#pay").addClass("hidden");
            $("#amount_paid").removeAttr("required")
        }
    }).trigger('change');

    $("#type_of_payment").on("change", function () {
        const type = $(this).val();
        (type == 1) ? $("#type-buttons-hidden, #type-installments-hidden").show() : $("#type-buttons-hidden, #type-installments-hidden").hide();

        $("#pay").removeClass("hidden");
        $("#vehicle-input").hide();
        $("#vehicle-input").find("input").removeAttr("required");
        $("#property-input").hide();

        const arrFunctions = {
            1: () => {
                $("#pay").addClass("hidden");
                $("#pay").find("label").html("Valor Pago");
            },
            2: () => {
                $("#pay").find("label").html("Valor do Imóvel");
                $("#property-input").show();
            },
            3: () => {
                $("#pay").find("label").html("Valor do Veículo");
                $("#vehicle-input").show();
                $("#vehicle-input").find("input").attr("required", "");
            },
        }

        arrFunctions[type]();
    }).trigger('change');
});
