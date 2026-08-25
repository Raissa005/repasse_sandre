jQuery(function() {
    $("#logo").on("change", function() {
        const filename = $(this).val().split("\\").pop();
        $("#filename-logo").html(filename);
    });
    
    /** Modal */

    const rowsProperties = 10;
    let button = [];

    $('#next-modal-properties').on('click', function () {
        const pageProperties = (parseInt($("#page-properties-ajax").val()));
        $("#page-properties-ajax").val(pageProperties + 1);
    });

    $('#back-modal-properties').on('click', function () {
        const pageProperties = (parseInt($("#page-properties-ajax").val()));
        $("#page-properties-ajax").val(pageProperties - 1);
    });

    $("#modal-add-branch-properties-modal, #next-modal-properties, #back-modal-properties").on("click", async function () {
        const pageProperties = (parseInt($("#page-properties-ajax").val()));
        pageProperties <= 1 ? $("#back-modal-properties").hide() : $("#back-modal-properties").show();

        let html = '';
        const filter = { rows: rowsProperties, page: pageProperties };

        let ajax = await post('property/getAndFilterAllProperties', filter);
        const properties = ajax.data.properties;
        const nextPage = ajax.data.next_properties;
        const selectAll = ajax.data.all_properties;

        nextPage <= 1 ? $("#next-modal-properties").hide() : $("#next-modal-properties").show();

        properties.map((property) => {
            const buttonClass = button.includes(property.id) ? 'danger' : 'success';
            const iconClass = button.includes(property.id) ? 'fa-times' : 'fa-check';

            html += `<tr>
                        <td class="text-center">${property.cod + ' - ' + property.name}</td>
                        <td class="text-center">${property.property_type_name + ' - ' + property.property_category_name}</td>
                        <td class="text-center">${property.uf_state + ' - ' + property.cities_name}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-${property ? buttonClass : 'default'} btn-sm btn-property-branches" id="${property.id}" title='Adicionar imóvel'>
                                <i class="fa ${property ? iconClass : 'fa-circle'} icon" style="width: 12px;"></i>
                            </button>
                        </td>
                    </tr>`;
            $('#branch_properties').html(html);
        })

        $('.btn-property-branches').on('click', function () {
            if ($(this).hasClass('btn-success')) {
                $(this).removeClass('btn-success').addClass('btn-danger');
                $(this).find('i').removeClass('fa-check').addClass('fa-times');
                button = [...button, $(this).attr('id')];
            } else {
                $(this).removeClass('btn-danger').addClass('btn-success');
                $(this).find('i').removeClass('fa-times').addClass('fa-check');
                button = button.filter(property => property != $(this).attr('id'));
            }
            $("input#property_branch").val([button]);
        });

        $('#select-all-properties').on('click', function () {
            if ($(this).hasClass('btn-primary')) {
                $(this).text('Desmarcar todos');
                selectAll.map((property) => {
                    $(this).removeClass('btn-primary').addClass('btn-warning');
                    $(`button[id=${property.id}]`).removeClass('btn-success').addClass('btn-danger');
                    $(`button[id=${property.id}]`).find('i').removeClass('fa-check').addClass('fa-times');
                    button = [...button, property.id];
                });
            } else {
                $(this).text('Selecionar todos');
                selectAll.map((property) => {
                    $(this).removeClass('btn-warning').addClass('btn-primary');
                    $(`button[id=${property.id}]`).removeClass('btn-danger').addClass('btn-success');
                    $(`button[id=${property.id}]`).find('i').removeClass('fa-times').addClass('fa-check');
                    button = [];
                });
            }
            $("input#property_branch").val([button]);
        })
    });
});