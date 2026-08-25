jQuery(function () {
    const attendanceId = $("#id_attendance").val();

    /**Cadastro de interesses do atendimento */

    $("#filter_type").on("change", function () {
        const arrayFilters = {
            1: 'resource',
            2: 'category',
            3: 'location',
            4: 'price',
            5: 'property_type',
        }

        if ($(this).val() != 1) {
            /**Diferente de caracteristicas */
            $(`.${arrayFilters[1]}`).hide();
            $(`#tvalue, #svalue`).removeAttr("required");
        }

        if ($(this).val() != 2) {
            /**Diferente de categoria */
            $(`.${arrayFilters[2]}`).hide();
            $(`#${arrayFilters[2]}`).removeAttr("required");
        }

        if ($(this).val() != 3) {
            /**Diferente de localização */
            $(`.${arrayFilters[3]}`).hide();

        }

        if ($(this).val() != 4) {
            /**Diferente de preço */
            $(`.${arrayFilters[4]}`).hide();
            $(`#start_price, #end_price`).removeAttr('required');
        }

        if ($(this).val() != 5) {
            /**Diferente de tipo imóvel */
            $(`.${arrayFilters[5]}`).hide();
            $(`#${arrayFilters[5]}`).removeAttr('required');
        }

        switch ($(this).val()) {
            case "1":
                /**Características: */
                $(`.${arrayFilters[$(this).val()]}`).show();

                $(`#${arrayFilters[$(this).val()]}`).on('change', function () {
                    const resourceId = $(this).val();
                    $.post({
                        url: url + "ajax/global/getGenericoById/",
                        dataType: 'json',
                        data: { table: 'immovable_resource', id: resourceId },
                        async: false,

                        success: function (response) {
                            const { error, message, data } = response;

                            if (!error) {
                                switch (data.data_type) {
                                    case '1':
                                        /**Texto livre */
                                        $(`#tvalue`).attr({
                                            "required": true,
                                            'type': 'text'
                                        });
                                        break;
                                    case '2':
                                        /**Número */
                                        $(`#tvalue`).attr({
                                            "required": true,
                                            'type': 'number'
                                        });
                                        break;
                                    case '3':
                                        /**Data */
                                        $(`#tvalue`).attr({
                                            "required": true,
                                            'type': 'date'
                                        });
                                        break;
                                    case '4':
                                        /**Sim / Não */
                                        $(`.svalue`).show();
                                        $(`#svalue`).attr('required', true);
                                        break;
                                }

                                if (data.data_type != 4) {
                                    $(`.svalue`).hide();
                                    $(`#svalue`).removeAttr('required');
                                    $(`.tvalue`).show();
                                } else {
                                    $(`#tvalue`).removeAttr("required");
                                    $(`.tvalue`).hide();
                                }

                            }
                        }
                    });
                }).trigger('change');

                break;
            case "2":
                /**Categoria: */
                $(`.${arrayFilters[$(this).val()]}`).show();
                $(`#${arrayFilters[$(this).val()]}`).attr('required', true);

                break;
            case "3":
                /**Localização: */
                $(`.${arrayFilters[$(this).val()]}`).show();

                let citiesInterest = [];

                $.post({
                    url: url + "ajax/global/getItemByGenericFieldArray/",
                    dataType: 'json',
                    data: {
                        array: {
                            id_attendance_filter_type: 3,
                            id_attendance: attendanceId,
                        },
                        table: 'attendance_filters_interests',
                    },
                    xhrFields: {
                        withCredentials: true
                    },
                    async: false,

                    success: function (response) {
                        const { error, message, data } = response;
                        if (!error) {
                            if (data.length > 0) {
                                citiesInterest = JSON.parse(data[0].json);
                            }
                        }
                    }
                });

                /**Alterando o estado */
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
                            let { cities } = response;
                            let html = "";

                            cities = cities.filter((city) => {
                                return !citiesInterest.includes(city.id);
                            });

                            cities.forEach(city => {
                                html += `<option value="${city.id}">${titleize(city.name)}</option>`;
                            });

                            $("#id_city").html(html);
                        }
                    });
                });

                // Search neighborhoods by city.
                $("#id_city").on("change", function () {
                    const id_city = $(this).val();
                    $.ajax({
                        url: url + "ajax/ajax/getNeighborhoodsByCityId",
                        dataType: 'json',
                        method: 'POST',
                        data: {
                            id_city: id_city
                        },
                        async: false,

                        success: function (response) {
                            let { neighborhoods } = response;
                            let opt = "";

                            if (neighborhoods.length !== 0) {
                                neighborhoods.forEach(el => {
                                    opt += `<option value="${el.neighborhood}">${el.neighborhood}</option>`;
                                });

                                $("#neighborhood").html(opt);
                            } else {
                                $("#neighborhood").html(`<option value="" disabled>Nem um bairro encontrado</option>`);
                            }
                        }
                    });
                }).trigger('change');
                break;
            case "4":
                /**Preço: */
                $(`.${arrayFilters[$(this).val()]}`).show();
                $(`#start_price, #end_price`).attr('required', true);

                $.post({
                    url: url + "ajax/global/getItemByGenericFieldArray/",
                    dataType: 'json',
                    data: {
                        array: {
                            id_attendance_filter_type: 4,
                            id_attendance: attendanceId,
                        },
                        table: 'attendance_filters_interests',
                    },
                    xhrFields: {
                        withCredentials: true
                    },
                    async: false,

                    success: function (response) {
                        const { error, message, data } = response;
                        if (!error) {
                            if (data.length > 0) {
                                price = JSON.parse(data[0].json);

                                if (price.start_price !== undefined) {
                                    $(`#start_price`).val(formatDecimal(price.start_price, 2));
                                }

                                if (price.end_price !== undefined) {
                                    $(`#end_price`).val(formatDecimal(price.end_price, 2));
                                }
                            }
                        }
                    }
                });
                break;
            case "5":
                /**Tipo Imóvel: */
                $(`.${arrayFilters[$(this).val()]}`).show();
                $(`#${arrayFilters[$(this).val()]}`).attr('required', true);
                break;
        }
    }).trigger("change");

    /**Busca de imoveis com os filtros de interesses do atendimento */
    let properties = [];
    $(document).on("click", ".modal-pagination-properties-interests li a.modal-page-link", function () {
        const page = parseInt($(this).attr('page'));
        $("#page-properties-interests-ajax").val(page);
    });

    $(document).on("click", "#modal-properties-interests, .modal-pagination-properties-interests li a.modal-page-link", async function () {
        const page = parseInt($("#page-properties-interests-ajax").val());
        const response_properties = await post('attendance/getPropertiesByFilteringByAttendanceInterest/', {
            attendance_id: attendanceId,
            page: page,
        })
        const html_properties = response_properties.data.map(property => {
            const bg_button = properties.includes(property.id) ? 'danger' : 'success';
            return `<tr>
                        <td>${(property.cod != "" ? property.cod : property.id)}<div>${property.name}</div></td>
                        <td class="text-center">${property.property_type_name + " - " + property.property_category_name}</td>
                        <td class="text-center">${property.cities_name} - ${property.cities_uf}</td>
                        <td class="text-center">${property.value}</td>
                        <td class="text-center" style="width: 120px;">
                            <a href="${url + "property/editItem/" + property.id}" target="_blank" title="Visualizar Imóvel" class="btn btn-warning btn-sm"><i class="fa fa-eye"></i></a>
                            <button type="button" class="btn btn-${bg_button} btn-sm btn-property-presentation" id="${property.id}" title='Apresentar Imóvel'><i class="fas fa-arrow-alt-circle-up"></i></button >
                        </td >
                    </tr> `;
        });

        $("#properties-interests").html(html_properties);

        let pagination = page > 1 ? `<li class="page-item"><a class="page-link modal-page-link" page="${page - 1}">&laquo;</a></li>` : "";
        for (let index = response_properties.pagination.min; index <= response_properties.pagination.max; index++) {
            pagination += `<li class="page-item ${page == index ? "active" : ""}"><a class="page-link modal-page-link" page="${index}">${index}</a></li>`;
        }
        pagination += page < response_properties.pagination.max ? `<li class="page-item"><a class="page-link modal-page-link" page="${page + 1}">&raquo;</a></li>` : '';
        $(".modal-pagination-properties-interests").html(pagination);

        if ($(this).is('#modal-properties-interests')) {
            $('#properties-interests-modal').modal('show');
        }
    });

    /**Selecionar os imóveis para apresentação */

    $("form#properties-presentation-form").hide();

    $(document).on("click", ".btn-property-presentation", function () {
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

});