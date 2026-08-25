$("#previous-page").hide();

$("#next-page").on('click', function () {
    let page = $("#modal-page").val();
    $("#modal-page").val(parseInt(page) + 1);
});

$("#previous-page").on('click', function () {
    let page = $("#modal-page").val();
    $("#modal-page").val(parseInt(page) - 1);
});

$(document).on("click", "#btn-property-modal, #next-page, #previous-page, #btn-search", async function () {
    const rows = 10;
    const page = parseInt($("#modal-page").val());
    const cod = $("#property-cod").val();
    const name = $("#property-name").val();
    const selected = $("#property-val").val();

    page == 1 ? $("#previous-page").hide() : $("#previous-page").show()

    const response = await post('property/getPropertiesForPaymentModal', { rows, page, cod, name });
    const { data } = response;

    if (data.length == 0) return;
    (data.length < rows) ? $("#next-page").hide() : $("#next-page").show();

    let html = '';
    data.map(item => {
        html += `<tr>
                    <td class="text-center">${item.cod} - ${item.name}</td>
                    <td class="w-4 text-center">
                        <button id="btn-check" class="btn ${item.id == selected ? "btn-warning" : "btn-success"} btn-sm" property-id="${item.id}">
                            <i class="fas fa-check"></i>
                        </button>
                    </td>
                </tr>`;
    });

    $("#table-body-properties").html(html);
    $("#property-modal").modal("show");
});

$(document).on("click", "#btn-check", function () {
    const propertyId = $(this).attr("property-id");
    $("#property-modal").modal("hide");
    $("#property-val").val(propertyId);
});

$('#property-search-modal').on('submit', function (e) {
    e.preventDefault();
    $("#btn-search").click();
});
