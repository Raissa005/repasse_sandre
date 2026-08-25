jQuery(function () {
    $("#popup_tipo").on("change", function () {
        const type = $(this).val();
        switch (type) {
            case "1":
                $("#texto").show();
                $("#imagem").hide();
                $("#video").hide();
                break;
            case "2":
                $("#imagem").show();
                $("#video").hide();
                $("#texto").hide();
                break;
            case "3":
                $("#video").show();
                $("#imagem").hide();
                $("#texto").hide();
                break;
        }
    }).trigger("change");
});