const saleId = $('#sale-id').val()
let paymentArrangement = false

async function getPaymentArrangementFromSale(id) {
    return post("sales/getPaymentArrangement", { id });
}

async function getCommissionCalculation(value, taxes, percentage, seller, positions) {
    return post("commissionArrangement/commissionCalculation", { value, taxes, percentage, seller, positions: positions, saleId });
}

async function renderSelectCustomer() {
    const response = await post("paymentAgreement/getFilterSupplierForSale", { saleId: saleId })

    if (!response.error) {
        if (response.data.length > 0) {
            $("#table-adds-partitions-commission").show()
            let html = '';
            html += `<option value="" selected disabled>Selecione</option>`;
            response.data.map((customer) => {
                html += `<option value="${customer.id}">${customer.name}</option>`;
            })
            $(`#new-position-customer`).html(html)

        } else {
            $("#table-adds-partitions-commission").hide()
        }
    }
}

$(`#new-position-customer`).ready(async function () {
    await renderSelectCustomer();
});

async function commissionCalculation() {
    if (!paymentArrangement) {
        paymentArrangement = await post("sales/getPaymentArrangement", { id: saleId });
    }
    if (!paymentArrangement.error) {
        let positions = [];
        $(`.positions`).each((index, value) => {
            positions = [...positions, {
                html_id: $(value).attr('id'),
                percentage_commission: $(value).children('td').children('div').children(`input.percentage-commission`).val(),
                origin_commission: $(value).children('td').children(`select.origin-commission`).val(),
            }]
        });

        const commission_calculation = await getCommissionCalculation(
            paymentArrangement.data.value,
            paymentArrangement.data.taxes,
            paymentArrangement.data.percentage,
            { percentage: $(`#percentage-commission-seller`).val(), origin: $(`#origin-commission-seller`).val() },
            positions
        );

        if (!commission_calculation.error) {
            $(`#seller-amount`).text(commission_calculation.data.seller.amount);
            commission_calculation.data.positions.map((position) => {
                $(`tr#${position.html_id}`).children('td.amount').text(position.amount)
            })

            $(`#branch-percentage-real`).text(commission_calculation.data.branch.percentage.real.toFixed(2))
            $(`#branch-percentage-virtual`).text(commission_calculation.data.branch.percentage.virtual.toFixed(2))
            $(`#branch-amount-real`).text(commission_calculation.data.branch.amount.real)
            $(`#branch-amount-virtual`).text(commission_calculation.data.branch.amount.virtual)
        }
    }
}

$(document).on('change', ".arrangement-state", async function () {
    await commissionCalculation()
})

$(`#new-position-submit`).on('click', async function () {
    const customerId = $(`#new-position-customer`).val()
    const costCenter = $(`#new-position-cost-center`).val()
    const formPayment = $(`#new-position-form-payment`).val()
    const origin = $(`#new-position-origin`).val()
    const percentage = $(`#new-position-percentage`).val()

    if (customerId != null && percentage > 0 && costCenter != null && formPayment != null) {
        const response = await post("paymentAgreement/createParticipantInPaymentArrangementOnSale", {
            saleId: saleId,
            customerId: customerId,
            percentage: percentage,
            origin: origin,
            costCenter: costCenter,
            formPayment: formPayment
        })

        if (!response.error) {
            $(`#new-position-customer`).html(`<option value="">Carregando...</option>`);
            const html = `<tr id="position_${response.data.id}" class="positions">
                            <td>
                                <input type="hidden" name="positions[${response.data.id}][id]" value="${response.data.id}">
                                ${response.data.name}
                            </td>
                            <td>${response.data.position}</td>
                            <td>
                                <select name="positions[${response.data.id}][origin_commission]" class="origin-commission form-control arrangement-state">
                                    <option value="1" ${response.data.origin == 1 ? 'selected' : ''}>Comissão Bruta</option>
                                    <option value="2" ${response.data.origin == 2 ? 'selected' : ''}>Comissão Líquida Real</option>
                                    <option value="3" ${response.data.origin == 3 ? 'selected' : ''}>Comissão Líquida Virtual</option>
                                    <option value="4" ${response.data.origin == 4 ? 'selected' : ''}>Comissão Parcela</option>
                                </select>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="number" placeholder="0.1" class="percentage-commission form-control arrangement-state" name="positions[${response.data.id}][percentage_commission]" value="${response.data.percentage}" min="0" max="100" step="0.01">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </td>
                            <td class="text-right amount">Calculando...</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-danger btn-sm btn-remove" id="${response.data.id}"><i class="fa fa-times"></i></button>
                            </td>
                        </tr>`;

            $(`table#commission-partition-table tbody tr`).last().after(html);

            await commissionCalculation()
            await renderSelectCustomer()

            $(`#new-position-customer`).val(null).trigger('change')
            $(`#new-position-origin`).val(1).trigger('change')
            $(`#new-position-percentage`).val('')
            $(`#new-position-cost-center`).val(null).trigger('change')
            $(`#new-position-form-payment`).val(null).trigger('change')

            $("select").select2();
        }

        Toast.fire({
            icon: response.error ? 'error' : 'success',
            title: response.message,
        });
    } else {
        Toast.fire({
            icon: 'warning',
            title: "Campos obrigatórios não preenchidos.",
        });
    }

})

$(document).on('click', '.btn-remove', async function () {
    $(`#remove-item-modal`).modal('show');
    $(`#remove-item-modal #confirm-remove-item`).attr('data-id', $(this).attr('id')).trigger('change');
})

$(document).on('click', '#remove-item-modal #confirm-remove-item', async function (el) {
    $(`#remove-item-modal`).modal('hide');
    let id = el.target.dataset.id
    let response = await post("sales/removeParticipantFromPaymentArrangement", { id: id });

    Toast.fire({
        icon: response.error ? 'error' : 'success',
        title: response.message,
    });

    if (!response.error) {
        $(`#new-position-customer`).html(`<option value="">Carregando...</option>`);
        $(`tr#position_${id}`).remove();
        await commissionCalculation()
        await renderSelectCustomer()
    }
})

$(document).on('change', ".summary-state", function () {
    let installments = [];
    $('.installments-values').each((index, value) => {
        installments = [...installments, {
            installment_number: $(value).attr('id'),
            currency_id: $(value).children('td').children(`select.currency-id`).val(),
            currency_value: $(value).children('td').children(`input.currency-value`).val(),
            value: $(value).children('td').children(`input.installments-value`).val(),
            due_date: $(value).children('td').children(`input.installments-due-date`).val(),
        }];
    });

    installments.forEach(element => {
        if (element.currency_id != 1) {
            $.ajax({
                url: url + "ajax/ajax/getValueCurrency",
                dataType: "json",
                method: "POST",
                data: {
                    currencyId: element.currency_id
                },
                async: false,

                success: function (response) {
                    const { error, currencies } = response;

                    $(".currency-value").show();

                    $(`tr#${element.installment_number}`).children('td').children(`input.currency-value`).val(formatDecimal(unmaskMoney(element.value) / currencies.value, 2));

                    $(`tr#${element.installment_number}`).children('td').children(`input.currency-value`).on("change", function () {
                        $(`tr#${element.installment_number}`).children('td').children(`input.installments-value`).val(formatMoney(unmaskMoney($(`tr#${element.installment_number}`).children('td').children(`input.currency-value`).val()) * currencies.value));
                    });
                }
            });
        } else {
            $(`tr#${element.installment_number}`).children('td.currency-value').children(`input.currency-value`).val(formatMoney(0));
        }
    });

    let installmentsPartitions = [];
    $('.commission-partition-table').each((index, value) => {
        installmentsPartitions = [...installmentsPartitions, {
            seller_id: $(value).attr('id'),
            seller_currency_id: $(value).children('tbody').children('tr').children('td').children(`select.seller-currency-id`).val(),
            seller_currency_value: $(value).children('tbody').children('tr').children('td').children(`input.seller-currency-value`).val(),
            seller_amount: $(value).children('tbody').children('tr').children('td').children(`input.seller-amount`).val(),
        }];
    });

    installmentsPartitions.forEach(el => {
        if (el.seller_currency_id != 1) {
            $.ajax({
                url: url + "ajax/ajax/getValueCurrency",
                dataType: "json",
                method: "POST",
                data: {
                    currencyId: el.seller_currency_id
                },
                async: false,

                success: function (response) {
                    const { error, currencies } = response;

                    $(".summary-involved").show();
                    if ($(`table#${el.seller_id}`).children('tbody').children('tr').children('td').children(`select.seller-currency-id`).val() != 1) {
                        $(`table#${el.seller_id}`).children('tbody').children('tr').children('td').children(`input.seller-currency-value`).val(formatDecimal(unmaskMoney(el.seller_amount) / currencies.value, 2));
                    } else {
                        $(`table#${el.seller_id}`).children('tbody').children('tr').children('td').children(`input.seller-currency-value`).val(formatMoney(0));
                    }

                    $(`table#${el.seller_id}`).children('tbody').children('tr').children('td').children(`input.seller-currency-value`).on("change", function () {
                        $(`table#${el.seller_id}`).children('tbody').children('tr').children('td').children(`input.seller-amount`).val(formatMoney(unmaskMoney($(`table#${el.seller_id}`).children('tbody').children('tr').children('td').children(`input.seller-currency-value`).val()) * currencies.value));
                    });
                }
            });
        } else {
            $(`table#${el.seller_id}`).children('tbody').children('tr').children('td').children(`input.seller-currency-value`).val(formatMoney(0));
        }
    });

    let positions = [];
    $(`.positions`).each((index, val) => {
        positions = [...positions, {
            position_id: $(val).attr('id'),
            position_currency_id: $(val).children('td').children(`select.position-currency-id`).val(),
            currency_value_position: $(val).children('td').children(`input.position-currency-value`).val(),
            position_amount: $(val).children('td').children(`input.position-amount`).val()
        }]
    });

    positions.forEach(item => {
        if (item.position_currency_id != 1) {
            $.ajax({
                url: url + "ajax/ajax/getValueCurrency",
                dataType: "json",
                method: "POST",
                data: {
                    currencyId: item.position_currency_id
                },
                async: false,

                success: function (response) {
                    const { error, currencies } = response;

                    $(".summary-involved").show();
                    if ($(`tr#${item.position_id}`).children('td').children(`select.position-currency-id`).val() != 1) {
                        $(`tr#${item.position_id}`).children('td').children(`input.position-currency-value`).val(formatDecimal(unmaskMoney(item.position_amount) / currencies.value, 2));
                    } else {
                        $(`tr#${item.position_id}`).children('td').children(`input.position-currency-value`).val(formatMoney(0));
                    }

                    $(`tr#${item.position_id}`).children('td').children(`input.position-currency-value`).on("change", function () {
                        $(`tr#${item.position_id}`).children('td').children(`input.position-amount`).val(formatMoney(unmaskMoney($(`tr#${item.position_id}`).children('td').children(`input.position-currency-value`).val()) * currencies.value, 1));
                    });
                }
            });
        } else {
            $(`tr#${item.position_id}`).children('td').children(`input.position-currency-value`).val(formatMoney(0));
        }
    });
});

$(document).ready(function () {
    let installments = [];

    $('.installments-values').each((index, value) => {
        installments = [...installments, {
            installment_number: $(value).attr('id'),
            currency_id: $(value).children('td').children(`select.currency-id`).val(),
            currency_value: $(value).children('td').children(`input.currency-value`).val(),
            value: $(value).children('td').children(`input.installments-value`).val(),
            due_date: $(value).children('td').children(`input.installments-due-date`).val(),
        }];
    });

    installments.forEach(element => {
        if (element.currency_id != 1) {
            $.ajax({
                url: url + "ajax/ajax/getValueCurrency",
                dataType: "json",
                method: "POST",
                data: {
                    currencyId: element.currency_id
                },
                async: false,

                success: function (response) {
                    const { error, currencies } = response;

                    $(".currency-value").show();

                    $(`tr#${element.installment_number}`).children('td').children(`input.currency-value`).val(formatDecimal(unmaskMoney(element.value) / currencies.value), 2);
                }
            });
        } else {
            $(".currency-value").hide();
        }
    });

    let installmentsPartitions = [];
    $('.commission-partition-table').each((index, value) => {
        installmentsPartitions = [...installmentsPartitions, {
            seller_id: $(value).attr('id'),
            seller_currency_id: $(value).children('tbody').children('tr').children('td').children(`select.seller-currency-id`).val(),
            seller_currency_value: $(value).children('tbody').children('tr').children('td').children(`input.seller-currency-value`).val(),
            seller_amount: $(value).children('tbody').children('tr').children('td').children(`input.seller-amount`).val(),
        }];
    });

    installmentsPartitions.forEach(el => {
        if ($(`table#${el.seller_id}`).children('tbody').children('tr').children('td').children(`select.seller-currency-id`).val() != 1) {
            $.ajax({
                url: url + "ajax/ajax/getValueCurrency",
                dataType: "json",
                method: "POST",
                data: {
                    currencyId: el.seller_currency_id
                },
                async: false,

                success: function (response) {
                    const { error, currencies } = response;

                    $(".summary-involved").show();
                    $(`table#${el.seller_id}`).children('tbody').children('tr').children('td').children(`input.seller-currency-value`).val(formatDecimal(unmaskMoney(el.seller_amount) / currencies.value, 2));
                }
            });
        } else {
            $(".summary-involved").hide();
        }
    });

    let positions = [];
    $(`.positions`).each((index, val) => {
        positions = [...positions, {
            position_id: $(val).attr('id'),
            position_currency_id: $(val).children('td').children(`select.position-currency-id`).val(),
            currency_value_position: $(val).children('td').children(`input.position-currency-value`).val(),
            position_amount: $(val).children('td').children(`input.position-amount`).val()
        }]
    });

    positions.forEach(item => {
        if (item.position_currency_id != 1) {
            $.ajax({
                url: url + "ajax/ajax/getValueCurrency",
                dataType: "json",
                method: "POST",
                data: {
                    currencyId: item.position_currency_id
                },
                async: false,

                success: function (response) {
                    const { error, currencies } = response;

                    $(".summary-involved").show();
                    $(`tr#${item.position_id}`).children('td').children(`input.position-currency-value`).val(formatDecimal(unmaskMoney(item.position_amount) / currencies.value, 2));
                }
            });
        } else {
            $(".summary-involved").hide();
        }
    });
});