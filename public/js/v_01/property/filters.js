$('#id_city').on('change', async function () {
    const cityId = $(this).val();

    $('#neighborhood').attr('disabled', 'true');
    $('#allotment').attr('disabled', 'true');

    if (cityId != '') {
        const neighborhood = $('#get-neighborhood').val()
        const allotment = $('#get-allotment').val()
        let html = "<option value=\"\">Todos</option>";

        $('#neighborhood').removeAttr('disabled');
        $('#allotment').removeAttr('disabled');

        let ajaxNeighborhood = await post('property/getNeighborhoodsByCityId', { cityId: cityId });
        let htmlNeighborhood = html;
        htmlNeighborhood += ajaxNeighborhood.data.map((element) => {
            return `<option value='${element.neighborhood}' ${neighborhood == element.neighborhood ? "selected" : ''}>${element.neighborhood}</option>`;
        });
        $('#neighborhood').html(htmlNeighborhood)

        let ajaxAllotment = await post('property/getAllotmentsByCityId', { cityId: cityId });
        let htmlAllotment = html;
        htmlAllotment += ajaxAllotment.data.map((element) => {
            return `<option value='${element.allotment}' ${allotment == element.allotment ? "selected" : ''}>${element.allotment}</option>`;
        });
        $('#allotment').html(htmlAllotment);

    }
}).trigger('change');