jQuery(function () {
    $("#id_state").on('change', function () {
        const state = $(this).val();
        $.ajax({
            url: url + "ajax/ajax/getCityByStateId/",
            dataType: 'json',
            method: 'POST',
            data: {
                uf: state
            },
            async: false,

            success: function (response) {
                const { cities } = response;
                let html = "";

                html += `<option value="">Todos</option>`;
                cities.forEach(city => {
                    html += `<option value="${city.id}">${titleize(city.name)}</option>`;
                });

                $("#id_city").html(html);
            }
        });
    });

});