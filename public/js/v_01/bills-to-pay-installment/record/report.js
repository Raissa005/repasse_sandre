$("tr.report-cost-center").on("click", async function () {
    const id = $(this).data("id");
    const name = $(this).children().eq(0).text();
    const get = $("#filters").attr('json');
    const response = await post('recordBillsToPayInstallments/getValuesFromCostCenterForRecord', { id_cost_center: id, get: get });
    let html = '';

    $("#cc-title").text(name);
    $("#cc-body").html('');
    if (userSession.branch.current.id == 0) {
        const { cc_values, total_values } = response.data

        $("#table-head").text("Filial");

        for (const key in cc_values) {
            if (Object.hasOwnProperty.call(cc_values, key)) {
                const element = cc_values[key];
                html += `<tr>
                            <td>${element.branch_name}</td>
                            <td><span class="pull-right">${formatMoney(element.amount)}</span></td>
                        </tr>`;
            }
        }

        html += `<tr>
                     <td><b>Total</b></td>
                     <td><span class="pull-right">${formatMoney(total_values)}</span></td>
                 </tr>`;

        $("#cc-body").append(html);
        $("#cost-center-item-modal").modal("show");
        return;
    }
    const { months_values } = response.data;
    console.log(months_values);

    $("#table-head").text("Meses");

    for (const key in months_values) {
        if (Object.hasOwnProperty.call(months_values, key)) {
            const element = months_values[key];
            for (const key in element) {
                if (Object.hasOwnProperty.call(element, key)) {
                    const item = element[key];
                    html += `<tr>
                                <td>${key} - ${item.name}</td>
                                <td><span class="pull-right">${formatMoney(item.amount)}</span></td>
                            </tr>`;
                }
            }
        }
    }

    $("#cc-body").append(html);
    $("#cost-center-item-modal").modal("show");
});

$("#date_type").change(function () {
    var selectedValue = $(this).val();

    if (selectedValue == 3) {
        $("#date_start").prop("type", "month");
        $("#date_end").prop("type", "month");
    } else {
        $("#date_start").prop("type", "date");
        $("#date_end").prop("type", "date");
    }
});
