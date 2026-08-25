jQuery(function () {
    $("#image").on("change", function () {
        const filename = $(this).val().split("\\").pop();
        $("#filename_image").html(filename);
    });
});