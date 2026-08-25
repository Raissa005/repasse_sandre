$("#submit").on('click', e => {
    if ($("#id_cost_center").val().length == 0 || $("#property").val().length == 0) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            confirmButtonColor: '#367FA9',
            title: 'Oops...',
            text: 'Confirme se a Imóveis ou Centro de Custo vinculados!',
        })
    }
});

const rowsProperties = 10;
let button = [];
button = $("input#property").data('id-selected').length > 0 ? $("input#property").data('id-selected').split(',') : [];

$('#next-modal-properties').on('click', function () {
    const pageProperties = (parseInt($("#page-properties-ajax").val()));
    $("#page-properties-ajax").val(pageProperties + 1);
});

$('#back-modal-properties').on('click', function () {
    const pageProperties = (parseInt($("#page-properties-ajax").val()));
    $("#page-properties-ajax").val(pageProperties - 1);
});

$(document).on("click", "#modal-add-properties-modal, #next-modal-properties, #back-modal-properties, #search", async function () {
    const pageProperties = (parseInt($("#page-properties-ajax").val()));
    pageProperties <= 1 ? $("#back-modal-properties").hide() : $("#back-modal-properties").show();

    let html = '';
    let nameProperties = $("#search_name").val();
    let codProperties = $("#search_cod").val();
    const filter = { rows: rowsProperties, page: pageProperties, status: 1, name: nameProperties, cod: codProperties };

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
                        <button type="button" class="btn btn-${property ? buttonClass : 'default'} btn-sm btn-property" id="${property.id}" title='Adicionar imóvel'>
                            <i class="fa ${property ? iconClass : 'fa-circle'} icon" style="width: 12px;"></i>
                        </button>
                    </td>
                </tr>`;
        $('#properties').html(html);
    })

    $('.btn-property').on('click', function () {
        if ($(this).hasClass('btn-success')) {
            $(this).removeClass('btn-success').addClass('btn-danger');
            $(this).find('i').removeClass('fa-check').addClass('fa-times');
            button = [...button, $(this).attr('id')];
        } else {
            $(this).removeClass('btn-danger').addClass('btn-success');
            $(this).find('i').removeClass('fa-times').addClass('fa-check');
            button = button.filter(property => property != $(this).attr('id'));
        }
        $("input#property").val([button]);
        $("input#property").change();
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
        $("input#property").val([button]);
        $("input#property").change();
    })
});

$("input#property").val([button]);

$("input#property").on('change', async function () {
    const properties = $(this).val().split(',');
    if (properties[0] == '') {
        $(".box-inputs").addClass('hidden');
        $(".properties-input").html('');
        return;
    }
    $(".box-inputs").removeClass('hidden');

    let html = '';
    for (let i = 0; i < properties.length; i++) {
        const id_property = properties[i];
        const { data: property } = await post('property/getPropertyNameAndCodById', { id_property });
        html += `
            <div class="col-md-4">
                <div class="form-group">
                    <label for="${property.id}">Valor F.R.T. Cód. ${property.cod}</label>
                    <label for="${property.id}" class="pull-right"><span><input type="checkbox" name="checkBox[${property.id}]" value="1" "></span> Permuta</label>
                    <input type="text" id="${property.id}" name="${property.id}" class="form-control" data-mask-money>
                </div>
            </div>
        `;
    }

    $('.properties-input').html(html);
    $('[data-mask-money]').maskMoney({
        decimal: ',',
        thousands: '.',
        prefix: 'R$ ',
        affixesStay: true
    });
});
