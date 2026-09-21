jQuery(function () {
    const attendanceId = $("#id_attendance").val();

    /**Cadastro de interesses do atendimento */

    $("#filter_type").on("change", function () {
        const arrayFilters = {
            1: 'brand_model',
            2: 'price',
            3: 'year_km',
        }

        if ($(this).val() != 1) {
            /**Diferente de marca e modelo */
            $(`.${arrayFilters[1]}`).hide();
            $(`#id_brand`).removeAttr("required");
        }

        if ($(this).val() != 2) {
            /**Diferente de faixa de preço */
            $(`.${arrayFilters[2]}`).hide();
            $(`#start_price, #end_price`).removeAttr('required');
        }

        if ($(this).val() != 3) {
            /**Diferente de ano/km */
            $(`.${arrayFilters[3]}`).hide();
        }

        switch ($(this).val()) {
            case "1":
                /**Marca e Modelo: */
                $(`.${arrayFilters[$(this).val()]}`).show();
                $(`#id_brand`).attr('required', true);

                $(`#id_brand`).off('change').on('change', function () {
                    const brandId = $(this).val();

                    if (brandId !== '') {
                        $.ajax({
                            url: url + 'ajax/VehicleModels/getAllItemsByBrandId/' + brandId,
                            dataType: 'json',
                            method: 'POST',
                            data: { brandId: brandId },
                            async: false,

                            success: function (response) {
                                const { models } = response;
                                let html = '<option value="">Todos</option>';

                                if (models.length != 0) {
                                    models.forEach(model => {
                                        html += `<option value="${model.id}">${titleize(model.name)}</option>`;
                                    });
                                }

                                $("#id_model").html(html);
                            }
                        });
                    }
                }).trigger('change');

                break;
            case "2":
                /**Faixa de Preço: */
                $(`.${arrayFilters[$(this).val()]}`).show();
                $(`#start_price, #end_price`).attr('required', true);

                $.post({
                    url: url + "ajax/global/getItemByGenericFieldArray/",
                    dataType: 'json',
                    data: {
                        array: {
                            id_attendance_filter_type: 2,
                            id_attendance: attendanceId,
                        },
                        table: 'attendance_filters_interests',
                    },
                    xhrFields: {
                        withCredentials: true
                    },
                    async: false,

                    success: function (response) {
                        const { error, data } = response;
                        if (!error) {
                            if (data.length > 0) {
                                const price = JSON.parse(data[0].json);

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
            case "3":
                /**Ano/Km: */
                $(`.${arrayFilters[$(this).val()]}`).show();

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
                        const { error, data } = response;
                        if (!error) {
                            if (data.length > 0) {
                                const yearKm = JSON.parse(data[0].json);

                                if (yearKm.year_from) {
                                    $(`#year_from`).val(yearKm.year_from);
                                }

                                if (yearKm.year_to) {
                                    $(`#year_to`).val(yearKm.year_to);
                                }

                                if (yearKm.km_max) {
                                    $(`#km_max`).val(yearKm.km_max);
                                }
                            }
                        }
                    }
                });
                break;
        }
    }).trigger("change");

    /**Busca de veículos com os filtros de interesses do atendimento */
    let vehicles = [];
    $(document).on("click", ".modal-pagination-vehicles-interests li a.modal-page-link", function () {
        const page = parseInt($(this).attr('page'));
        $("#page-vehicles-interests-ajax").val(page);
    });

    $(document).on("click", "#modal-vehicles-interests, .modal-pagination-vehicles-interests li a.modal-page-link", async function () {
        const page = parseInt($("#page-vehicles-interests-ajax").val());
        const response_vehicles = await post('attendance/getVehiclesByFilteringByAttendanceInterest/', {
            attendance_id: attendanceId,
            page: page,
        })
        const html_vehicles = response_vehicles.data.map(vehicle => {
            const bg_button = vehicles.includes(vehicle.id) ? 'danger' : 'success';
            return `<tr>
                        <td>${vehicle.id}<div>${vehicle.name}</div></td>
                        <td class="text-center">${vehicle.brand_name + " - " + vehicle.model_name}</td>
                        <td class="text-center text-uppercase">${vehicle.plate ?? ''}</td>
                        <td class="text-center">${vehicle.vehicle_sales_value}</td>
                        <td class="text-center" style="width: 120px;">
                            <a href="${url + "vehicles/editItem/" + vehicle.id}" target="_blank" title="Visualizar Veículo" class="btn btn-warning btn-sm"><i class="fa fa-eye"></i></a>
                            <button type="button" class="btn btn-${bg_button} btn-sm btn-vehicle-presentation" id="${vehicle.id}" title='Apresentar Veículo'><i class="fas fa-arrow-alt-circle-up"></i></button >
                        </td >
                    </tr> `;
        });

        $("#vehicles-interests").html(html_vehicles);

        let pagination = page > 1 ? `<li class="page-item"><a class="page-link modal-page-link" page="${page - 1}">&laquo;</a></li>` : "";
        for (let index = response_vehicles.pagination.min; index <= response_vehicles.pagination.max; index++) {
            pagination += `<li class="page-item ${page == index ? "active" : ""}"><a class="page-link modal-page-link" page="${index}">${index}</a></li>`;
        }
        pagination += page < response_vehicles.pagination.max ? `<li class="page-item"><a class="page-link modal-page-link" page="${page + 1}">&raquo;</a></li>` : '';
        $(".modal-pagination-vehicles-interests").html(pagination);

        if ($(this).is('#modal-vehicles-interests')) {
            $('#vehicles-interests-modal').modal('show');
        }
    });

    /**Selecionar os veículos para apresentação */

    $("form#vehicles-presentation-form").hide();

    $(document).on("click", ".btn-vehicle-presentation", function () {
        if ($(this).hasClass("btn-success")) {
            $(this).removeClass("btn-success").addClass("btn-danger");
            vehicles = [...vehicles, $(this).attr("id")];
        } else {
            $(this).removeClass("btn-danger").addClass("btn-success");
            vehicles = vehicles.filter(vehicle => vehicle != $(this).attr("id"));
        }
        $("input#vehicles_presentations").val([vehicles]);

        if (vehicles.length == 0) {
            $("form#vehicles-presentation-form").hide()
        } else {
            $("form#vehicles-presentation-form").show()
            if (vehicles.length == 1) {
                $("#btn-submit-vehicles-presentations").text("Apresentar Veículo");
            } else {
                $("#btn-submit-vehicles-presentations").text("Apresentar " + vehicles.length + " Veículos");
            }
        }
    });

});
