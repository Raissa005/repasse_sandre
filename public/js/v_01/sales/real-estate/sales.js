$("#id_branch").on('change', function () {
    const idBranch = $(this).val();
    const idTypeCustomer = 8
    const id_customer = $('#id_customer_selected').val();
    const id_product = $('#id_product_selected').val();
    const id_contract = $('#id_contract_selected').val();
    const id_proposal = $('#id_proposal_selected').val();
    const id_user = $('#id_sales_manager_selected').val();
    const currencyId = $('#currency').val();

    $.ajax({
        url: url + "ajax/ajax/getExclusiveByBranch/",
        dataType: 'json',
        method: 'POST',
        data: {
            id_branch: idBranch,
            id_type_customer: idTypeCustomer,
        },
        async: false,

        success: function (response) {
            const { customers, products, contracts, proposal, users, currencies } = response;

            const customersOpt = customers.map(customer => {
                return `<option value="${customer.id}" ${id_customer == customer.id ? "selected" : ""}>${customer.name}</option>`;
            });

            const productsOpt = products.map(product => {
                return `<option value="${product.id}" ${id_product == product.id ? "selected" : ""}>${product.name}</option>`;
            });

            const contractsOpt = contracts.map(contract => {
                return `<option value="${contract.id}" ${id_contract == contract.id ? "selected" : ""}>${contract.name}</option>`;
            });

            const proposalOpt = proposal.map(prl => {
                return `<option value="${prl.id}" ${id_proposal == prl.id ? "selected" : ""}>${prl.name}</option>`;
            });

            const usersOpt = users.map(user => {
                return `<option value="${user.id}" ${id_user == user.id ? "selected" : ""}>${user.name}</option>`;
            });

            const currenciesOpt = currencies.map(currency => {
                return `<option value="${currency.id}" ${currencyId == currency.id ? "selected" : ""}>${currency.currency_symbol}</option>`;
            });

            $("#id_customer").html(customersOpt).trigger("change");
            $("#id_product_edit").html(productsOpt).trigger("change");
            $("#id_standard_contract").html(contractsOpt).trigger("change");
            $("#id_standard_proposal").html(proposalOpt).trigger("change");
            $("#sales_manager").html(usersOpt).trigger("change");
            $("#currency").html(currenciesOpt).trigger("change");
        }
    });
}).trigger("change");

$("#id_product").on('change', async function () {
    const id_product = $('#id_product').val();
    const currencyId = $('#currency').val();

    const branch = await post("sales/getCurrentBranch", { id_branch: userSession.branch.current.id });

    $.ajax({
        url: url + "ajax/ajax/getProductById/",
        dataType: 'json',
        method: 'POST',
        data: { id_product, currencyId },
        async: false,

        success: function (response) {
            const { product, currencies } = response;
            $(".sale_value").val(formatMoney(product.value, 2));
            $(".commission_value").val(formatMoney((product.value * (branch.data.percentage_commission_sale / 100)), 2));
            $(".commission_value").on('change', function () {
                let val = unmaskMoney($('.commission_value').val());

                $(".liquid_value").val(formatMoney(val / 0.05), 2);
            }).trigger("change");
            $(".installment_value").val(formatMoney(product.installment_value, 2));

            if (currencyId != 1) {
                $(".currency_value").show();
                $("#currency_value").val(formatDecimal((product.value * (branch.data.percentage_commission_sale / 100)) / currencies.value, 2));
            } else {
                $(".currency_value").hide();
            }
        }
    });
}).trigger("change");

$("#id_product_edit").on('change', async function () {
    const id_product = $('#id_product_edit').val();
    const branch = await post("sales/getCurrentBranch", { id_branch: userSession.branch.current.id });

    $.ajax({
        url: url + "ajax/ajax/getProductById/",
        dataType: 'json',
        method: 'POST',
        data: { id_product },
        async: false,

        success: function (response) {
            const { product } = response;
            $(".sale_value").val(formatMoney(product.value, 2));
            $(".commission_value").val(formatMoney((product.value * (branch.data.percentage_commission_sale / 100)), 2));
            $(".commission_value").on('change', function () {
                let val = unmaskMoney($('.commission_value').val());

                $(".liquid_value").val(formatMoney(val / 0.05), 2);
            }).trigger("change");
            $(".installment_value").val(formatMoney(product.installment_value, 2));
        }
    });
});

/**FGTS */
$("#fgts_select").on('change', function () {
    $(this).val() == 1 ? $(".fgts").show() : $(".fgts").hide()
}).trigger("change");

$("#sale_value").on('change', function () {
    let val = unmaskMoney($(this).val());
    let per = $('#percentage-commission').val();
    const currencyId = $('#currency').val();

    $.ajax({
        url: url + "ajax/ajax/getValueCurrency",
        dataType: "json",
        method: "POST",
        data: {
            currencyId: currencyId
        },
        async: false,

        success: function (response) {
            const { error, currencies } = response;

            $("#currency_value").val(formatDecimal((val * (per / 100)) / currencies.value, 2));
        }
    });

    $(".commission_value").val(formatMoney(val * (per / 100)), 2);
    $(".liquid_value").val(formatMoney((val * (per / 100)) / 0.05), 2);
});

$("#percentage-commission").on('change', function () {
    let val = unmaskMoney($('.sale_value').val());
    let per = $(this).val();
    const currencyId = $('#currency').val();

    $.ajax({
        url: url + "ajax/ajax/getValueCurrency",
        dataType: "json",
        method: "POST",
        data: {
            currencyId: currencyId
        },
        async: false,

        success: function (response) {
            const { error, currencies } = response;

            $("#currency_value").val(formatDecimal((val * (per / 100)) / currencies.value, 2));
        }
    });

    $(".commission_value").val(formatMoney(val * (per / 100)), 2);
    $(".liquid_value").val(formatMoney((val * (per / 100)) / 0.05), 2);
});

$("#commission_value").on('change', function () {
    let val = unmaskMoney($(this).val());
    let sale_val = unmaskMoney($('#sale_value').val());
    let per = ((val * 100) / sale_val);

    const currencyId = $('#currency').val();

    $.ajax({
        url: url + "ajax/ajax/getValueCurrency",
        dataType: "json",
        method: "POST",
        data: {
            currencyId: currencyId
        },
        async: false,

        success: function (response) {
            const { error, currencies } = response;

            $("#currency_value").val(formatDecimal(val / currencies.value, 2));
        }
    });

    $("#percentage-commission").val(per.toFixed(2));
    $(".liquid_value").val(formatMoney(val / 0.05), 2);
});

$("#currency").on("change", function () {
    const currencyId = $(this).val();
    let val = unmaskMoney($('#sale_value').val());
    let per = $('#percentage-commission').val();

    $.ajax({
        url: url + "ajax/ajax/getValueCurrency",
        dataType: "json",
        method: "POST",
        data: {
            currencyId: currencyId
        },
        async: false,

        success: function (response) {
            const { error, currencies } = response;

            if (currencyId != 1) {
                $(".currency_value").show();
                $("#currency_value").val(formatDecimal((val * (per / 100)) / currencies.value, 2));
            } else {
                $(".currency_value").hide();
            }
        }
    });
});

$("#currency_value").on("change", function () {
    let currencyVal = $(this).val();
    const currencyId = $('#currency').val();

    $.ajax({
        url: url + "ajax/ajax/getValueCurrency",
        dataType: "json",
        method: "POST",
        data: {
            currencyId: currencyId
        },
        async: false,

        success: function (response) {
            const { error, currencies } = response;

            $(".commission_value").val(formatMoney(unmaskMoney(currencyVal) * currencies.value));
            $(".currency_value").val(formatDecimal(unmaskMoney(currencyVal), 2));
        }
    });

    let val = unmaskMoney($(".commission_value").val());
    let sale_val = unmaskMoney($('#sale_value').val());
    let per = ((val * 100) / sale_val);

    $("#percentage-commission").val(per.toFixed(2));
});

$("#updateValue").on("click", function () {
    const currencyId = $('#currency').val();
    let val = unmaskMoney($('#sale_value').val());
    let per = $('#percentage-commission').val();

    $.ajax({
        url: url + "ajax/ajax/getValueCurrency",
        dataType: "json",
        method: "POST",
        data: {
            currencyId: currencyId
        },
        async: false,

        success: function (response) {
            const { error, currencies } = response;

            if (currencyId != 1) {
                $(".currency_value").show();
                $("#currency_value").val(formatDecimal((val * (per / 100)) / currencies.value, 2));
            } else {
                $(".currency_value").hide();
            }
        }
    });
});


/**Buscando cliente Propreitário */
const rows = 10;

$(document).on("click", ".modal-page-link", function () {
    const page = parseInt($(this).attr('page'));
    $("#page-attendance-ajax").val(page);
});


$(document).on("click", "#modal-attendance, #search_attendance, .modal-page-link", async function () {
    let searchName = $("#search_name_attendance").val();
    const page = parseInt($("#page-attendance-ajax").val());

    const responseAttendance = await post("sales/getAttendanceForSale", {
        page: page,
        limit: rows,
        name: searchName
    });

    $("#attendance-table").html(
        responseAttendance.data.map((attendance) => {
            return `<tr>
                    <td class="text-center">${attendance.id}</td>
                    <td>${attendance.name}</td>
                    <td class="text-center" cpfcnpj>${attendance.users_name}</td>
                    <td class="text-center">
                        <a target="_blank" href="${url + 'attendance/attendance/' + attendance.id}" class="btn btn-warning btn-sm" title="Visualizar"><i class="fa fa-eye"></i></a>
                        <button type="button" class="btn btn-success btn-sm attendance" id_attendance="${attendance.id}" title="Selecionar"><i class="fa fa-check"></i></button>
                    </td>
                </tr>`;
        })
    );

    let pagination = page > 1 ? `<li class="page-item"><a class="page-link modal-page-link" page="${page - 1}">&laquo;</a></li>` : "";
    for (let index = responseAttendance.pagination.min; index <= responseAttendance.pagination.max; index++) {
        pagination += `<li class="page-item ${page == index ? "active" : ""}"><a class="page-link modal-page-link" page="${index}">${index}</a></li>`;
    }

    pagination += page < responseAttendance.pagination.max ? `<li class="page-item"><a class="page-link modal-page-link" page="${page + 1}">&raquo;</a></li>` : '';
    $(".modal-pagination-attendance").html(pagination);

    $(".attendance").on("click", function () {
        const id_attendance = $(this).attr("id_attendance");
        $.ajax({
            url: url + "ajax/ajax/getAttendanceById/",
            dataType: "json",
            method: "POST",
            data: { id: id_attendance },
            async: false,
            success: function (response) {
                const { error, attendance } = response;
                if (!error) {
                    $("#name_attendance").val(attendance.id + " - " + attendance.name);
                    $("#id_attendance").val(attendance.id);
                    $("#attendance").modal("hide");
                }
            },
        });
    });
});