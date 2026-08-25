jQuery(function () {

    /**Busca dados de cliente já cadastrando para o atendimento */

    $("#id_customer").on("change", function () {
        const idCustomer = $(this).val();

        $.ajax({
            url: url + "ajax/ajax/getCustomerById/",
            dataType: "json",
            method: "POST",
            data: { id_customer: idCustomer },
            async: false,

            success: function (response) {
                const { error, customer } = response;

                if (!error) {
                    if (customer) {
                        $("#name").val(customer.name);
                        $("#phone").val(customer.cellphone);
                        $("#email").val(customer.email);
                        $("#state").val(customer.uf_state).trigger("change");
                        $("#id_city").val(customer.id_city).trigger("change");
                    } else {
                        $("#name").val("");
                        $("#phone").val("");
                        $("#email").val("");
                        $("#state").val("SC").trigger("change");
                    }

                    $("#phone").trigger("blur");
                }
            },
        });

    });

    /**Ao cadastrar um novo atendimento verifica se o já possui atendimento com o telefone adicionado */

    $("#phone").blur(function () {
        const phone = $(this).val().replace(/\D/g, "");

        if (phone !== "") {
            $.ajax({
                url: url + "ajax/ajax/comparePhone",
                dataType: "json",
                method: "POST",
                data: { id_branch: userSession.branch.current.id, phone },
                async: false,

                success: function (response) {
                    const { error, attendance } = response;

                    if (!error) {
                        if (attendance) {
                            /**Existe atendimento */
                            if ((attendance.id_status != 10 && attendance.id_status != 11) && attendance.status == 1) {
                                /**O atendimento não está cancelado ou concluído e está ativo */
                                if (attendance.created_by != userSession.user.id) {
                                    /**O atendimento não pertence a esse usuário */
                                    $('div#attendance-notice-modal').modal('show');
                                    $('h4.modal-title').html("Aviso!");

                                    $.post({
                                        url: url + "ajax/global/getGenericoById/",
                                        dataType: 'json',
                                        data: { table: 'users', id: attendance.created_by },
                                        async: false,

                                        success: function (response) {
                                            const { error, message, data } = response;

                                            if (!error) {
                                                $('.modal-body').html("Já existe um atendimento com esse cliente com o usuário " + data.name + "!");
                                            }
                                        }
                                    });

                                    $('button#btn-confirm').html("Ok");
                                    $('button#btn-confirm').removeClass(['btn-danger', 'btn-success', 'btn-warning', 'btn-info', 'btn-primary']);
                                    $('button#btn-confirm').addClass('btn-primary');

                                } else {
                                    /**O atendimento pertence a esse usuário */
                                    $('div#attendance-notice-modal').modal('show');

                                    $('h4.modal-title').html("Aviso!");
                                    $('.modal-body').html("Você já possui um atendimento com esse cliente!");
                                    $('button#btn-confirm').html("Ver atendimento");
                                    $('button#btn-confirm').removeClass(['btn-danger', 'btn-success', 'btn-warning', 'btn-info', 'btn-primary']);
                                    $('button#btn-confirm').addClass('btn-success');


                                    $('button#btn-confirm').on("click", function () {
                                        location.href = `${url}Attendance/attendance/${attendance.id}`;
                                    })
                                }
                                $("#phone").val("");

                            }
                        }
                    }
                },
            });
        }
    });

});