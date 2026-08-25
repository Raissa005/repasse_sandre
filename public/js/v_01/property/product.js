async function checkCode(code) {
    return post('property/checkCode', {
        code
    });
}

jQuery(function () {
    const productId = $("#id_product").val();
    if (productId != undefined) {
        /** Editando imóvel*/

        /**Impedir que o código do imóvel se repita */
        $("#cod").blur(async function () {
            const inputCod = $(this).val();

            if (inputCod !== "") {
                const properties = await checkCode(inputCod);

                if (properties.data.find((element) => productId != element.id)) {
                    $("#cod").val("");
                    $('div#generic-message-modal').modal('show');

                    $('div#generic-message-modal  h4.modal-title').html("Aviso!");
                    $('div#generic-message-modal .modal-body').html("Esse código já existe em outro imóvel!");
                    $('div#generic-message-modal button#btn-confirm').html("Ok");
                    $('div#generic-message-modal button#btn-confirm').removeClass(['btn-danger', 'btn-success', 'btn-warning', 'btn-info', 'btn-primary']);
                    $('div#generic-message-modal button#btn-confirm').addClass('btn-warning');
                }
            }
        });

        $("#id_residential_type").on("change", function () {
            const id_property_type = $(this).val();

            /**Busca as características do tipo do imovel*/
            $.ajax({
                url: url + "ajax/ajax/getAllPropertyTypeResourceByIdPropertyType/",
                dataType: "json",
                method: "POST",
                data: { id_property_type: id_property_type },
                async: false,
                success: function (response) {
                    const { error, propertyTypeResources } = response;
                    if (!error) {

                        /**Busca Imóvel */
                        $.ajax({
                            url: url + "ajax/ajax/getProductById",
                            dataType: "json",
                            method: "POST",
                            data: { id_product: productId },
                            async: false,
                            success: function (response) {
                                const { error, product } = response;
                                if (!error) {
                                    const permission = (product.created_by == userSession.user.id || userSession.profile.access <= 25 ? true : false);

                                    const attrImp = permission ? "" : "disabled";

                                    /**Busca caracteristicas selecionadas */
                                    $.ajax({
                                        url: url + "ajax/ajax/getAllProductOwnershipFeatureByIdProduct/",
                                        dataType: "json",
                                        method: "POST",
                                        data: { id_product: productId },
                                        async: false,
                                        success: function (response) {
                                            const { error, productOwnershipFeature } = response;
                                            if (!error) {

                                                const htmlOptionProductOwnershipFeature =
                                                    propertyTypeResources.map((resource) => {
                                                        let checkbox = "";
                                                        const selectedElement = productOwnershipFeature.findIndex((resourceChecked) => {
                                                            return (
                                                                resourceChecked.id_immovable_resource === resource.id_immovable_resource
                                                            );
                                                        });

                                                        if (selectedElement !== -1) {
                                                            /**Selecionado */
                                                            productOwnershipFeature.splice(selectedElement, 1);

                                                            checkbox = `<div class="col-md-3 col-lg-3">
                                                                                <div class="form-group">
                                                                                    <div class="checkbox">
                                                                                        <label for="property_type_resources_${resource.id_immovable_resource}">
                                                                                            <input type="checkbox" name="propertyTypeResources[]" id="property_type_resources_${resource.id_immovable_resource}" value="${resource.id}" ${attrImp} checked> ${resource.immovable_resource_name}
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            </div>`;

                                                        } else {
                                                            checkbox = `<div class="col-md-3 col-lg-3">
                                                                                <div class="form-group">
                                                                                    <div class="checkbox">
                                                                                        <label for="property_type_resources_${resource.id_immovable_resource}">
                                                                                            <input type="checkbox" name="propertyTypeResources[]" id="property_type_resources_${resource.id_immovable_resource}" value="${resource.id}" ${attrImp}> ${resource.immovable_resource_name}
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            </div>`;
                                                        }
                                                        return checkbox;
                                                    });
                                                $("#editImmovableResource").html(htmlOptionProductOwnershipFeature);
                                            }
                                        },
                                    });
                                }
                            },
                        });

                    }
                },
            });
        }).trigger("change");

    } else {
        /**Adicionando Imóvel */

        /**Impedir que o código do imóvel se repita */
        $("#cod").blur(async function () {
            const inputCod = $(this).val();
            if (inputCod !== "") {
                const properties = await checkCode(inputCod);
                if (properties.data.length > 0) {
                    $("#cod").val("");
                    $('div#generic-message-modal').modal('show');

                    $('div#generic-message-modal h4.modal-title').html("Aviso!");
                    $('div#generic-message-modal .modal-body').html("Esse código já existe em outro imóvel!");
                    $('div#generic-message-modal button#btn-confirm').html("Ok");
                    $('div#generic-message-modal button#btn-confirm').removeClass(['btn-danger', 'btn-success', 'btn-warning', 'btn-info', 'btn-primary']);
                    $('div#generic-message-modal button#btn-confirm').addClass('btn-warning');
                }
            }
        });

        /**Rederizando características do tipo do imóvel */
        $("#id_residential_type").on("change", function () {
            const id_property_type = $("#id_residential_type").val();
            $.ajax({
                url: url + "ajax/ajax/getAllPropertyTypeResourceByIdPropertyType/",
                dataType: "json",
                method: "POST",
                data: { id_property_type: id_property_type },
                async: false,
                success: function (response) {
                    const { error, propertyTypeResources } = response;
                    if (!error) {

                        const htmlCheckboxImmovableResource = propertyTypeResources.map((resource) => {
                            return `<div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <div class="checkbox">
                                                <label for="property_type_resources_${resource.id_immovable_resource}">
                                                    <input type="checkbox" name="propertyTypeResources[]" id="property_type_resources_${resource.id_immovable_resource}" value="${resource.id}" checked> ${resource.immovable_resource_name}
                                                </label>                                                
                                            </div>
                                        </div>
                                    </div>`;
                        });

                        $("#immovableResource").html(htmlCheckboxImmovableResource);
                    }
                },
            });
        }).trigger("change");

    }

    /**Buscando cliente Propreitário */
    const rows = 10;

    $(document).on("click", ".modal-page-link", function () {
        const page = parseInt($(this).attr('page'));
        $("#page-owner-ajax").val(page);
    });

    $(document).on("click", "#modal-owner, #search_owner, .modal-page-link", async function () {
        let searchName = $("#search_name_owner").val();
        const page = parseInt($("#page-owner-ajax").val());

        const response_customer = await post("customer/getCustomersSuppliersAndBuilders", {
            page: page,
            limit: rows,
            name: searchName
        });

        $("#customer-owner-table").html(
            response_customer.data.map((customer) => {
                return `<tr>
                        <td class="text-center">${customer.id}</td>
                        <td>${customer.name}</td>
                        <td class="text-center" cpfcnpj>${customer.cpf_cnpj}</td>
                        <td class="text-center">${customer.cities_name} - ${customer.cities_uf}</td>
                        <td class="text-center">
                            <a target="_blank" href="${url + 'customer/editItem/' + customer.id}" class="btn btn-warning btn-sm" title="Visualizar"><i class="fa fa-eye"></i></a>
                            <button type="button" class="btn btn-success btn-sm owner" id_owner="${customer.id}" title="Selecionar"><i class="fa fa-check"></i></button>
                        </td>
                    </tr>`;
            })
        );
        $("[cpfcnpj]").inputmask({ mask: ["999.999.999-99", "99.999.999/9999-99",], keepStatic: true });
        let pagination = page > 1 ? `<li class="page-item"><a class="page-link modal-page-link" page="${page - 1}">&laquo;</a></li>` : "";
        for (let index = response_customer.pagination.min; index <= response_customer.pagination.max; index++) {
            pagination += `<li class="page-item ${page == index ? "active" : ""}"><a class="page-link modal-page-link" page="${index}">${index}</a></li>`;
        }
        pagination += page < response_customer.pagination.max ? `<li class="page-item"><a class="page-link modal-page-link" page="${page + 1}">&raquo;</a></li>` : '';
        $(".modal-pagination-owner").html(pagination);

        /**Selecionando Proprietário */
        $(".owner").on("click", function () {
            const id_owner = $(this).attr("id_owner");
            $.ajax({
                url: url + "ajax/ajax/getCustomerById/",
                dataType: "json",
                method: "POST",
                data: { id_customer: id_owner },
                async: false,
                success: function (response) {
                    const { error, customer } = response;
                    if (!error) {
                        if (customer.fancy_name_company) {
                            $("#name_owner").val(customer.fancy_name_company);
                            $("#cpf_owner").val(customer.cnpj);
                        } else {
                            $("#name_owner").val(customer.name);
                            $("#cpf_owner").val(customer.person_registration);
                        }
                        $("#id_owner").val(customer.id);
                        $("#customer_owner").attr("href", url + "customer/editItem/" + customer.id);
                        $("#city_owner").val(titleize(customer.city_name) + " - " + customer.uf_state);
                        $("#owner").modal("hide");
                    }
                },
            });
        });
    });

    $("#btn-submit-edit-products").on("click", function (ev) {
        let idOwner = $("#id_owner").val();

        if (idOwner == "") {
            $('div#generic-message-modal').modal('show');

            $('div#generic-message-modal h4.modal-title').html("Aviso!");
            $('div#generic-message-modal .modal-body').html("Selecione o proprietário do Imóvel!");
            $('div#generic-message-modal button#btn-confirm').html("Ok");
            $('div#generic-message-modal button#btn-confirm').removeClass(['btn-danger', 'btn-success', 'btn-warning', 'btn-info', 'btn-primary']);
            $('div#generic-message-modal button#btn-confirm').addClass('btn-warning');
            ev.preventDefault();
        }
    });

    $("#btn-submit-products").on("click", function (ev) {
        let idOwner = $("#id_owner").val();

        if (idOwner == "") {
            $('div#generic-message-modal').modal('show');

            $('div#generic-message-modal h4.modal-title').html("Aviso!");
            $('div#generic-message-modal .modal-body').html("Selecione o proprietário do Imóvel!");
            $('div#generic-message-modal button#btn-confirm').html("Ok");
            $('div#generic-message-modal button#btn-confirm').removeClass(['btn-danger', 'btn-success', 'btn-warning', 'btn-info', 'btn-primary']);
            $('div#generic-message-modal button#btn-confirm').addClass('btn-warning');
            ev.preventDefault();
        }

        let immovable_record = $("#immovable_record").val() + " month";
        let data1 = $("#created_at").val();
        let data2 = $("#re_registered_at").val();

        if (data1 == "" || data1 == undefined || data1.indexOf("-") == -1) {
            $('div#generic-message-modal').modal('show');

            $('div#generic-message-modal h4.modal-title').html("Aviso!");
            $('div#generic-message-modal .modal-body').html("Dígite uma data de cadastro válida!");
            $('div#generic-message-modal button#btn-confirm').html("Ok");
            $('div#generic-message-modal button#btn-confirm').removeClass(['btn-danger', 'btn-success', 'btn-warning', 'btn-info', 'btn-primary']);
            $('div#generic-message-modal button#btn-confirm').addClass('btn-warning');

            ev.preventDefault();
        } else if (data2 == "" || data2 == undefined || data2.indexOf("-") == -1) {
            $('div#generic-message-modal').modal('show');

            $('div#generic-message-modal h4.modal-title').html("Aviso!");
            $('div#generic-message-modal .modal-body').html("Dígite uma data de recadastro válida!");
            $('div#generic-message-modal button#btn-confirm').html("Ok");
            $('div#generic-message-modal button#btn-confirm').removeClass(['btn-danger', 'btn-success', 'btn-warning', 'btn-info', 'btn-primary']);
            $('div#generic-message-modal button#btn-confirm').addClass('btn-warning');
            ev.preventDefault();
        } else {
            $.ajax({
                url: url + "ajax/ajax/compareDate/",
                dataType: "json",
                method: "POST",
                data: {
                    data1: data1,
                    data2: data2,
                    sum: immovable_record,
                },
                async: false,
                success: function (response) {
                    const { error, obj } = response;
                    if (obj == "data2 maior limite" && $("#immovable_record").val() != "") {
                        $('div#generic-message-modal').modal('show');

                        $('div#generic-message-modal h4.modal-title').html("Aviso!");
                        $('div#generic-message-modal .modal-body').html("Data de recadastro está a cima do padrão da filial: " + $("#immovable_record").val() + " meses após a data de cadastro!");
                        $('div#generic-message-modal button#btn-confirm').html("Ok");
                        $('div#generic-message-modal button#btn-confirm').removeClass(['btn-danger', 'btn-success', 'btn-warning', 'btn-info', 'btn-primary']);
                        $('div#generic-message-modal button#btn-confirm').addClass('btn-warning');
                        ev.preventDefault();
                    }
                    if (obj == "data1 maior") {
                        $('div#generic-message-modal').modal('show');

                        $('div#generic-message-modal h4.modal-title').html("Aviso!");
                        $('div#generic-message-modal .modal-body').html("Data de recadastro não pode ser menor que a data de cadastro!");
                        $('div#generic-message-modal button#btn-confirm').html("Ok");
                        $('div#generic-message-modal button#btn-confirm').removeClass(['btn-danger', 'btn-success', 'btn-warning', 'btn-info', 'btn-primary']);
                        $('div#generic-message-modal button#btn-confirm').addClass('btn-warning');
                        ev.preventDefault();
                    }
                },
            });
        }
    });

    $("#btn-submit-products-modal").on("click", function (ev) {
        let immovable_record = $("#immovable_record").val() + " month";
        let data1 = $('#today').val();
        let data2 = $("#new_re_registered_at").val();
        if (data2 == "" || data2 == undefined || data2.indexOf("-") == -1) {
            $('div#generic-message-modal').modal('show');

            $('div#generic-message-modal h4.modal-title').html("Aviso!");
            $('div#generic-message-modal .modal-body').html("Dígite uma data de recadastro válida!");
            $('div#generic-message-modal button#btn-confirm').html("Ok");
            $('div#generic-message-modal button#btn-confirm').removeClass(['btn-danger', 'btn-success', 'btn-warning', 'btn-info', 'btn-primary']);
            $('div#generic-message-modal button#btn-confirm').addClass('btn-warning');
            ev.preventDefault();
        } else {
            $.ajax({
                url: url + "ajax/ajax/compareDate/",
                dataType: "json",
                method: "POST",
                data: {
                    data1: data1,
                    data2: data2,
                    sum: immovable_record,
                },
                async: false,
                success: function (response) {
                    const { error, obj } = response;
                    if (obj == "data2 maior limite" && $("#immovable_record").val() != "") {
                        $('div#generic-message-modal').modal('show');

                        $('div#generic-message-modal h4.modal-title').html("Aviso!");
                        $('div#generic-message-modal .modal-body').html("Data de recadastro está a cima do padrão da filial: " + $("#immovable_record").val() + " meses aṕos o dia de hoje!");
                        $('div#generic-message-modal button#btn-confirm').html("Ok");
                        $('div#generic-message-modal button#btn-confirm').removeClass(['btn-danger', 'btn-success', 'btn-warning', 'btn-info', 'btn-primary']);
                        $('div#generic-message-modal button#btn-confirm').addClass('btn-warning');
                        ev.preventDefault();
                    }
                    if (obj == "data1 maior") {
                        $('div#generic-message-modal').modal('show');

                        $('div#generic-message-modal h4.modal-title').html("Aviso!");
                        $('div#generic-message-modal .modal-body').html("Data de recadastro não pode ser menor que a data de hoje!");
                        $('div#generic-message-modal button#btn-confirm').html("Ok");
                        $('div#generic-message-modal button#btn-confirm').removeClass(['btn-danger', 'btn-success', 'btn-warning', 'btn-info', 'btn-primary']);
                        $('div#generic-message-modal button#btn-confirm').addClass('btn-warning');
                        ev.preventDefault();
                    }
                },
            });
        }
    });

    $(".created_at").on("change", function () {
        let immovable_record = $("#immovable_record").val() + " month";
        if ($("#immovable_record").val() != "") {
            let created_at = $(this).val();
            $.ajax({
                url: url + "ajax/ajax/sumDate/",
                dataType: "json",
                method: "POST",
                data: {
                    date: created_at,
                    sum: immovable_record,
                },
                async: false,
                success: function (response) {
                    const { error, obj } = response;
                    $("#re_registered_at").val(obj);
                },
            });
        }
    });

    /**Chamando Modals */
    $(".btn-reCreated-at").on("click", function () {
        $("#reCreatedAtModal").modal("show");
    });

    $("#open-log-re_registered_at").on("click", function () {
        $("#log-reCreated-Modal").modal("show");
    });

});