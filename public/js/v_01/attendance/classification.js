jQuery(function () {
    $(".star").on("click", function () {
        const [text, starId] = $(this).attr("id").split("-");
        const status = $(this).hasClass("fas");
        let classificationId = $("#classification").val();

        let aux = starId;

        if (status == true) {
            if (classificationId == starId) {
                $("#classification").val(0);

                while (aux > 0) {
                    let itemIcon = $("#" + text + "-" + aux);
                    $(itemIcon).removeClass("fas");
                    $(itemIcon).addClass("far");
                    aux--;
                }
            } else {
                $("#classification").val(starId);
                while (aux < 5) {
                    aux++;
                    let itemIcon = $("#" + text + "-" + aux);
                    $(itemIcon).removeClass("fas");
                    $(itemIcon).addClass("far");
                }
            }
        } else {
            $("#classification").val(starId);
            while (aux > 0) {
                let itemIcon = $("#" + text + "-" + aux);
                $(itemIcon).removeClass("far");
                $(itemIcon).addClass("fas");
                aux--;
            }
        }
    });
});
