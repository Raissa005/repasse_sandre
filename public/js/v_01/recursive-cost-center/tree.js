jQuery(function () {
    $(".itemWithChildren").on("click", function () {
        const id = $(this).parent('div.btn-group').parent("li").attr('id');
        const ul = $(this).parent('div.btn-group').siblings('ul');
        const i = $(this).children("i.fas");
        const classItem = $(ul).hasClass("open");

        if (classItem) {
            $(ul).removeClass("open");
            $(ul).addClass("close");
            $(ul).hide();
            $(i).removeClass("fa-angle-down");
            $(i).addClass("fa-angle-right");
        } else {
            $(ul).removeClass("close");
            $(ul).addClass("open");
            $(ul).show();
            $(i).addClass("fa-angle-down");
            $(i).removeClass("fa-angle-right");
        }
    });

    if ($('#id_cost_center')) {
        let idCostCenter = $('#id_cost_center');
        let activeId = $(idCostCenter).val();

        $('button.tree').on('click', function () {
            idCostCenter = $('#id_cost_center');
            activeId = $(idCostCenter).val();

            if ($(this).hasClass('btn-primary')) {
                $(this).removeClass('btn-primary');
                $(this).addClass('btn-default');
                $(idCostCenter).val('');
            } else {
                if ($("button[cost-center-id='" + activeId + "']").hasClass('btn-primary')) {
                    $("button[cost-center-id='" + activeId + "']").removeClass('btn-primary')
                }
                $(this).removeClass('btn-default');
                $(this).addClass('btn-primary');
                $(idCostCenter).val($(this).attr('cost-center-id'));
            }

        });

        if ($("button[cost-center-id='" + activeId + "']").hasClass('btn-primary')) {
            const arrayParents = $("button[cost-center-id='" + activeId + "']").parents('ul.list-group');
            arrayParents.each(function () {
                $(this).removeClass('close').addClass('open').show()
                $(this).siblings('div.btn-group').children('a.btn').children('i.fas').removeClass('fa-angle-right').addClass('fa-angle-down');
            });
        }
    }

});
