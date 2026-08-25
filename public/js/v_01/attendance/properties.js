jQuery(function() {

    /**Adicionar imovel aprensentado no atendimento */

    const attendanceId = $("#modal-add-properties-presentations-modal").data("id-attendance");
    const classChecked = "btn-danger";
    const classNotChecked = "btn-success";
    const checkedIcon = '<i style="width: 12px;" class="fa fa-times"></i>';
    const notCheckedIcon = '<i class="fa fa-check"></i>';

    const rows = 10;
    let properties = [];

    $("#next-modal-properties").on("click", function() {
        const page = parseInt($("#page-properties-ajax").val());
        $("#page-properties-ajax").val(page + 1);
    });

    $("#back-modal-properties").on("click", function() {
        const page = parseInt($("#page-properties-ajax").val());
        $("#page-properties-ajax").val(page - 1);
    });

    $("#modal-add-properties-presentations-modal, #next-modal-properties, #back-modal-properties, #search_properties").on("click", function() {
        const page = parseInt($("#page-properties-ajax").val());
        page <= 1 ? $("#back-modal-properties").hide() : $("#back-modal-properties").show();

        let searchName = $("#search_name_properties").val();
        let searchCod = $("#search_cod_properties").val();
        let searchType = $("#search_property_type_properties").val();
        let searchCategory = $("#search_property_category_properties").val();

        $.ajax({
            url: url + "ajax/ajax/getAndFilterAllProducts/",
            dataType: "json",
            method: "POST",
            data: {
                page: page,
                rows: rows,
                id_branch: userSession.branch.current.id,
                name: searchName,
                cod: searchCod,
                property_type: searchType,
                category: searchCategory,
                id_attendance: attendanceId,
                availability: 1,
                order: ` (
                    SELECT
                        dp.id
                    FROM displayed_properties dp
                    LEFT JOIN attendance a ON a.id = dp.id_attendance
                    WHERE dp.id_product = pdts.id
                    AND a.id = :id_attendance
                    ) ASC `,
                status: 1,
            },
            async: false,

            success: function(response) {
                const { error, products, nextPage } = response;
                if (!error) {
                    $.post({
                        url: url + "ajax/ajax/getAndFilterAllPropertiesPresentationByAttendance/",
                        dataType: 'json',
                        data: {
                            'page': 0,
                            'rows': 0,
                            'attendanceId': attendanceId
                        },
                        async: false,

                        success: function(response) {
                            const { error, propertiesPresentations } = response;

                            if (!error) {
                                const propertiesRelatedToFilters = products.map(product => {
                                    product.properties = false;
                                    const indexPropertiesPresentations = propertiesPresentations.findIndex(pp => product.id === pp.id_product);
                                    if (indexPropertiesPresentations !== -1) {
                                        product.properties = true;
                                    }
                                    return product;
                                });

                                propertiesRelatedToFilters.sort((a) => a.properties == true ? 1 : -1);

                                nextPage ? $("#next-modal-properties").show() : $("#next-modal-properties").hide();
                                const html = products.map((product) => {
                                    const buttonClass = properties.includes(product.id) ? 'danger' : 'success';
                                    const tr = `<tr>
                                        <td style="vertical-align: middle;">${product.cod != "" ? product.cod : product.id} - ${product.name}</td>
                                        <td class="text-center" style="vertical-align: middle;">${product.property_type_name} - ${product.category_name}</td>
                                        <td class="text-center" style="vertical-align: middle;">${titleize(product.city_name)} - ${product.uf}</td>
                                        <td class="text-center" style="vertical-align: middle;">${formatMoney(product.value)}</td>
                                        <td class="text-center">
                                            <a href="${url + "property/editItem/" + product.id}" target="_blank" class="btn btn-warning btn-sm">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-${product.properties ? "default" : buttonClass} btn-sm btn-property-presentation" ${product.properties ? "disabled" : ""} id="${product.id}" ${product.properties ? "" : "title='Apresentar Imóvel'"}>
                                                <i class="fas fa-arrow-alt-circle-up"></i>
                                            </button>
                                        </td>
                                    </tr>`;
                                    return tr;
                                });

                                $("#properties_attendance").html(html);



                            }
                        }
                    });
                }
            }
        });
    });

    $("form#properties-presentation-form").hide();

    $(document).on("click", ".btn-property-presentation", function() {        
        if ($(this).hasClass("btn-success")) {
            $(this).removeClass("btn-success").addClass("btn-danger");
            properties = [...properties, $(this).attr("id")];
        } else {
            $(this).removeClass("btn-danger").addClass("btn-success");
            properties = properties.filter(property => property != $(this).attr("id"));
        }
        $("input#properties_presentations").val([properties]);

        if (properties.length == 0) {
            $("form#properties-presentation-form").hide()
        } else {
            $("form#properties-presentation-form").show()
            if (properties.length == 1) {
                $("#btn-submit-properties-presentations").text("Apresentar Imóvel");
            } else {
                $("#btn-submit-properties-presentations").text("Apresentar " + properties.length + " Imóveis");
            }
        }
    });

    /**Visualizações do imóvel na landing-page */

    $(".viewProperty").on("click", function() {
        const propertyId = $(this).attr("data-id-property");

        $.ajax({
            url: url + "ajax/ajax/getPresentationsIPByIdDisplayed/",
            dataType: "json",
            method: "POST",
            data: { id_displayed_properties: propertyId },
            async: false,

            success: function(response) {
                const { error, presentations } = response;

                if (!error) {
                    const html = presentations.map((view) => {
                        return `<tr>
                                    <td>${view.IP}</td>
                                    <td class="text-center">${normalDateFormat2(view.created_at)}</td>
                                </tr>`;
                    });

                    $("tbody#view_property_table").html(html);
                }
            },
        });
    });

});