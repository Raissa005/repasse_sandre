jQuery(function () {
    $("#cnpj").blur(function () {
        const cnpj = $(this).val().replace(/\D/g, '');
        const validaCnpj = /^[0-9]{14}$/;
        if (validaCnpj.test(cnpj)) {
            $.ajax({
                url: "https://www.receitaws.com.br/v1/cnpj/" + cnpj,
                type: 'GET',
                dataType: 'jsonp',
                complete: function (xhr) {
                    response = xhr.responseJSON
                    if (response.status != "ERROR") {
                        $("#company_name").val(titleize(response.nome));
                        if (response.fantasia) {
                            $("#fancy_name_company").val(titleize(response.fantasia));
                        } else {
                            $("#fancy_name_company").val(titleize(response.nome));
                        }

                        $("#name").val(titleize(response.qsa[0].nome));
                        $("#birth_date").val(response.abertura.split("/").reverse().join("-"));
                        $("#phone").val(response.telefone);
                        $("#email").val(titleize(response.email));
                        $("#cep").val(response.cep).trigger("blur");
                        $("#neighborhood").val(titleize(response.bairro));
                        $("#address").val(titleize(response.logradouro));
                        $("#number_address").val(response.numero);
                    } else {
                        $("#cnpj").val("");
                        alert('CNPJ inválido');
                    }
                },

            });
        }
    });
});
