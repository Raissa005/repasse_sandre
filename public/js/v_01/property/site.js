jQuery(function() {
    $("#paste_description").on("click", function() {
        const productId = $("#id_product").val();
        $.ajax({
            url: url + "ajax/ajax/getProductById/",
            dataType: "json",
            method: "POST",
            data: { id_product: productId },
            async: false,
            success: function(response) {
                const { error, product } = response;
                if (!error) {
                    CKEDITOR.instances["description"].setData(product.description);
                }
            },
        });
    });
});
