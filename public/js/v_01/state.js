jQuery(function () {
    $("#state").on('change', function () {
        const state = $(this).val();
        $.ajax({
            url: url + "ajax/ajax/getCityByState/",
            dataType: 'json',
            method: 'POST',
            data: {
                uf: state
            },
            async: false,

            success: function (response) {
                const { cities } = response;
                let html = "";

                cities.forEach(city => {
                    html += `<option value="${city.id}">${titleize(city.name)}</option>`;
                });

                $("#id_city").html(html);
            }
        });
    });

    $("#cep").blur(function () {
        var cep = $(this).val().replace(/\D/g, '');

        if (cep != "") {
            var validacep = /^[0-9]{8}$/;

            if (validacep.test(cep)) {
                $.ajax({
                    url: "https://viacep.com.br/ws/" + cep + "/json",
                    type: 'GET',
                    dataType: 'json',
                    success: function (dadosCep) {

                        if (!("erro" in dadosCep)) {
                            $("#state").val(dadosCep.uf).change();
                            var city = $("#id_city option").filter(function () {
                                return $(this).text().toUpperCase() === dadosCep.localidade.toUpperCase();
                            }).first().attr("value");
                            $("#id_city").val(city).change();
                        } else {
                            swal('Ops..', 'CEP não encontrado!', 'error');
                        }
                    }
                });
            } else {
                swal('Ops..', 'Formato de CEP Inválido!', 'error');
            }
        }
    });

    // Modifica imputs através do pais
    $("#country").on('change', function () {
        const country = $(this).val();

        if (country !== 41) {
            $("#state").empty();
            $("#id_city").empty();
            $("#div-cep").prop('hidden', true);
            $("#state").prop('disabled', true);
            $("#div-zip").prop('hidden', false);
            $("#id_city").prop('disabled', true);
        }

        if (country == 41) {
            $("#div-zip").prop('hidden', true);
            $("#div-cep").prop('hidden', false);
            $("#state").prop('disabled', false);
            $("#id_city").prop('disabled', false);

            $.ajax({
                url: url + "ajax/ajax/getAllStates/",
                dataType: 'json',
                method: 'POST',

                async: false,
    
                success: function (response) {
                    const { states } = response;
                    let html = "";
    
                    states.forEach(state => {
                        html += `<option value="${state.uf}">${titleize(state.name)}</option>`;
                    });
    
                    $("#state").html(html);
                }
            });
        }
    });
});