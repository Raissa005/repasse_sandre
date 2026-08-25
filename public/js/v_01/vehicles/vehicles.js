jQuery(function () {
    $('#plate').on('change', function () {
        $("#plate").inputmask({ mask: ["AAA-9999", "AAA9A99"] });
    }).trigger('change');

    $('#addVehicleBrands').on('click', function () {
        var name = $('#nameBrand').val().trim();
        var status = $('#status').val();

        if (name.length <= 0) {
            Toast.fire({
                icon: 'warning',
                title: "Por favor, preencha todos os campos obrigatórios."
            });
        } else {
            $.ajax({
                url: url + 'ajax/VehicleBrands/addItem',
                dataType: 'json',
                method: 'POST',
                data: {
                    name: name,
                    status: status
                },
                async: false,

                success: function (response) {
                    const { error, message, brand } = response;

                    if (!error) {
                        Toast.fire({
                            icon: 'success',
                            title: message
                        });

                        $('#brands').append($('<option>', {
                            value: brand.id,
                            text: brand.name,
                            selected: 'selected'
                        }));

                        $("#models").html('<option value="">Selecione um modelo...</option>');

                        $('#vehicleBrands').append($('<option>', {
                            value: brand.id,
                            text: brand.name,
                            selected: 'selected'
                        }));

                        $('#nameBrand').val('');
                    } else {
                        Toast.fire({
                            icon: 'warning',
                            title: message
                        });
                    }
                }
            });

            $('#modalBrands').modal("hide");
        }
    });

    $('#brands').on('change', function () {
        const brandId = $(this).val();

        if (brandId !== '') {
            $.ajax({
                url: url + 'ajax/VehicleModels/getAllItemsByBrandId/' + brandId,
                dataType: 'json',
                method: 'POST',
                data: {
                    brandId: brandId
                },
                async: false,

                success: function (response) {
                    const { error, models } = response;

                    let html = "";

                    if (models.length != 0) {
                        models.forEach(model => {
                            html += `<option value="${model.id}">${titleize(model.name)}</option>`;
                        });
                    } else {
                        html += `<option value=''>Selecione um modelo..</option>`;
                    }

                    $("#models").html(html);
                }
            });

            $('#vehicleBrands').val(brandId).select2();
        }
    }).trigger('change');

    $('#addVehicleModels').on('click', function () {
        var name = $('#nameModels').val().trim();
        var brandId = $('#vehicleBrands').val();
        var status = $('#status').val();

        if (name.length <= 0 || brandId == null) {
            Toast.fire({
                icon: 'warning',
                title: "Por favor, preencha todos os campos obrigatórios."
            });
        } else {
            $.ajax({
                url: url + 'ajax/VehicleModels/addItem',
                dataType: 'json',
                method: 'POST',
                data: {
                    name: name,
                    brandId: brandId,
                    status: status
                },
                async: false,

                success: function (response) {
                    const { error, message, model } = response;

                    if (!error) {
                        Toast.fire({
                            icon: 'success',
                            title: message
                        });

                        $('#models').append($('<option>', {
                            value: model.id,
                            text: model.name,
                            selected: 'selected'
                        }));

                        $('#nameModels').val('');
                        $('#brands').val(brandId).select2();
                    } else {
                        Toast.fire({
                            icon: 'warning',
                            title: message
                        });
                    }
                }
            });

            $('#modalModels').modal("hide");
        }
    });

    $('#addVehicleColors').on('click', function () {
        var name = $('#nameColor').val().trim();
        var status = $('#status').val();

        if (name.length <= 0) {
            Toast.fire({
                icon: 'warning',
                title: "Por favor, preencha todos os campos obrigatórios."
            });
        } else {
            $.ajax({
                url: url + 'ajax/VehicleColors/addItem',
                dataType: 'json',
                method: 'POST',
                data: {
                    name: name,
                    status: status
                },
                async: false,

                success: function (response) {
                    const { error, message, color } = response;

                    if (!error) {
                        $('#colors').append($('<option>', {
                            value: color.id,
                            text: color.name,
                            selected: 'selected'
                        }));

                        Toast.fire({
                            icon: 'success',
                            title: message
                        });
                    } else {
                        Toast.fire({
                            icon: 'warning',
                            title: message
                        });
                    }
                }
            });

            $('#modalColors').modal("hide");
        }
    });

    $('#addVehicleDoors').on('click', function () {
        var door = $('#inputModalDoors').val();
        var status = $('#status').val();

        if (door.length <= 0) {
            Toast.fire({
                icon: 'warning',
                title: "Por favor, preencha todos os campos obrigatórios."
            });
        } else {
            $.ajax({
                url: url + 'ajax/VehicleDoors/addItem',
                dataType: 'json',
                method: 'POST',
                data: {
                    door: door,
                    status: status
                },
                async: false,

                success: function (response) {
                    const { error, message, door } = response;

                    if (!error) {
                        $('#doors').append($('<option>', {
                            value: door.id,
                            text: door.doors > 1 ? door.doors + ' Portas' : door.doors + ' Porta',
                            selected: 'selected'
                        }));

                        Toast.fire({
                            icon: 'success',
                            title: message
                        });
                    } else {
                        Toast.fire({
                            icon: 'warning',
                            title: message
                        });
                    }
                }
            });

            $('#modalDoors').modal("hide");
        }
    });

    $('#addVehicleCategories').on('click', function () {
        var name = $('#nameCategories').val().trim();
        var status = $('#status').val();

        if (name.length <= 0) {
            Toast.fire({
                icon: 'warning',
                title: "Por favor, preencha todos os campos obrigatórios."
            });
        } else {
            $.ajax({
                url: url + 'ajax/VehicleCategories/addItem',
                dataType: 'json',
                method: 'POST',
                data: {
                    name: name,
                    status: status
                },
                async: false,

                success: function (response) {
                    const { error, message, category } = response;

                    if (!error) {
                        $('#categories').append($('<option>', {
                            value: category.id,
                            text: category.name,
                            selected: 'selected'
                        }));

                        Toast.fire({
                            icon: 'success',
                            title: message
                        });
                    } else {
                        Toast.fire({
                            icon: 'warning',
                            title: message
                        });
                    }
                }
            });

            $('#modalCategories').modal("hide");
        }
    });

    $('#addVehicleTypes').on('click', function () {
        var name = $('#nameTypes').val().trim();
        var status = $('#status').val();

        if (name.length <= 0) {
            Toast.fire({
                icon: 'warning',
                title: "Por favor, preencha todos os campos obrigatórios."
            });
        } else {
            $.ajax({
                url: url + 'ajax/VehicleTypes/addItem',
                dataType: 'json',
                method: 'POST',
                data: {
                    name: name,
                    status: status
                },
                async: false,

                success: function (response) {
                    const { error, message, type } = response;

                    if (!error) {
                        $('#types').append($('<option>', {
                            value: type.id,
                            text: type.name,
                            selected: 'selected'
                        }));

                        Toast.fire({
                            icon: 'success',
                            title: message
                        });
                    } else {
                        Toast.fire({
                            icon: 'warning',
                            title: message
                        });
                    }
                }
            });

            $('#modalTypes').modal("hide");
        }
    });

    $('#addVehicleFuels').on('click', function () {
        var name = $('#nameFuel').val().trim();
        var status = $('#status').val();

        if (name.length <= 0) {
            Toast.fire({
                icon: 'warning',
                title: "Por favor, preencha todos os campos obrigatórios."
            });
        } else {
            $.ajax({
                url: url + 'ajax/VehicleFuels/addItem',
                dataType: 'json',
                method: 'POST',
                data: {
                    name: name,
                    status: status
                },
                async: false,

                success: function (response) {
                    const { error, message, fuel } = response;

                    if (!error) {
                        $('#fuels').append($('<option>', {
                            value: fuel.id,
                            text: fuel.name,
                            selected: 'selected'
                        }));

                        Toast.fire({
                            icon: 'success',
                            title: message
                        });
                    } else {
                        Toast.fire({
                            icon: 'warning',
                            title: message
                        });
                    }
                }
            });

            $('#modalFuels').modal("hide");
        }
    });

    $('#yearManufacture').on('blur', function () {
        var yearManufacture = $(this).val();
        var yearModel = $('#yearModel').val();

        if (yearManufacture.length != 4) {
            Toast.fire({
                icon: 'warning',
                title: 'Ano de fabricação deve conter apenas 4 dígitos!'
            });

            $(this).focus();
            $(this).val(yearManufacture.slice(0, 4));
        } else {
            if (yearModel.length != 0 && yearModel < yearManufacture) {
                Toast.fire({
                    icon: 'warning',
                    title: 'Ano de fabricação não pode ser maior que o ano do modelo!'
                });

                $(this).focus();
            }
        }
    });

    $('#yearModel').on('blur', function () {
        var yearModel = $(this).val();
        var yearManufacture = $('#yearManufacture').val();

        if (yearModel.length != 4) {
            Toast.fire({
                icon: 'warning',
                title: 'Ano do modelo deve conter apenas 4 dígitos!'
            });

            $(this).focus();
            $(this).val(yearModel.slice(0, 4));
        } else {
            if (yearManufacture.length != 0 && yearModel < yearManufacture) {
                Toast.fire({
                    icon: 'warning',
                    title: 'Ano do modelo não pode ser menor que o ano de fabricação!'
                });

                $(this).focus();
            }
        }
    });

    $('#toggleCosts').on('click', function () {
        if ($(".costsDropdown").is(":hidden")) {

            $(".costsDropdown").show();
        } else {

            $(".costsDropdown").hide();
        }
    });

    $('#addCost').on('click', function () {
        let itemId = 1;

        let createdAt = $('#createdAt').val();
        let customerId = $('#customer').val();
        let costValue = $('#costValue').val();
        let description = $('#description').val();

        if (createdAt === "" || customerId === "" || costValue === "" || description === "") {
            Toast.fire({
                icon: 'warning',
                title: "Por favor, preencha todos os campos.",
            });
        } else {
            $.ajax({
                url: url + 'ajax/Vehicles/getCustomerById',
                dataType: 'json',
                method: 'POST',
                data: {
                    id: customerId
                },
                async: false,

                success: function (response) {
                    const { error, message, customer } = response;

                    if (!error) {
                        $('#costsTable').append(
                            `<tr id="item-${itemId}">
                                <input type="hidden" name="customerId" value="${customer.id}"/>
                                <td class="text-center">${moment(createdAt).format("DD/MM/YYYY")}</td>
                                <td>${customer.name}</td>
                                <td>${description}</td>
                                <td>${costValue}</td>
                                <td class="text-center"><button class="btn btn-danger btn-sm remove-item"><i class="fa fa-times"></i></button></td>
                            </tr>`
                        );

                        $('#costs').find('input:text').val('');

                        itemId++;
                    } else {
                        Toast.fire({
                            icon: 'warning',
                            title: message
                        });
                    }
                }
            });
        }
    });

    $('#costsTable').on('click', '.remove-item', function () {
        $(this).closest('tr').remove();
    });

    $('#cancel').on('click', function () {
        $('#costsTable').empty();

        $('.modal').on('hidden.bs.modal', function () {
            $(this).find('input:text').val('');
            $(this).find('select').select2().val(null).trigger('change');
        });
    });

    $('#addVehicleCost').on('click', function () {
        let costs = [];
        let vehicleId = $('#vehicleId').val();

        $('#costsTable tr').each(function () {
            let customer = $(this).find('td:eq(1)').text();
            let createdAt = $(this).find('td:eq(0)').text();
            let costValue = $(this).find('td:eq(3)').text();
            let description = $(this).find('td:eq(2)').text();
            let customerId = $(this).find('input[name="customerId"]').val();

            let costObj = {
                customer: customer,
                costValue: costValue,
                createdAt: createdAt,
                customerId: customerId,
                description: description
            };

            costs.push(costObj);
        });

        $.ajax({
            url: url + 'ajax/Vehicles/addCosts',
            dataType: 'json',
            method: 'POST',
            data: {
                data: costs,
                vehicleId: vehicleId
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
                        icon: 'error',
                        title: message
                    });
                }
            }
        });
    });

    // Função para atualizar a tabela de custos
    function updateCostsTable(costsList, total) {
        const tableRows = costsList.map((cost, index) => {
            return `<tr>
                    <td class="text-center">${moment(cost.updated_at ?? cost.created_at).format("DD/MM/YYYY")}</td>
                    <td>${cost.description}</td>
                    <td>${formatMoney(cost.value)}</td>
                    <td class="text-center">
                        <a class="btn btn-sm btn-primary btnEditCost" data-index="${index}" data-id="${cost.id}">
                            <i class="fa fa-pencil-alt"></i>
                        </a>
                        <a class="btn btn-sm btn-danger btnDeleteCost" data-index="${index}" data-id="${cost.id}">
                            <i class="fa fa-trash-alt"></i>
                        </a>
                    </td>
                </tr>`;
        }).join('');

        const totalRow = `<tr>
                        <td colspan="2" class="text-right"><strong>Total:</strong></td>
                        <td><strong>${formatMoney(total)}</strong></td>
                        <td></td>
                    </tr>`;

        $("#vehicleCostsTable").html(tableRows + totalRow);

        // Atribuir evento de clique para o botão de edição
        $('.btnEditCost').off('click').on('click', function () {
            var clickedIndex = $(this).data('index');
            var costEdit = costsList[clickedIndex];

            // Preencher formulário com os dados para edição
            var id = costEdit.id;
            var idVehicle = costEdit.id_vehicle;
            var description = costEdit.description;
            var value = formatMoney(costEdit.value);
            var data = moment(costEdit.created_at).format("YYYY-MM-DD");

            var editForm = `
            <div class="box-fieldset clearfix">
                <div class="title-fieldset">Editar Item</div>
                <div class="col-md-12">
                    <input type="hidden" id="idHidden" value="${id}">
                    <input type="hidden" id="idVehicleHidden" value="${idVehicle}">
                    <div class="col-md-3">
                        <label>Data </label>
                        <div class="form-group">
                            <input type="date" class="form-control" id="editUpdatedAt" name="updatedAt" value="${data}" required>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <label>Descrição</label>
                        <div class="form-group">
                            <input type="text" class="form-control" id="editDescription" name="editDescription" value="${description}" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label>Valor</label>
                        <div class="form-group">
                            <input type="text" class="form-control" id="editCostValue" name="editCostValue" value="${value}" data-mask-money required>
                        </div>
                    </div>
                    <div class="col-md-1 pull-right">
                        <button class="btn btn-primary btnUpdateCost" style="margin-top: 25px;">Salvar</button>
                    </div>
                </div>
            </div>`;

            // Substituir o conteúdo da div costEdit pelo formulário de edição
            $('.costEdit').html(editForm);

            // Atribuir evento de clique para o botão de atualizar
            $('.btnUpdateCost').off('click').on('click', function () {
                var id = $('#idHidden').val();
                var idVehicle = $('#idVehicleHidden').val();
                var value = $('#editCostValue').val();
                var updated_at = $('#editUpdatedAt').val();
                var description = $('#editDescription').val();

                $.ajax({
                    url: url + 'ajax/Vehicles/editCostList',
                    dataType: 'json',
                    method: 'POST',
                    data: {
                        id: id,
                        value: value,
                        vehicleId: idVehicle,
                        updated_at: updated_at,
                        description: description
                    },

                    success: function (response) {
                        const { error, message, newCostList, total } = response;

                        if (!error) {
                            updateCostsTable(newCostList, total);

                            Toast.fire({
                                icon: 'success',
                                title: message
                            });

                            // Limpar o conteúdo da div costEdit
                            $('.costEdit').empty();
                        } else {
                            Toast.fire({
                                icon: 'error',
                                title: message
                            });
                        }
                    }
                });
            });

            $('[data-mask-money]').maskMoney({
                decimal: ',',
                thousands: '.',
                prefix: 'R$ ',
                affixesStay: true
            });
        });

        // Atribuir evento de clique para o botão de exclusão
        $('.btnDeleteCost').off('click').on('click', function () {
            var clickedIndex = $(this).data('index');

            $.ajax({
                url: url + 'ajax/Vehicles/deleteCost',
                dataType: 'json',
                method: 'POST',
                data: {
                    costEdit: costsList[clickedIndex]
                },
                async: false,

                success: function (response) {
                    const { error, message, newCostList, total } = response;

                    if (!error) {
                        updateCostsTable(newCostList, total);

                        Toast.fire({
                            icon: 'success',
                            title: message
                        });

                        // Limpar o conteúdo da div costEdit
                        $('.costEdit').empty();
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: message
                        });
                    }
                }
            });
        });
    }

    // Evento de clique para o botão de listar custos
    $('.btn-costs-list').on('click', function () {
        var customerId = $(this).data('id');
        var vehicleId = $(this).closest('tr').find('#vehicleId').val();
        var customerName = $(this).closest('tr').find('td:eq(2)').text();

        $('#costsList #customerId').val(customerId);
        $('#costsList .modal-title').text('Lista de custos do fornecedor ' + customerName);
        $('#costsList').modal('show');

        $.ajax({
            url: url + 'ajax/Vehicles/getCosts',
            dataType: 'json',
            method: 'POST',
            data: {
                vehicleId: vehicleId,
                customerId: customerId
            },
            async: false,

            success: function (response) {
                const { error, message, costsList, total } = response;

                if (!error) {
                    updateCostsTable(costsList, total);
                } else {
                    Toast.fire({
                        icon: 'error',
                        title: message
                    });
                }
            }
        });
    });

    // Evento de fechar o modal
    $('.costsList').on('hidden.bs.modal', function () {
        $('.costEdit').empty();

        location.reload();
    });
});