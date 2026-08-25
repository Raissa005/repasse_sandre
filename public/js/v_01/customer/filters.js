jQuery(function () {
    $("#id_state").on('change', async function () {
        const id = $(this).val();
        const citySelected = $("#id_city").attr('idcity');

        let city = await post('ajax/getCityByStateId', { id });
        let html = "";

        html += `<option value="">Todos</option>`;
        city.cities.map((city) => {
            html += `<option value="${city.id}" ${city.id == citySelected ? 'selected' : ''}>${titleize(city.name)}</option>`;
        });

        if (city.cities.length === 0) {
            html = "";
            $("#id_city").attr('disabled', true);
        } else {
            $("#id_city").removeAttr('disabled');
        }

        $("#id_city").html(html);

    }).trigger("change");
});