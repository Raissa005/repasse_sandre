$(document).on("click", ".itemWithChildren", function () {
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

$(document).on('change', "select#id_cost_center", async function (e) {
    const response_not = await post("costCenter/recursiveCostCenterList", { id_cost_center: $(`#${e.target.id}`).val(), tree: 'tree-not' });
    const response_ignore_count = await post("costCenter/recursiveCostCenterList", { id_cost_center: $(`#${e.target.id}`).val(), tree: 'tree-no-count' });

    $("#cc-ignored").html(response_not.data);
    $("#cc-ignored-sum").html(response_ignore_count.data);
    val_cc_not = [];
    val_ignored_sum = [];
    $("input#id_not_cost_center").val('');
    $("input#id_ignore_sum_cc").val('');
});

let val_cc_not = [];
val_cc_not = $("input#id_not_cost_center").length > 0 ? $("input#id_not_cost_center").val().split(',') : [];
$(document).on('click', "button.tree-not", function () {
    const cc_id = $(this).attr('cost-center-id');
    getChilds(cc_id);

    if ($(this).hasClass('btn-default')) {
        $(this).removeClass('btn-default').addClass('btn-primary');
        kids.map(function (kid) {
            $(`button[cost-center-id='${kid}'][class='btn btn-xs tree-not btn-default']`).removeClass('btn-default').addClass('btn-primary');
        });
        val_cc_not = [...val_cc_not, cc_id];
        val_cc_not = [...val_cc_not, ...kids];
    } else {
        $(this).removeClass('btn-primary').addClass('btn-default');
        kids.map(function (kid) {
            $(`button[cost-center-id='${kid}'][class='btn btn-xs tree-not btn-primary']`).removeClass('btn-primary').addClass('btn-default');
        });
        val_cc_not = val_cc_not.filter(cc => cc != cc_id && !kids.includes(cc));
    }
    $("input#id_not_cost_center").val([val_cc_not]);
    kids = [];
});

let val_ignored_sum = [];
val_ignored_sum = $("input#id_ignore_sum_cc").length > 0 ? $("input#id_ignore_sum_cc").val().split(',') : [];
$(document).on('click', "button.tree-no-count", function () {
    const cc_id = $(this).attr('cost-center-id');
    getChilds(cc_id);

    if ($(this).hasClass('btn-default')) {
        $(this).removeClass('btn-default').addClass('btn-primary');
        kids.map(function (kid) {
            $(`button[cost-center-id='${kid}'][class='btn btn-xs tree-no-count btn-default']`).removeClass('btn-default').addClass('btn-primary');
        });
        val_ignored_sum = [...val_ignored_sum, cc_id];
        val_ignored_sum = [...val_ignored_sum, ...kids];
    } else {
        $(this).removeClass('btn-primary').addClass('btn-default');
        kids.map(function (kid) {
            $(`button[cost-center-id='${kid}'][class='btn btn-xs tree-no-count btn-primary']`).removeClass('btn-primary').addClass('btn-default');
        });
        val_ignored_sum = val_ignored_sum.filter(cc => cc != cc_id && !kids.includes(cc));
    }
    $("input#id_ignore_sum_cc").val([val_ignored_sum]);
    kids = [];
});

let kids = [];
const getChilds = (id_father) => {
    let father = $(`[father-id='${id_father}']`)[1];
    if (father == undefined) return;

    let arrChildren = Array.from(father.children)
    arrChildren.map(function (child) {
        kids.push(child.id);
        getChilds(child.id);
    });
}
