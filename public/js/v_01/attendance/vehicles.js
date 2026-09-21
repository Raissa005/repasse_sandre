jQuery(function() {

    /**Adicionar veículo apresentado no atendimento */

    const attendanceId = $("#modal-add-vehicles-presentations-modal").data("id-attendance");

    const rows = 10;
    let vehicles = [];

    $("#next-modal-vehicles").on("click", function() {
        const page = parseInt($("#page-vehicles-ajax").val());
        $("#page-vehicles-ajax").val(page + 1);
    });

    $("#back-modal-vehicles").on("click", function() {
        const page = parseInt($("#page-vehicles-ajax").val());
        $("#page-vehicles-ajax").val(page - 1);
    });

    $("#modal-add-vehicles-presentations-modal, #next-modal-vehicles, #back-modal-vehicles, #search_vehicles").on("click", function() {
        const page = parseInt($("#page-vehicles-ajax").val());
        page <= 1 ? $("#back-modal-vehicles").hide() : $("#back-modal-vehicles").show();

        let searchName = $("#search_name_vehicles").val();
        let searchCod = $("#search_cod_vehicles").val();
        let searchBrand = $("#search_brand_vehicles").val();

        $.ajax({
            url: url + "ajax/attendance/searchVehiclesForPresentation/",
            dataType: "json",
            method: "POST",
            data: {
                page: page,
                rows: rows,
                name: searchName,
                cod: searchCod,
                id_brand: searchBrand,
                attendance_id: attendanceId,
            },
            async: false,

            success: function(response) {
                const { error, vehicles: vehiclesFound, nextPage } = response;
                if (!error) {
                    nextPage ? $("#next-modal-vehicles").show() : $("#next-modal-vehicles").hide();

                    const html = vehiclesFound.map((vehicle) => {
                        const buttonClass = vehicles.includes(vehicle.id) ? 'danger' : 'success';
                        return `<tr>
                            <td style="vertical-align: middle;">${vehicle.id} - ${vehicle.name}</td>
                            <td class="text-center" style="vertical-align: middle;">${vehicle.brand_name} - ${vehicle.model_name}</td>
                            <td class="text-center text-uppercase" style="vertical-align: middle;">${vehicle.plate ?? ''}</td>
                            <td class="text-center" style="vertical-align: middle;">${vehicle.vehicle_sales_value}</td>
                            <td class="text-center">
                                <a href="${url + "vehicles/editItem/" + vehicle.id}" target="_blank" class="btn btn-warning btn-sm">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <button type="button" class="btn btn-${vehicle.already_presented ? "default" : buttonClass} btn-sm btn-vehicle-presentation" ${vehicle.already_presented ? "disabled" : ""} id="${vehicle.id}" ${vehicle.already_presented ? "" : "title='Apresentar Veículo'"}>
                                    <i class="fas fa-arrow-alt-circle-up"></i>
                                </button>
                            </td>
                        </tr>`;
                    });

                    $("#vehicles_attendance").html(html);
                }
            }
        });
    });

    $("form#vehicles-presentation-form").hide();

    $(document).on("click", ".btn-vehicle-presentation", function() {
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
