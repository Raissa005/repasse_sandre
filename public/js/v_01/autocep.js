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

                        $("#estado").val(dadosCep.uf).change();
                        var cidade = $("#cidade option").filter(function () {
                            return $(this).text().toUpperCase() === dadosCep.localidade.toUpperCase();
                        }).first().attr("value");

                        $("#cidade").val(cidade).change();
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