jQuery(function () {
    /**Se for tipo Fornecedor alguns campo não são mas obrigatórios */
    $("#id_customer_type").on("change", function () {
        const customerType = $(this).val();
        const supplier = customerType.filter((obj) => obj === '10').length !== 0 ? true : false;

        if (customerType.length !== 0 && customerType.length == 1 && supplier) {
            $('#nationality, #rg, #cpf, #cellphone, #email, #cep, #neighborhood, #address, #number_address, #complement').removeAttr('required')
            $("label[for='nationality'] span, label[for='rg'] span, label[for='cpf'] span, label[for='cellphone'] span, label[for='phone'] span, label[for='email'] span, label[for='cep'] span, label[for='neighborhood'] span, label[for='address'] span, label[for='number_address'] span, label[for='complement'] span").html('')

            $('#phone').attr('required', true);
            $("label[for='phone'] span").html('*');

            /**Tipo Pessoa */
            $("#id_person_type").on("change", function () {
                const id_person_type = $(this).val();

                if (id_person_type == 1) {
                    /**Fisica */
                    $(".legal_person").hide();

                    $("#company_name, #fancy_name_company, #cnpj").removeAttr("required");
                    $("label[for='company_name'] span, label[for='fancy_name_company'] span, label[for='cnpj'] span").html('');

                    $(".legal_required").show();
                } else {
                    /**Juridica */
                    $(".legal_person").show();
                    $("#company_name, #fancy_name_company, #cnpj").removeAttr("required");
                    $("label[for='company_name'] span, label[for='fancy_name_company'] span, label[for='cnpj'] span").html('');
                    $("#id_profession").val(656).trigger("change");
                    $(".legal_required").hide();
                }
            }).trigger("change");

        } else {

            $.post({
                url: url + "ajax/ajax/requeredCustomerField/",
                dataType: 'json',
                data: {},
                async: false,

                success: function (response) {
                    const { error, requiredField } = response;

                    if (!error) {
                        let require = [];
                        let notRequire = [];

                        for (const key in requiredField) {
                            if (Object.hasOwnProperty.call(requiredField, key)) {
                                if (key != 'id' && key != 'name') {
                                    if (requiredField[key] == 1) {
                                        require.push(key);
                                    } else {
                                        notRequire.push(key);
                                    }
                                }
                            }
                        }

                        /**Campos obrigatórios */
                        require.forEach(element => {
                            $(`#${element}`).attr('required', true);
                            $(`label[for='${element}'] span`).html('*')
                        });

                        /**Campos não obrigatórios */
                        notRequire.forEach(element => {
                            $(`#${element}`).removeAttr('required');
                            $(`label[for='${element}'] span`).html('')
                        });
                    }
                }
            });

            /**Tipo Pessoa */
            $("#id_person_type").on("change", function () {
                const id_person_type = $(this).val();

                if (id_person_type == 1) {
                    /**Fisica */
                    $(".legal_person").hide();

                    $("#company_name, #fancy_name_company, #cnpj").removeAttr("required");
                    $("label[for='company_name'] span, label[for='fancy_name_company'] span, label[for='cnpj'] span").html('');

                    $(".legal_required").show();
                } else {
                    /**Juridica */
                    $(".legal_person").show();

                    $("#company_name, #fancy_name_company, #cnpj").attr("required", "");
                    $("label[for='company_name'] span, label[for='fancy_name_company'] span, label[for='cnpj'] span").html('*');
                    $("#birth_date").removeAttr("required", "");

                    $("#id_profession").val(656).trigger("change");
                    $(".legal_required").hide();
                }
            }).trigger("change");
        }

    }).trigger("change");

});
