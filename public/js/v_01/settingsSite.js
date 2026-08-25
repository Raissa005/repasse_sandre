jQuery(function () {
    $("#logo_site").on("change", function () {
        const filename = $(this).val().split("\\").pop();
        $("#filename_logo_site").html(filename);
    });

    $("#logo_admin").on("change", function () {
        const filename = $(this).val().split("\\").pop();
        $("#filename_logo_admin").html(filename);
    });

    $("#logo_admin2").on("change", function () {
        const filename = $(this).val().split("\\").pop();
        $("#filename_logo_admin2").html(filename);
    });

    $("#favicon").on("change", function () {
        const filename = $(this).val().split("\\").pop();
        $("#filename_favicon").html(filename);
    });

    const valor = $("#whatsapp_link").val();
    $('#whatsapp_link').val(valor.slice(2));

});