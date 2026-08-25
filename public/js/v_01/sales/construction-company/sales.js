$("#id_branch").on('change', function () {
    const idBranch = $(this).val();
    const idTypeCustomer = 8
    const id_customer = $('#id_customer_selected').val();
    const id_product = $('#id_product_selected').val();
    const id_contract = $('#id_contract_selected').val();
    const id_proposal = $('#id_proposal_selected').val();
    const id_user = $('#id_sales_manager_selected').val();

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
            const { customers, products, contracts, proposal, users } = response;

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

            $("#id_customer").html(customersOpt).trigger("change");
            $("#id_product_edit").html(productsOpt).trigger("change");
            $("#id_standard_contract").html(contractsOpt).trigger("change");
            $("#id_standard_proposal").html(proposalOpt).trigger("change");
            $("#sales_manager").html(usersOpt).trigger("change");
        }
    });
}).trigger("change");

$("#id_product").on('change', function () {
    const id_product = $('#id_product').val();

    $.ajax({
        url: url + "ajax/ajax/getProductById/",
        dataType: 'json',
        method: 'POST',
        data: { id_product },
        async: false,

        success: function (response) {
            const { product } = response;

            $(".sale_value").val(formatMoney(product.value, 2));
            $(".installment_value").val(formatMoney(product.installment_value, 2));
        }
    });
}).trigger("change");

$("#id_product_edit").on('change', function () {
    const id_product = $('#id_product_edit').val();

    $.ajax({
        url: url + "ajax/ajax/getProductById/",
        dataType: 'json',
        method: 'POST',
        data: { id_product },
        async: false,

        success: function (response) {
            const { product } = response;

            $(".sale_value").val(formatMoney(product.value, 2));
            $(".installment_value").val(formatMoney(product.installment_value, 2));
        }
    });
});

/**FGTS */
$("#fgts_select").on('change', function () {
    $(this).val() == 1 ? $(".fgts").show() : $(".fgts").hide()
}).trigger("change");