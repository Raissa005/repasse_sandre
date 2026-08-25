jQuery(function () {
    $("#checkAddress").on("change", function () {
        if ($(this).prop('checked')) {
            $("#addressFields").hide();
            $(".input-spouse-address").removeAttr("required");
        } else {
            $("#addressFields").show();
            $.ajax({
                url: url + "api/ajax/getGenericoByID/",
                dataType: 'json',
                method: 'POST',
                data: {
                    'tabela': 'system_config',
                    'id': 1,
                },
                async: false,

                success: function (response) {
                    const { error, obj } = response;

                    if (!error) {
                        obj.required_spouse_cep == 1 ? $("#cep").attr('required', "") : $("#cep").removeAttr("required");
                        obj.required_spouse_address == 1 ? $("#address").attr('required', "") : $("#address").removeAttr("required");
                        obj.required_spouse_neighborhood == 1 ? $("#neighborhood").attr('required', "") : $("#neighborhood").removeAttr("required");
                        obj.required_spouse_number_address == 1 ? $("#number_address").attr('required', "") : $("#number_address").removeAttr("required");
                        obj.required_spouse_complement == 1 ? $("#complement").attr('required', "") : $("#complement").removeAttr("required");
                    }
                }
            });
        }
    }).trigger('change');
});
