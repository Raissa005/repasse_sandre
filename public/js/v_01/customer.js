jQuery(function() {
    $("#logo").on("change", function() {
        const filename = $(this).val().split("\\").pop();
        $("#filename-logo").html(filename);
    });
});

jQuery(function() {
    /**Tipo Pessoa */
    $("#id_person_type")
        .on("change", function() {
            const id_person_type = $(this).val();

            if (id_person_type == 1) {
                /**Fisica */
                $(".legal_person").hide();

                $("#company_name").removeAttr("required");
                $("#fancy_name_company").removeAttr("required");
                $("#cnpj").removeAttr("required");

                $(".legal_required").show();
            } else {
                /**Juridica */
                $(".legal_person").show();

                $("#company_name").attr("required", "");
                $("#fancy_name_company").attr("required", "");
                $("#cnpj").attr("required", "");

                $("#birth_date").removeAttr("required", "");

                $("#id_profession").val(656).trigger("change");
                $(".legal_required").hide();
            }
        })
        .trigger("change");

    /**Contratos */

    $("#btnSelectProducts").on("click", function() {
        const customerId = $("#customerId").val();
        $.ajax({
            url: url + "api/ajax/getAndFilterAllProducts/",
            dataType: "json",
            method: "POST",
            data: {
                page: 0,
                rows: 0,
                id_owner: customerId,
                status: 1,
            },
            async: false,

            success: function(response) {
                const { error, products, nextPage } = response;

                if (!error) {
                    const htmlProducts = products.map((product) => {
                        return `<tr>
                                    <td class="text-center"><input type="checkbox" name="product[${product.id}]" checked></td>
                                    <td>
                                        ${product.cod} - ${product.name}
                                    </td>
                                    <td class="text-center">
                                        ${product.name_property_type}
                                    </td>
                                    <td class="text-center">
                                        ${product.name_category}
                                    </td>
                                    <td class="text-center">
                                        ${titleize(product.city_name)} - ${product.uf}
                                    </td>
                                    <td class="text-center">
                                        ${formatMoney(product.value)}
                                    </td>
                                </tr>`;
                    });
                    $("#list_products").html(htmlProducts);
                }
            },
        });
    });

    $(".btnContractProducts").on("click", function() {
        const contractId = $(this).attr("contractId");

        $.ajax({
            url: url + "api/ajax/getItemByGenericField/",
            dataType: "json",
            method: "POST",
            data: {
                value: contractId,
                table: "property_involved_authorization_contract",
                field: "id_property_authorization_contract",
            },
            async: false,

            success: function(response) {
                const { error, obj } = response;

                if (!error) {
                    let html;
                    obj.map((contractProduct) => {
                        $.ajax({
                            url: url + "api/ajax/getProductById/",
                            dataType: "json",
                            method: "POST",
                            data: { id_product: contractProduct.id_product },
                            async: false,

                            success: function(response) {
                                const { error, product } = response;
                                if (!error) {
                                    html += `<tr>
                                                <td class="text-center">
                                                    ${product.cod}
                                                </td>
                                                <td>
                                                    ${product.name}
                                                </td>
                                                <td class="text-center">
                                                    ${product.property_type_name}
                                                </td>
                                                <td class="text-center">
                                                    ${product.property_category_name}
                                                </td>
                                                <td class="text-center">
                                                    ${titleize(product.city_name)} - ${product.uf}
                                                </td>
                                                <td class="text-center">
                                                    ${formatMoney(product.value)}
                                                </td>
                                                <td class="text-center">
                                                    <a class="btn btn-warning" target="_blank" href="${url + "property/editItem/" + product.id }">
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
});
