$(document).ready(function () {
    const hash = window.location.hash.replace('#', '');
    if (hash.length > 0) $(`[data-id=${hash}]`).click();
})

$(".profile_menu").on("click", async function () {
    const value = $(this).data('id');
    $("#menu_selected").val(value);
    $(".profile_menu").parent().removeClass("active");
    $(this).parent().addClass("active");
    $("#system_menus").html("");

    const response = await post('settings/getMenus');
    const menuAccess = await post('settings/getMenuAccess', { id_profile: value });
    const active = menuId => {
        const item = menuAccess.data.filter(x => x.id_menu == menuId);
        if (item.length > 0) return item[0].status == 1 ? 1 : 2;

        return 3;
    }

    response.data.map((item) => {
        const item_active = active(item.id);
        item.class = item_active == 2 ? 'opcty-50' : '';
        item.btn_class = ({ 1: 'danger', 2: 'success', 3: 'info' }[item_active]);
        item.title = ({ 1: 'Ativo', 2: 'Inativo', 3: 'Padrão do sistema' }[item_active]);
        item.enabled = item_active == 1 ? 'disable' : 'enable';
        item.icon = ({ 1: 'times', 2: 'check', 3: 'question' }[item_active]);

        $("#system_menus").append(`
            <tr>
                <td class="va-middle pl-1rem txt-bold ${item.class}">${item.name}</td>
                <td><button title="${item.title}" id="${item.id}" class="btn btn-sm btn-${item.btn_class} pull-right btn-${item.enabled}-item" sendTo="settings/updateMenu/${value}/"><i class="fas fa-${item.icon}" style="width: 12px;"></i></button></td>
            </tr>
        `);

        item.subMenus.map((submenu) => {
            const submenu_active = active(submenu.id);
            submenu.class = submenu_active == 2 ? 'opcty-50' : '';
            submenu.btn_class = ({ 1: 'danger', 2: 'success', 3: 'info' }[submenu_active]);
            submenu.title = ({ 1: 'Ativo', 2: 'Inativo', 3: 'Padrão do sistema' }[submenu_active]);
            submenu.enabled = submenu_active == 1 ? 'disable' : 'enable';
            submenu.icon = ({ 1: 'times', 2: 'check', 3: 'question' }[submenu_active]);

            $("#system_menus").append(`
                <tr>
                    <td class="va-middle pl-3rem ${item.class}">${submenu.name}</td>
                    <td><button id="${submenu.id}" title="${submenu.title}" title="${item.title}" class="btn btn-sm btn-${submenu.btn_class} pull-right btn-${submenu.enabled}-item" sendTo="settings/updateMenu/${value}/"><i class="fas fa-${submenu.icon}" style="width: 12px;"></i></button></td>
                </tr>
            `);
        });
    });
});

$("#open-modal").on("click", ".btn-enable-item", function () {
    $('#enable-item-modal').modal('show');

    const id = $(this).attr('id');
    const sendTo = $(this).attr('sendTo');
    const html = $(this).attr('bodyHtml');
    const page = $("#page").val();

    $('.form-enable-item').attr('action', url + sendTo + (id != undefined ? id : "") + (page != undefined ? "/" + page : ""));

    if (html) {
        $('.modal-body').html(html);
    }
});

$("#open-modal").on("click", ".btn-disable-item", function () {
    if ($(this).attr('disabled')) {
        return
    }

    $('#disable-item-modal').modal('show');

    const id = $(this).attr('id');
    const sendTo = $(this).attr('sendTo');
    const html = $(this).attr('bodyHtml');
    const page = $("#page").val();

    $('.form-disable-item').attr('action', url + sendTo + (id != undefined ? id : "") + (page != undefined ? "/" + page : ""));

    if (html) {
        $('.modal-body').html(html);
    }
});
