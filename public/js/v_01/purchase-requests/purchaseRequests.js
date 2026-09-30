jQuery(function () {
    const rows = 10;

    $(document).on('click', '.modal-page-link', function () {
        const page = parseInt($(this).attr('page'));
        $('#pageSellerCustomerJax').val(page);
    });

    $(document).on('click', '#modalSellerCustomer, #searchSellerCustomer, .modal-page-link', async function () {
        let searchName = $('#searchNameSellerCustomer').val();
        const page = parseInt($('#pageSellerCustomerJax').val());

        const responseCustomer = await post('PurchaseRequests/getCustomersSellerAndBuyer', {
            page: page,
            limit: rows,
            name: searchName
        });

        $('#sellerCustomerTable').html(
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
                            $('#nameSellerCustomer').val(customer.fancy_name_company);
                            $('#cpfSellerCustomer').val(customer.cnpj);
                        } else {
                            $('#nameSellerCustomer').val(customer.name);
                            $('#cpfSellerCustomer').val(customer.person_registration);
                        }
                        $('#sellerCustomerId').val(customer.id);
                        $('#citySellerCustomer').val(titleize(customer.city_name) + " - " + customer.uf_state);
                        $('#sellerCustomer').modal('hide');
                    }
                },
            });
        });
    });

    $('#formAddItem').on('submit', function (event) {
        event.preventDefault();

        var nameSellerCustomer = $('#nameSellerCustomer').val().trim();

        if (nameSellerCustomer === '') {
            Toast.fire({
                icon: 'warning',
                title: 'Campo Cliente / Vendedor está vazio!'
            });
            return;
        }

        $(this).unbind('submit').submit();
    });

    var vehicles = [];
    var totalPurchaseValue = 0;
    var count = parseInt($('#totalVehicle').text());
    var totalVehicle = count != undefined || count != '' ? count : 0;
    $('#addVehiclePurchaseRequest').on('click', function () {
        $('.disableEditButton').addClass('disabled').off('click');
        $('.disableDeleteButton').removeAttr('href').addClass('disabled');

        var vehicleId = $('#vehicleIdHidden').val();
        var purchaseId = $('#purchaseIdHidden').val();

        var name = $('#name').val();
        var state = $('#state').val();
        var plate = $('#plate').val();
        var typeId = $('#types').val();
        var doorId = $('#doors').val();
        var fuelId = $('#fuels').val();
        var chassi = $('#chassi').val();
        var status = $('#status').val();
        var colorId = $('#colors').val();
        var brandId = $('#brands').val();
        var modelId = $('#models').val();
        var id_city = $('#id_city').val();
        var renavam = $('#renavam').val();
        var mileage = $('#mileage').val();
        var yearModel = $('#yearModel').val();
        var categoryId = $('#categories').val();
        var commission = $('#commission').val();
        var value = $('#vehiclePurcahseValue').val();
        var valueSale = $('#vehicleSalesValue').val();
        var factoryWarranty = $('#factoryWarranty').val();
        var yearManufacture = $('#yearManufacture').val();
        var zeroMileage = $('#zeroMileage').prop("checked");
        var selectedBrandName = $('#brands option:selected').text();
        var selectedModelName = $('#models option:selected').text();
        var selectedColorName = $('#colors option:selected').text();

        if (!name || !brandId || !modelId || !categoryId || !typeId || !doorId || !colorId || !fuelId || !value || !yearModel || !yearManufacture) {
            Toast.fire({
                icon: 'warning',
                title: 'Campos obrigatórios estão vazios!'
            });
            return;
        }

        totalVehicle++;
        totalPurchaseValue += parseFloat(unmaskMoney(value));

        var totalValue = parseFloat(unmaskMoney($('.totalLine').text()));
        if (totalValue > 0) {
            var total = totalValue + parseFloat(unmaskMoney(value));
        } else {
            total = totalPurchaseValue;
        }

        var novoVeiculo = {
            name: name,
            state: state,
            value: value,
            plate: plate,
            typeId: typeId,
            chassi: chassi,
            status: status,
            doorId: doorId,
            fuelId: fuelId,
            colorId: colorId,
            brandId: brandId,
            renavam: renavam,
            mileage: mileage,
            modelId: modelId,
            id_city: id_city,
            yearModel: yearModel,
            valueSale: valueSale,
            categoryId: categoryId,
            commission : commission,
            zeroMileage: zeroMileage,
            factoryWarranty: factoryWarranty,
            yearManufacture: yearManufacture,
        };

        vehicles.push(novoVeiculo);

        if (vehicleId !== '') {
            $.ajax({
                url: url + 'ajax/PurchaseRequests/editVehiclesPurchase',
                dataType: 'json',
                method: 'POST',
                data: {
                    itemId: purchaseId,
                    vehicles: vehicles,
                    vehicleId: vehicleId,
                    totalPurchaseValue: totalPurchaseValue
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
            var lastRowIndex = $('#vehiclePurchaseTableTbody tr:last').data('id') || 0;
            var nextIndex = lastRowIndex + 1;

            var newLine = "<tr data-id='" + nextIndex + "'>" +
                "<td class='align-middle'>" + name + "</td>" +
                "<td class='align-middle'>" + selectedBrandName + "</td>" +
                "<td class='align-middle'>" + selectedModelName + "</td>" +
                "<td class='align-middle'>" + selectedColorName + "</td>" +
                "<td class='text-center align-middle'>" + plate + "</td>" +
                "<td class='text-center align-middle'>" + value + "</td>" +
                "<td class='text-center align-middle'>" + valueSale + "</td>" +
                "<td class='text-center align-middle'>" +
                "<button type='button' class='btn btn-danger btn-sm removerLinha'><i class='fa fa-trash'></i></button>" +
                "</td>" +
                "</tr>";

            $('#vehiclePurchaseTableTbody').append(newLine);

            $('#totalVehicle').text(totalVehicle);

            $('#vehiclePurchaseTableTbody').find(".totalLine").remove();

            var newTotalLine = "<tr class='totalLine'>" +
                "<td class='text-right' colspan='5'><strong>Total: </strong></td>" +
                "<td class='text-center align-middle'><strong>" + formatMoney(total) + "</strong></td>" +
                "</tr>";

            $('#vehiclePurchaseTableTbody').append(newTotalLine);

            $('#name').val('');
            $('#plate').val('');
            $('#value').val('');
            $('#chassi').val('');
            $('#renavam').val('');
            $('#mileage').val('');
            $('#models').empty('');
            $('#yearModel').val('');
            $('#types').val('').change();
            $('#doors').val('').change();
            $('#fuels').val('').change();
            $('#colors').val('').change();
            $('#factoryWarranty').val('');
            $('#yearManufacture').val('');
            $('#brands').val('').change();
            $('#vehicleSalesValue').val('');
            $('#categories').val('').change();
            $('#vehiclePurcahseValue').val('');

            $('#models').html(`<option value=''>Selecione um modelo..</option>`);

            $('#purchaseRequestList').show();
        }
    });

    $('#vehiclePurchaseTable').on('click', '.removerLinha', function () {
        var $linha = $(this).closest('tr');
        var rowIndex = $linha.data('id');

        if (rowIndex >= 0 && rowIndex < vehicles.length) {
            totalLine = parseFloat(unmaskMoney($('.totalLine').text()));
            totalPurchaseValue = (totalLine - parseFloat(unmaskMoney(vehicles[rowIndex].value)));
            vehicles.splice(rowIndex, 1);
            totalVehicle--;

            $('#totalVehicle').text(totalVehicle);
            $('#vehiclePurchaseTableTbody').find(".totalLine").remove();
            $('#vehiclePurchaseTableTbody').append("<tr class='totalLine'><td colspan='4'></td><td><strong>Total: </strong></td><td><strong>" + formatMoney(totalPurchaseValue) + "</strong></td><td></td></tr>");
        }

        $linha.remove();

        var rowCount = $('#vehiclePurchaseTableTbody tr').length;
        if (rowCount === 0) {
            $('#purchaseRequestList').hide();
        }
    });

    $('#btnFinalizar').on('click', function () {
        purchaseId = $('#purchaseIdHidden').val();

        if (vehicles.length > 0) {
            $.ajax({
                url: url + 'ajax/PurchaseRequests/addVehiclesPurchase',
                dataType: 'json',
                method: 'POST',
                data: {
                    itemId: purchaseId,
                    vehicles: vehicles,
                    totalPurchaseValue: totalPurchaseValue
                },
                async: false,

                success: function (response) {
                    const { error, message } = response;

                    if (!error) {
                        $('.disableEditButton').removeClass('disabled');
                        $('.disableDeleteButton').each(function () {
                            $(this).attr('href', url + 'purchase-requests/deleteVehiclesPurchased/' + $(this).attr('id'));
                        }).removeClass('disabled');

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
            $('.disableDeleteButton').each(function () {
                $(this).attr('href', url + 'purchase-requests/deleteVehiclesPurchased/' + $(this).attr('id'));
            }).removeClass('disabled');
        }
    });

    $('.editVehiclesPurchased').on('click', function () {
        vehicleId = $(this).attr('id');

        $.ajax({
            url: url + 'ajax/PurchaseRequests/getVehicleById',
            dataType: 'json',
            method: 'POST',
            data: {
                vehicleId: vehicleId
            },
            async: false,

            success: function (response) {
                const { error, message, vehicle } = response;

                if (!error) {

                    let valueCommission = parseFloat(vehicle.value_commission).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                    $('#name').val(vehicle.name);
                    $('#state').val(vehicle.state);
                    $('#status').val(vehicle.status);
                    $('#id_city').val(vehicle.id_city);
                    $('#plate').val(vehicle.plate ?? '');
                    $('#vehicleIdHidden').val(vehicle.id);
                    $('#chassi').val(vehicle.chassi ?? '');
                    $('#yearModel').val(vehicle.year_model);
                    $('#renavam').val(vehicle.renavam ?? '');
                    $('#mileage').val(vehicle.mileage ?? '');
                    $('#commission').val(valueCommission);
                    $('#types').val(vehicle.id_type).trigger('change');
                    $('#doors').val(vehicle.id_door).trigger('change');
                    $('#fuels').val(vehicle.id_fuel).trigger('change');
                    $('#yearManufacture').val(vehicle.year_manufacture);
                    $('#brands').val(vehicle.id_brand).trigger('change');
                    $('#models').val(vehicle.id_model).trigger('change');
                    $('#colors').val(vehicle.id_color).trigger('change');
                    $('#factoryWarranty').val(vehicle.factory_warranty ?? '');
                    $('#categories').val(vehicle.id_category).trigger('change');
                    $('#vehiclePurcahseValue').val(formatMoney(vehicle.purchase_value));
                    $('#vehicleSalesValue').val(formatMoney(vehicle.vehicle_sales_value));
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
