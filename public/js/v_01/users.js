jQuery(function () {

    if (userSession.profile.access >= 10) {
        $(".select-branches").hide();
        $("#sellers").hide();
        $(".user_profile").hide();
    }

    $("#id_profile").on("change", function () {
        let value = parseInt($(this).val());

        if ([1, 5].includes(value)) {
            $(".select-branches").hide();
            $("#id_branch").removeAttr("required");
        } else {
            $(".select-branches").show();
            $("#id_branch").attr("required", "required");
        }

        if ([7].includes(value)) {
            if (userSession.profile.access <= 10) {
                $("#sellers").show();
            }
            $("#id_seller").removeAttr('disabled')
            $("#id_branch").removeAttr("multiple").select2('destroy').select2().trigger('change');
        } else {
            $("#sellers").hide();
            $("#id_seller").attr('disabled', true);
            $("#id_branch").attr('multiple', true);
            $("#id_branch").select2('destroy');
            $("#id_branch").select2();
        }
    }).trigger("change");

    /**Validando Email */
    $("#email").on('change', async function () {
        let email = $("#email").val();
        if (email.length > 5) {
            const response = await post('user/validateEmail', { email: email });

            if (response.data.length >= 1) {
                $("#btn3").attr('disabled', true);
                $('#validate-email').addClass('text-bold text-danger').text(`Email em uso!`).parent().removeClass('has-success').addClass('has-error')
            } else {
                $('#validate-email').removeClass('text-danger').text("").parent().removeClass('has-error').addClass('has-success')
                $("#btn3").removeAttr("disabled");
            }
        }
    });

    $("#password_confirm").on('change', function () {
        let password = $("#password").val();
        let confirmPassword = $("#password_confirm").val();

        if (password !== confirmPassword) {
            $("#btn-s").attr('disabled', true);
            $('#validate-password').addClass('text-bold text-danger').text(`Senha Não confere!`).parent().removeClass('has-success').addClass('has-error')
        } else {
            $('#validate-password').removeClass('text-danger').text("").parent().removeClass('has-error').addClass('has-success')
            $("#btn2").removeAttr("disabled", false);
        }
    })

    $("#id_branch").on('change', async function () {
        if ($("#id_profile").val() == 7) {
            let branch = $(this).val();
            $("#id_seller").html('');

            const response = await post('user/getSellerByBranch', { id_branch: branch });
            response.data.map((seller) => {
                $("#id_seller").append(`<option value="${seller.id}">${seller.name}</option>`);
            })
        }
    });
});

//function to save the order of the dashboard in the bank
$(function () {
    $("#sortable").sortable({
        update: function (event, ui) {
            var postDate = $(this).sortable('serialize');
            var userId = $("#id_user_edit").val();

            $.post(url + "ajax/ajax/addOrderDashboard/", {
                list: postDate,
                id_user: userId
            }, function(o) {
                console.log(o);
            }, 'json');
        }
    });
});

$("[type='checkbox']").click(function () {
    var id = $(this).attr('id');
    var id_user = $('#id_user_registered').val();

    if (this.checked) {
        $.ajax ({
            url: url + "ajax/ajax/setActiveBoxDashboard/",
            dataType: 'json',
            method: 'POST',
            data: {"id" : id, "id_user" : id_user},

            success: function (data) {
                var obj = data;
            }
        });
    } else {
        $.ajax({
            url: url + "ajax/ajax/setDeactivateBoxDashboard/",
            dataType: 'json',
            method: 'POST',
            data: {"id" : id, "id_user" : id_user},

            success: function (data) {
                var obj = data;
            }
        });
    }
});