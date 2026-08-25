jQuery(function () {
    $("#distribution_type").on("change", function () {
        const distributionType = $(this).val();

        (distributionType == 1) ? $("#serviceHours").show() : $("#serviceHours").hide();
    }).trigger("change");

    $("#distribution_type").on("change", function () {
        $('form#submit-distribution-type').submit();
    });
});