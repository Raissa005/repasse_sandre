jQuery(function () {
    const rows = 10;

    $(document).on('click', '.modal-page-link', function () {
        const page = parseInt($(this).attr('page'));
        $('#pageBuyerCustomerJax').val(page);
    });

    $(document).on('click', '#modalBuyerCustomer, #searchBuyerCustomer, .modal-page-link', async function () {
        let searchName = $('#searchNameBuyerCustomer').val();
        const page = parseInt($('#pageBuyerCustomerJax').val());

        const responseCustomer = await post('SaleRequests/getCustomersSellerAndBuyer', {
            page: page,
            limit: rows,
            name: searchName
        });

        $('#buyerCustomerTable').html(
            responseCustomer.data.map((customer) => {
                return `<tr>
                            <td class="text-center align-middle">${customer.id}</td>
                            <td class="align-middle">${customer.name}</td>
                            <td class="text-center align-middle" cpfcnpj>${customer.cpf_cnpj}</td>
                            <td class="align-middle">${customer.cityName} - ${customer.cityUF}</td>
                            <td class="text-center align-middle">
                                <a target="_blank" href="${url + 'customer/editItem/' + customer.id}" class="btn btn-warning btn-sm" title="Visualizar"><i class="fa fa-eye"></i></a>
                                <button type="button" class="btn btn-success btn-sm customer" customerId="${customer.id}" title="Selecionar"><i class="fa fa-check"></i></button>
                            </td>
                        </tr>`;
            })
        );

        $('[cpfcnpj]').inputmask({ mask: ['999.999.999-99', '99.999.999/9999-99',], keepStatic: true });

        let pagination = page > 1 ? `<li class="page-item"><a class="page-link modal-page-link" page="${page - 1}">&laquo;</a></li>` : "";

        for (let index = responseCustomer.pagination.min; index <= responseCustomer.pagination.max; index++) {
            pagination += `<li class="page-item ${page == index ? "active" : ""}"><a class="page-link modal-page-link" page="${index}">${index}</a></li>`;
        }

        pagination += page < responseCustomer.pagination.max ? `<li class="page-item"><a class="page-link modal-page-link" page="${page + 1}">&raquo;</a></li>` : '';

        $('.modal-pagination-seller-customer').html(pagination);

        $('.customer').on('click', function () {
            const customerId = $(this).attr('customerId');

            $.ajax({
                url: url + 'ajax/ajax/getCustomerById/',
                dataType: 'json',
                method: 'POST',
                data: { id_customer: customerId },
                async: false,

                success: function (response) {
                    const { error, customer } = response;

                    if (!error) {
                        if (customer.fancy_name_company) {

                            $('#nameBuyerCustomer').val(customer.fancy_name_company);
                            $('#cpfBuyerCustomer').val(customer.cnpj);
                        } else {

                            $('#nameBuyerCustomer').val(customer.name);
                            $('#cpfBuyerCustomer').val(customer.person_registration);
                        }

                        $('#buyerCustomerId').val(customer.id);
                        $('#cityBuyerCustomer').val(titleize(customer.city_name) + " - " + customer.uf_state);
                        $('#buyerCustomer').modal('hide');
                    }
                },
            });
        });
    });

    $('#formAddItem').on('submit', function (event) {
        event.preventDefault();

        var nameBuyerCustomer = $('#nameBuyerCustomer').val().trim();
        var saleBrokerId = $('#saleBrokerId').val();

        if (nameBuyerCustomer === '') {

            Toast.fire({
                icon: 'warning',
                title: 'Campo Cliente / Comprador está vazio!'
            });

            return;
        }

        // if (saleBrokerId === '' || saleBrokerId === undefined) {

        //     Toast.fire({
        //         icon: 'warning',
        //         title: 'Campo Corretor está vazio!'
        //     });

        //     return;
        // }

        $(this).unbind('submit').submit();
    });

    $(document).on('click', '.modal-page-link', function () {
        const page = parseInt($(this).attr('page'));
        $('#pageVehiclesSaleJax').val(page);
    });

    var vehicles = [];
    $(document).on('click', '#modalVehiclesSale, #searchVehiclesSale, .modal-page-link', async function () {
        let searchName = $('#searchNameVehiclesSale').val();
        const page = parseInt($('#pageVehiclesSaleJax').val());

        const responseVehicles = await post('SaleRequests/getVehiclesForSale', {
            page: page,
            limit: rows,
            name: searchName,
            vehicles: vehicles
        });

        $('#vehiclesSaleTable').html(
            responseVehicles.vehicles.map((vehicle) => {
                return `<tr>
                            <td class="text-center align-middle">${vehicle.id}</td>
                            <td class="align-middle">${vehicle.name}</td>
                            <td class="text-center align-middle">${vehicle.plate}</td>
                            <td class="text-center align-middle">${vehicle.vehicle_sales_value}</td>
                            <td class="text-center align-middle">
                                <a target="_blank" href="${url + 'vehicles/editItem/' + vehicle.id}" class="btn btn-warning btn-sm" title="Visualizar"><i class="fa fa-eye"></i></a>
                                <button type="button" class="btn btn-success btn-sm vehicle" vehicleId="${vehicle.id}" title="Selecionar"><i class="fa fa-check"></i></button>
                            </td>
                        </tr>`;
            })
        );

        let pagination = page > 1 ? `<li class="page-item"><a class="page-link modal-page-link" page="${page - 1}">&laquo;</a></li>` : "";
        for (let index = responseVehicles.pagination.min; index <= responseVehicles.pagination.max; index++) {
            pagination += `<li class="page-item ${page == index ? "active" : ""}"><a class="page-link modal-page-link" page="${index}">${index}</a></li>`;
        }

        pagination += page < responseVehicles.pagination.max ? `<li class="page-item"><a class="page-link modal-page-link" page="${page + 1}">&raquo;</a></li>` : '';
        $('.modal-pagination-vehicles-sale').html(pagination);

        $('.vehicle').on('click', function () {
            const vehicleId = $(this).attr('vehicleId');

            $.ajax({
                url: url + 'ajax/SaleRequests/getVehicleById',
                dataType: 'json',
                method: 'POST',
                data: {
                    vehicleId: vehicleId
                },
                async: false,

                success: function (response) {
                    const { error, message, vehicle } = response;
                    let valueCommission = vehicle.value_commission ? parseFloat(vehicle.value_commission).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : '';

                    if (!error) {
                        $('#vehicleName').val(vehicle.name);
                        $('#vehicleIdHidden').val(vehicle.id);
                        $('#vahiclePlate').val(vehicle.plate);
                        $('#brandNameHidden').val(vehicle.brand_name);
                        $('#modelNameHidden').val(vehicle.model_name);
                        $('#colorNameHidden').val(vehicle.color_name);
                        $('#vehicleValue').val(vehicle.vehicle_sales_value);
                        $('#vehicleSalesCommission').val(valueCommission);
                        $('#vehicleSalesValue').val(vehicle.vehicle_sales_value);

                        $('#vehiclesSale').modal('hide');
                    } else {
                        Toast.fire({
                            icon: 'warning',
                            title: message
                        });
                    }
                }
            });
        });
    });

    var totalSaleValue = 0;
    var totalVehiclesValue = 0;
    var count = parseInt($('#totalVehicle').text());
    var totalVehicle = count != undefined || count != '' ? count : 0;
    $('#addVehicleSaleRequest').on('click', function () {
        $('.disableEditButton').addClass('disabled').off('click');
        $('.disableDeleteButton').removeAttr('href').addClass('disabled');

        var name = $('#vehicleName').val();
        var plate = $('#vahiclePlate').val();
        var value = $('#vehicleValue').val();
        var saleId = $('#saleIdHidden').val();
        var vehicleId = $('#vehicleIdHidden').val();
        var saleValue = $('#vehicleSalesValue').val();
        var commission = $('#vehicleSalesCommission').val().replace(/[R$\s.]/g, '').replace(',', '.') ?? '';
        var brandNameHidden = $('#brandNameHidden').val();
        var modelNameHidden = $('#modelNameHidden').val();
        var colorNameHidden = $('#colorNameHidden').val();

        if (!name) {
            Toast.fire({
                icon: 'warning',
                title: 'Nenhum veículo selecionado.'
            });
            return;
        }

        totalVehicle++;
        totalSaleValue += parseFloat(unmaskMoney(saleValue));
        totalVehiclesValue += parseFloat(unmaskMoney(value));

        var totalValue = parseFloat(unmaskMoney($('#totalVehicleLine').text()));
        if (totalValue > 0) {
            total = totalValue + parseFloat(unmaskMoney(value));
        } else {
            total = totalVehiclesValue;
        }

        var totalSale = parseFloat(unmaskMoney($('#totalSaleLine').text()));
        if (totalSale > 0) {
            totalVehicleSale = totalSale + parseFloat(unmaskMoney(saleValue));
        } else {
            totalVehicleSale = totalSaleValue;
        }

        var newVeiculo = {
            vehicleId: vehicleId,
            saleValue: saleValue,
            commission: commission
        };

        vehicles.push(newVeiculo);

        if (brandNameHidden == '') {
            $.ajax({
                url: url + 'ajax/SaleRequests/editVehiclesSale',
                dataType: 'json',
                method: 'POST',
                data: {
                    saleId: saleId,
                    vehicles: vehicles,
                    vehicleId: vehicleId,
                },
                async: false,

                success: function (response) {
                    const { error, message } = response;

                    if (!error) {
                        Toast.fire({
                            icon: 'success',
                            title: message
                        });

                        setTimeout(function () {
                            location.reload();
                        }, 1000);
                    } else {
                        Toast.fire({
                            icon: 'warning',
                            title: message,
                        });
                    }
                }
            });
        } else {
            var nextIndex = vehicles.length - 1;

            var newLine = "<tr data-id='" + nextIndex + "'>" +
                "<td class='align-middle'>" + name + "</td>" +
                "<td class='align-middle'>" + brandNameHidden + "</td>" +
                "<td class='align-middle'>" + modelNameHidden + "</td>" +
                "<td class='align-middle'>" + colorNameHidden + "</td>" +
                "<td class='text-center align-middle'>" + plate + "</td>" +
                "<td class='text-center align-middle'>" + value + "</td>" +
                "<td class='text-center align-middle'>" + saleValue + "</td>" +
                "<td class='text-center align-middle'>" +
                "<button type='button' class='btn btn-danger btn-sm removerLinha'><i class='fa fa-trash'></i></button>" +
                "</td>" +
                "</tr>";

            $('#vehicleSaleTableTbody').append(newLine);

            $('#totalVehicle').text(totalVehicle);

            $('#vehicleSaleTableTbody').find(".totalLine").remove();

            var newTotalLine = "<tr class='totalLine'>" +
                "<td class='text-right' colspan='5'><strong>Total:</strong></td>" +
                "<td class='text-center align-middle' id='totalVehicleLine'><strong>" + formatMoney(total) + "</strong></td>" +
                "<td class='text-center align-middle' id='totalSaleLine'><strong>" + formatMoney(totalVehicleSale) + "</strong></td>" +
                "</tr>";

            $('#vehicleSaleTableTbody').append(newTotalLine);

            $('#vehicleName').val('');
            $('#vahiclePlate').val('');
            $('#vehicleValue').val('');
            $('#vehicleIdHidden').val('');
            $('#vehicleSalesValue').val('');

            $('#saleRequestList').show();
        }
    });

    $('#vehicleSaleTable').on('click', '.removerLinha', function () {
        var $linha = $(this).closest('tr');
        var rowIndex = $linha.data('id');

        if (rowIndex >= 0 && rowIndex < vehicles.length) {
            totalVehiclesValue = parseFloat(unmaskMoney($('#totalVehicleLine').text()));

            totalSaleLine = parseFloat(unmaskMoney($('#totalSaleLine').text()));
            totalSaleValue = (totalSaleLine - parseFloat(unmaskMoney(vehicles[rowIndex].saleValue)));
            vehicles.splice(rowIndex, 1);
            totalVehicle--;

            $('#totalVehicle').text(totalVehicle);
            $('#vehicleSaleTableTbody').find(".totalLine").remove();
            $('#vehicleSaleTableTbody').append("<tr class='totalLine'><td class='text-right' colspan='5'><strong>Total:</strong></td><td id='totalVehicleLine'><strong>" + formatMoney(totalVehiclesValue) + "</strong></td><td id='totalSaleLine' ><strong>" + formatMoney(totalSaleValue) + "</strong></td></tr>");
        }

        $linha.remove();

        var rowCount = $('#vehicleSaleTableTbody tr').length;
        if (rowCount === 0) {
            $('#saleRequestList').hide();
        }
    });

    $('#btnToSave').on('click', function () {
        saleId = $('#saleIdHidden').val();
        let commission = $('#vehicleSalesCommission').val();;

        if (vehicles.length > 0) {
            $.ajax({
                url: url + 'ajax/SaleRequests/addVehiclesSale',
                dataType: 'json',
                method: 'POST',
                data: {
                    saleId: saleId,
                    vehicles: vehicles,
                    totalSaleValue: totalSaleValue,
                    commission: commission
                },
                async: false,

                success: function (response) {
                    const { error, message } = response;

                    if (!error) {
                        $('.disableEditButton').removeClass('disabled');
                        $('.disableDeleteButton').attr('href', 'URL . $this->route . "/deleteVehiclesSale/$vehicle->id').removeClass('disabled');

                        Toast.fire({
                            icon: 'success',
                            title: message
                        });

                        setTimeout(function () {
                            location.reload();
                        }, 1000);
                    } else {
                        Toast.fire({
                            icon: 'warning',
                            title: "Erro ao enviar os dados!",
                        });
                    }
                },
                error: function (xhr, status, error) {
                    Toast.fire({
                        icon: 'warning',
                        title: "Erro ao enviar os dados!",
                    });
                }
            });
        } else {
            $('.disableEditButton').removeClass('disabled');
            $('.disableDeleteButton').attr('href', 'URL . $this->route . "/deleteVehiclesSale/$vehicle->id').removeClass('disabled');

            location.reload();
        }
    });

    $('.editVehiclesSale').on('click', function () {
        vehicleId = $(this).attr('id');

        $.ajax({
            url: url + 'ajax/SaleRequests/getVehicleById',
            dataType: 'json',
            method: 'POST',
            data: {
                vehicleId: vehicleId
            },
            async: false,

            success: function (response) {
                const { error, message, vehicle } = response;
                let valueCommission = vehicle.value_commission ? parseFloat(vehicle.value_commission).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : '';

                if (!error) {
                    $('#modalVehiclesSale').prop('disabled', true);

                    $('#vehicleName').val(vehicle.name);
                    $('#vehicleIdHidden').val(vehicle.id);
                    $('#vahiclePlate').val(vehicle.plate);
                    $('#vehicleValue').val(vehicle.vehicle_sales_value);
                    $('#vehicleSalesCommission').val(valueCommission);
                    $('#vehicleSalesValue').val(vehicle.sale_request_value);

                } else {
                    Toast.fire({
                        icon: 'warning',
                        title: message,
                    });
                }
            }
        });
    });
});
