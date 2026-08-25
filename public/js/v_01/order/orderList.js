jQuery(function () {
    /**Inglês */
    $(".disableOrder").sortable("disable");
    $("#order_list").sortable({
        update: function (event, ui) {
            const postData = $(this).sortable("serialize");
            const table = $(this).data("table");

            $.post(
                url + "ajax/ajax/updateFilesOrder/",
                { list: postData, table },
                function (o) {},
                "json"
            );
        },
    });

    /**Português */
    $("#ordem").sortable({
        update: function (event, ui) {
            const postData = $(this).sortable("serialize");
            const table = $(this).data("table");

            $.post(
                url + "ajax/ajax/updateFilesOrdem/",
                { list: postData, table },
                function (o) {},
                "json"
            );
        },
    });

});
