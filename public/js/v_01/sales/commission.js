let bill_receive_installment_id;
let sale_id;
let modal;
let arrangement;

async function getCommissionCalculation(value, taxes, seller, positions, saleId) {
    return post("commissionArrangement/commissionCalculation", { value: value, taxes: taxes, percentage: 100, seller: seller, positions: positions, saleId: saleId });
}

async function renderSelectCustomer() {
    const response = await post("paymentAgreement/getFilterSupplierForBillReceiveInstallment", {
        saleId: sale_id,
        billReceiveInstallmentId: bill_receive_installment_id,
    });

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

async function commissionCalculation() {
    if (arrangement != null && !arrangement.error) {
        const seller = {
            origin: $(`${modal} #origin-commission-seller`).val(),
            percentage: $(`${modal} #percentage-commission-seller`).val()
        }
        const saleId = $("#sale_id").attr("sale_id");

        let positions = []
        $(`${modal} tr.positions`).map((index, item) => {
            positions.push({
                html_id: item.id,
                origin_commission: $(item).children('td').find('.origin-commission').val(),
                percentage_commission: $(item).children('td').find('.percentage-commission').val()
            })
        });

        const response_commission_calculation = await getCommissionCalculation(arrangement.data.value, arrangement.data.taxes, seller, positions, sale_id)

        if (!response_commission_calculation.error) {
            $(`${modal} #seller-amount`).text(`${response_commission_calculation.data.seller.amount} `)

            response_commission_calculation.data.positions.map((item) => {
                $(`${modal} tr#${item.html_id} `).children('td.amount').text(item.amount)
            });

            $(`${modal} #branch-percentage-real`).text(`${response_commission_calculation.data.branch.percentage.real.toFixed(2)} `)
            $(`${modal} #branch-percentage-virtual`).text(`${response_commission_calculation.data.branch.percentage.virtual.toFixed(2)} `)
            $(`${modal} #branch-amount-real`).text(`${response_commission_calculation.data.branch.amount.real} `)
            $(`${modal} #branch-amount-virtual`).text(`${response_commission_calculation.data.branch.amount.virtual} `)
        }
    }
}

$(`.btn-cancel-installments`).on('click', async function () {
    const modal = "#generic-item-modal";
    const form = "form#form-generic-item";
    const sendTo = $(this).attr('sendTo');
    const bill_receive_id = $(this).attr('bill-receive-id');

    const response = await post(`sales/checkPaidInstallments`, { bill_receive_id: bill_receive_id });
    let html = '<input type="hidden" id="payment-transaction" name="payment_transaction" value="0">';
    if (response.data.bill_receive.length > 0) {
        $("#payment-transaction").val("-1");
        $(`${modal} button#btn-submit`).attr('disabled', true);
        if (response.data.bill_pay.length > 0) {
            html += `<p>Existem parcelas que foram pagas em <strong>contas a receber</strong> e em <strong>contas a pagar</strong> deste arranjo.<br>O que você deseja fazer?</p>`;
            html += `<div class="radio">
                        <label class="radio" style="margin-bottom: 0.625rem;">
                            <input type="radio" class="radio option-transaction-click" name="option-payment" id="option-payment-1" value="1">Gerar <strong>créditos</strong> ao cliente comprador e <strong>débitos</strong> ao clientes fornecedores.
                        </label>                        
                        <label class="radio" style="margin-bottom: 0.625rem;">
                            <input type="radio" class="radio option-transaction-click" name="option-payment" id="option-payment-2" value="2">Manter apenas essas parcelas, <strong>sem alterações</strong>.
                        </label>                        
                    </div>`;
        } else {
            html += `<p>Existem parcelas que foram pagas em <strong>contas a receber</strong> deste arranjo.<br>O que você deseja fazer?</p>`;
            html += `<div class="radio">
                        <label class="radio" style="margin-bottom: 0.625rem;">
                            <input type="radio" class="radio option-transaction-click" name="option-payment" id="option-payment-1" value="1">Gerar <strong>créditos</strong> ao cliente comprador.
                        </label>
                        <label class="radio" style="margin-bottom: 0.625rem;">
                            <input type="radio" class="radio option-transaction-click" name="option-payment" id="option-payment-2" value="2">Manter apenas essas parcelas, <strong>sem alterações</strong>.
                        </label>                                            
                    </div>`;
        }
    } else {
        html += `<p>Deseja realmente excluir as parcelas desse arranjo?</p>`;
    }

    /**Selecionou a operação */

    $(document).on("click", `${modal} .option-transaction-click`, function () {
        $("#payment-transaction").val($(this).val());
    });

    $(document).on("click", `${modal} .option-transaction-click`, function () {
        $(`${modal} button#btn-submit`).removeAttr("disabled");

        $(`${modal} button#btn-submit`).on("click", function () {
            $(`${modal}`).submit();
        })
    })

    if ($(`${modal} #payment-transaction`).val() == "-1") {
        $(`${modal} button#btn-submit`).on('click', function (event) {
            event.preventDefault();
        })
    }

    $(`${modal}`).modal('show');
    $(`${modal} ${form}`).attr('action', sendTo);

    $(`${modal} div.modal-body`).html(html)
    $(`${modal} h4.modal-title`).html("Excluir parcelas da Comissão");
    $(`${modal} button#btn-submit`).removeClass(['btn-danger', 'btn-success', 'btn-warning', 'btn-info', 'btn-primary']).removeAttr('data-dismiss').addClass('btn-danger').html("Confirmar");

});

$('.btn-arrangement').on('click', async function () {
    bill_receive_installment_id = $(this).attr('id');
    sale_id = $(this).attr('sale_id');
    const sendTo = $(this).attr('sendTo');

    arrangement = await post("commissionArrangement/getPaymentArrangementInstallment", { sale_id, bill_receive_installment_id });
    let commission_calculation = await getCommissionCalculation(arrangement.data.value, arrangement.data.taxes, arrangement.data.seller, arrangement.data.positions, sale_id);

    modal = "#arrangement-modal";
    $(`${modal}`).modal('show');
    $(`${modal} form.form-arrangement`).attr('action', url + sendTo);
    $(`${modal} h4.modal-title`).html("Arranjo de Pagamento da Parcela");
    $(`${modal} button#btn-submit`).removeClass(['btn-danger', 'btn-success', 'btn-warning', 'btn-info', 'btn-primary']).removeAttr('data-dismiss').addClass('btn-primary').html("Salvar");

    $(`${modal} #commission-gross`).text(commission_calculation.data.commission.gross)
    $(`${modal} #taxes-real`).text(`${arrangement.data.taxes.real} %`)
    $(`${modal} #taxes-virtual`).text(`${arrangement.data.taxes.virtual} %`)
    $(`${modal} #taxes-amount-real`).text(`${commission_calculation.data.taxes.amount.real}`)
    $(`${modal} #taxes-amount-virtual`).text(`${commission_calculation.data.taxes.amount.virtual}`)
    $(`${modal} #commission-real`).text(`${commission_calculation.data.taxes.commission.real}`)
    $(`${modal} #commission-virtual`).text(`${commission_calculation.data.taxes.commission.virtual}`)

    $(`${modal} #name_seller`).text(`${arrangement.data.seller.name}`);
    $(`${modal} #percentage-commission-seller`).val(arrangement.data.seller.percentage).addClass('arrangement-state')
    $(`${modal} #origin-commission-seller`).val(`${arrangement.data.seller.origin}`).trigger('change').addClass('arrangement-state')
    $(`${modal} #value_origin_commission_seller`).val(arrangement.data.seller.origin)
    $(`${modal} #seller-amount`).text(`${commission_calculation.data.seller.amount}`)

    $(`${modal} #branch-percentage-real`).text(`${commission_calculation.data.branch.percentage.real.toFixed(2)}`)
    $(`${modal} #branch-percentage-virtual`).text(`${commission_calculation.data.branch.percentage.virtual.toFixed(2)}`)
    $(`${modal} #branch-amount-real`).text(`${commission_calculation.data.branch.amount.real}`)
    $(`${modal} #branch-amount-virtual`).text(`${commission_calculation.data.branch.amount.virtual}`)

    const html_position = commission_calculation.data.positions.map((position) => {

        return `<tr id="position_${position.id}" class="positions">
                    <td>
                        <input type="hidden" name="positions[${position.id}][id]" value="${position.id}">                        
                        ${position.customer_name}
                    </td>                                           
                    <td>
                        <input type="hidden" name="positions[${position.id}][origin_commission]" value="${position.origin_commission}" id="value_origin_commission_seller">
                        <select class="form-control origin-commission arrangement-state" name="positions[${position.id}][origin_commission]" disabled>
                            <option value="1" ${position.origin_commission == 1 ? 'selected' : ''}>Bruta</option>
                            <option value="2" ${position.origin_commission == 2 ? 'selected' : ''}>Líquida Real</option>
                            <option value="3" ${position.origin_commission == 3 ? 'selected' : ''}>Líquida Virtual</option>
                        </select>
                    </td>
                    <td>
                        <div class="input-group">
                            <input type="number" placeholder="0.1" class="form-control percentage-commission arrangement-state" name="positions[${position.id}][percentage_commission]" value="${position.percentage_commission}" min="0" max="100" step="0.01" readonly>
                            <span class="input-group-addon">%</span>
                        </div>
                    </td>
                    <td class="text-right amount">${position.amount}</td>
                </tr> `;
    });

    $(`${modal} tr.positions`).remove()
    $(`${modal} #seller`).after(html_position);

    await renderSelectCustomer();
    $("select").select2();

});

$(document).on('change', ".arrangement-state", async function () {
    await commissionCalculation();
});

$(`#new-position-submit`).on('click', async function () {
    const customerId = $(`#new-position-customer`).val()
    const origin = $(`#new-position-origin`).val()
    const costCenter = $(`#new-position-cost-center`).val()
    const formPayment = $(`#new-position-form-payment`).val()
    const percentage = $(`#new-position-percentage`).val()
    if (customerId != null && percentage > 0 && costCenter != null && formPayment != null) {
        const response = await post("paymentAgreement/createParticipantInPaymentArrangementOnBillReceiveInstallment", {
            billReceiveInstallmentId: bill_receive_installment_id,
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
                            <td>
                                <select name="positions[${response.data.id}][origin_commission]" class="origin-commission form-control arrangement-state">                                
                                    <option value="1" ${origin == 1 ? 'selected' : ''}>Bruta</option>                                
                                    <option value="2" ${origin == 2 ? 'selected' : ''}>Líquida Real</option>                                
                                    <option value="3" ${origin == 3 ? 'selected' : ''}>Líquida Virtual</option>                                
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

            await commissionCalculation();
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
    let response = await post("paymentAgreement/removeParticipantFromPaymentAgreementOfTheInvoiceReceiveInstallment", { id: id });

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