jQuery(function() {
    /**Adicionar */
    $('.btn-add-item').on('click', function() {
        const fatherId = $(this).attr('id');
        let idType = $(this).attr('id-type');

        idType = idType ? idType : 1;

        $.post({
            url: url + "ajax/costCenter/recursiveCostCenterView/",
            dataType: 'json',
            data: { itemSelected: fatherId, idType: idType },
            xhrFields: {
                withCredentials: true
            },
            async: false,

            success: function(response) {
                const { error, options } = response;

                if (!error) {
                    let html = `<option value="0">Raiz</option>` + options;
                    $("div#costcenter-add-modal select#id_father").html(html);
                }
            }
        });

        $('div#costcenter-add-modal').modal('show');

        $('h4.modal-title').html("Adicionar Centro de Custo");
        $('div#costcenter-add-modal form.form-add-item').attr('action', url + "costCenter/handleSubmitAddItem/");
        $('div#costcenter-add-modal #id_type').val(idType);
        $('button#btn-submit-modal').html("Cadastrar");
        $('button#btn-submit-modal').removeClass(['btn-danger', 'btn-success', 'btn-warning', 'btn-info', 'btn-primary']);
        $('button#btn-submit-modal').addClass('btn-primary');
    });

    /**Editar */
    $('.btn-edit-item').on('click', function() {
        const ownId = $(this).attr('id');
        let idType = $(this).attr('id-type');

        idType = idType ? idType : 1;

        $.post({
            url: url + "ajax/global/getGenericoById/",
            dataType: 'json',
            data: { id: ownId, table: "cost_center" },
            xhrFields: {
                withCredentials: true
            },
            async: false,

            success: function(response) {
                const { error, message, data } = response;

                if (!error) {
                    const fatherId = data.id_father;

                    $.post({
                        url: url + "ajax/costCenter/recursiveCostCenterView/",
                        dataType: 'json',
                        data: { itemSelected: fatherId, idType: idType, ownId: ownId, showChildren: 0 },
                        xhrFields: {
                            withCredentials: true
                        },
                        async: false,

                        success: function(response) {
                            const { error, options } = response;

                            if (!error) {
                                const html = `<option value="nowhere">Nenhum lugar</option>
                                              <option value="0">Raiz</option>` + options;

                                $("div#costcenter-edit-modal select#id_father").html(html);
                                $("div#costcenter-edit-modal input#name").val(data.name);
                                $("div#costcenter-edit-modal #status").val(data.status).trigger("change");

                                $("div#costcenter-edit-modal select#move_data").html(html);
                                $("div#costcenter-edit-modal #status").on("change", function() {
                                    if ($(this).val() == 0) {
                                        $('.move-data').fadeIn();
                                        $('#move_data').attr("required", "");
                                    } else {
                                        $('.move-data').fadeOut();
                                        $('#move_data').removeAttr('required');
                                    }
                                })
                            }
                        }
                    });
                }
            }
        });

        $('div#costcenter-edit-modal').modal('show');

        $('h4.modal-title').html("Editar Centro de Custo");
        $('div#costcenter-edit-modal form.form-add-item').attr('action', url + "costCenter/handleSubmitEditItem/" + ownId);
        $('button#btn-submit-modal').html("Salvar");
        $('button#btn-submit-modal').removeClass(['btn-danger', 'btn-success', 'btn-warning', 'btn-info', 'btn-primary']);
        $('button#btn-submit-modal').addClass('btn-primary');
    });

    /**Clonar */
    $('.btn-clone-item').on('click', function() {
        const ownId = $(this).attr('id');

        $.post({
            url: url + "ajax/global/getGenericoById/",
            dataType: 'json',
            data: { id: ownId, table: "cost_center" },
            xhrFields: {
                withCredentials: true
            },
            async: false,

            success: function(response) {
                const { error, message, data } = response;

                if (!error) {
                    $('div#costcenter-clone-modal').modal('show');

                    $('h4.modal-title').html(`Clonar Estrutura "${data.name}"`);
                    $('div#costcenter-clone-modal form.form-clone-item').attr('action', url + "costCenter/handleSubmitCloneItem/" + data.id);
                    $('button#btn-submit-modal').html("Clonar");
                    $('button#btn-submit-modal').removeClass(['btn-danger', 'btn-success', 'btn-warning', 'btn-info', 'btn-primary']);
                    $('button#btn-submit-modal').addClass('btn-primary');

                }
            }
        });


    });
});
