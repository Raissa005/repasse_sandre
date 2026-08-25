async function checkCode(code) {
    return post('property/checkCode', {
        code
    });
}


jQuery(function () {

    $("#newCod").blur(async function () {
        const inputCod = $(this).val();
        if (inputCod !== "") {
            const properties = await checkCode(inputCod);
            if (properties.data.length > 0) {
                $("#newCod").val("");
                $("#newCod").attr("placeholder", "Código já existente");
                $('#newCod').addClass('text-bold text-danger').parent().removeClass('has-success').addClass('has-error');
            } else {
                $('#newCod').removeClass('text-danger').parent().removeClass('has-error').addClass('has-success');
            }
        }
    });

    $(".btn-clone-property").on("click", function () {
        $("#cloneProperty").modal("show");
    });
});
