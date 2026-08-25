jQuery(function () {
    /**Contratos */
    $("#btnSelectProducts").on("click", function () {
        const customerId = $("#customerId").val();
        $.ajax({
            url: url + "ajax/ajax/getAndFilterAllProducts/",
            dataType: "json",
            method: "POST",
            data: {
                page: 0,
                rows: 0,
                id_owner: customerId,
                status: 1,
            },
            async: false,

            success: function (response) {
                const { error, products, nextPage } = response;

                if (!error) {
                    if (products.length == 0) {
                        $('#btn-submit').attr('disabled', 'disabled')
                        $('#btn-submit').prop('title', 'Sem imovel Cadastrado');

                    };

                    const htmlProducts = products.map((product) => {
                        return `<tr>
                            <td class="text-center"><input type="checkbox" name="product[${product.id}]"></td>
                                    <td>${product.cod} - ${product.name}</td>
                                    <td class="text-center">${product.property_type_name}</td>
                                    <td class="text-center">${product.category_name}</td>
                                    <td class="text-center">${titleize(product.city_name)} - ${product.uf}</td>
                                    <td class="text-center">${formatMoney(product.value)}</td>
                                </tr>`;
                    });
                    $("#list_products").html(htmlProducts);
                }
            },
        });
    });

    $(".btnContractProducts").on("click", function () {
        const contractId = $(this).attr("contractId");

        $.ajax({
            url: url + "ajax/global/getItemByGenericField/",
            dataType: "json",
            method: "POST",
            data: {
                value: contractId,
                table: "property_involved_authorization_contract",
                field: "id_property_authorization_contract",
            },
            async: false,

            success: function (response) {
                const { error, message, data } = response;

                if (!error) {
                    let html;
                    data.map((contractProduct) => {
                        $.ajax({
                            url: url + "ajax/ajax/getProductById/",
                            dataType: "json",
                            method: "POST",
                            data: { id_product: contractProduct.id_product },
                            async: false,

                            success: function (response) {
                                const { error, product } = response;
                                if (!error) {
                                    html += `<tr>
                                                <td class="text-center">${product.cod}</td>
                                                <td>${product.name}</td>
                                                <td class="text-center">${product.property_type_name}</td>
                                                <td class="text-center">${product.property_category_name}</td>
                                                <td class="text-center">${titleize(product.city_name)} - ${product.uf}</td>
                                                <td class="text-center">${formatMoney(product.value)}</td>
                                                <td class="text-center">
                                                    <a class="btn btn-warning" target="_blank" href="${url + "property/editItem/" + product.id}">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>`;
                                }
                            },
                        });
                    });

                    $("#list_contract_products").html(html);
                }
            },
        });
    });

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
