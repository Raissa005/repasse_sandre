const today = (new Date()).toLocaleDateString('pt-br').split('/').reverse().join('-');

function formatMoney(money) {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL', minimumFractionDigits: 2 }).format(money);
}

function formatDecimal(number, digits) {
    return new Intl.NumberFormat('pt-BR', { style: 'decimal', minimumFractionDigits: digits }).format(number);
}

function unmaskMoney(money) {
    return parseFloat(money.replace(/[^0-9,]*/g, '').replace(',', '.')).toFixed(2)
}

function readURL(input, imgID = "onloadImage") {
    /**onloadImagem */
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('#' + imgID)
                .attr('src', e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function onfilename(input, span) {
    const filename = $(input).val().split("\\").pop();
    $("#" + span).html(filename);
}

function onlyNumbers(input) {
    const value = $(input).val().replace(/[^0-9\.]/g, '');

    $(input).val(value);
}

function onlyIntNumbers(input) {
    const value = $(input).val().replace(/\D/g, '');

    $(input).val(value);
}

function normalDateFormat1(data) {
    let date = new Date(data);
    let formattedDate = ((date.getDate())) + "/" + (date.getMonth() + 1 < 10 ? `0${date.getMonth() + 1}` : date.getMonth() + 1) + "/" + date.getFullYear();
    return formattedDate;
}

function normalDateFormat2(data) {
    let date = new Date(data);
    let formattedDate = ((date.getDate())) + "/" + (date.getMonth() + 1 < 10 ? `0${date.getMonth() + 1}` : date.getMonth() + 1) + "/" + date.getFullYear() + " " + (date.getHours() < 10 ? `0${date.getHours()}` : date.getHours()) + ":" + (date.getMinutes() < 10 ? `0${date.getMinutes()}` : date.getMinutes());
    return formattedDate;
}

function titleize(text) {
    //var words = text.toLowerCase().split(" "); // Deu problema com palavras que foram cadastradas com um espaçamento a mais, por exemplo: ' golf', com um espaçamento na frente.

    var words = text.toLowerCase().trim().split(/\s+/); // Deve retirar os espaçamentos a mais entre as palavras e adicionar um espaçamento entre as plavras corretamente
    for (var a = 0; a < words.length; a++) {
        var w = words[a];
        words[a] = w[0].toUpperCase() + w.slice(1);
    }
    return words.join(" ");
}

$('.pagina').children('a').each(function () {
    const id_pagina = $(this).attr('id');
    const id_menu = $("#id_menu").val();

    if (id_pagina == id_menu) {
        $(this).parent('li').addClass('active');
        $(this).parent('li').parent('ul').parent('.pagina').addClass('active');
        $(this).parent('li').parent('ul').parent('li').addClass('active');
        $(this).parent('li').parent('ul').parent('li').parent('ul').parent('li').addClass('active');
    }
});

$("a.sidebar-toggle").on("click", function () {
    const sidebar = $("body").hasClass("sidebar-collapse") ? 1 : 0;

    $.post({
        url: url + "ajax/ajax/sidebarCollapse/",
        dataType: 'json',
        data: { sidebar: sidebar },
        xhrFields: {
            withCredentials: true
        },
        async: false,

        success: function (response) {
            const { error } = response;
        }
    });
})

$(".change-branch").on("click", function () {
    const branchId = $(this).data("id-branch");

    $.ajax({
        url: `${url}ajax/ajax/changeBranch`,
        dataType: 'json',
        method: 'POST',
        data: {
            branchId
        },
        xhrFields: {
            withCredentials: true
        },
        async: false,
        success: function (response) {
            document.location.reload();
        }
    });
});

$(document).ready(function () {
    $.post({
        url: `${url}ajax/global/toast`,
        dataType: 'json',
        data: {},
        xhrFields: {
            withCredentials: true
        },
        async: false,
        success: function (response) {
            const { error, message, data } = response;

            if (data.toast) {
                Toast.fire({
                    icon: data.toast.icon,
                    title: data.toast.title,
                    // padding: '.8em',
                });
            }
        }
    });
});

$("select").select2();

$("input.custon-checkbox").checkboxradio();

$('[data-toggle="popover"]').popover()

$("#logo").on("change", function () {
    const filename = $(this).val().split("\\").pop();
    $("#filename-logo").html(filename);
});

$('.cp2').colorpicker();

function post(endpoint, body = {}, headers = {}) {
    return new Promise((resolve) => {
        $.post({
            url: `${url}ajax/${endpoint}`,
            dataType: 'json',
            data: body,
            headers: headers,
            xhrFields: { withCredentials: true },
            success: function (response) {
                resolve(response);
            },
            error: function (jqXHR, exception) {
                let error_msg = '';
                if (jqXHR.status === 0) {
                    error_msg = 'Não conectado.\n Verificar rede.';
                } else if (jqXHR.status == 404) {
                    // 404 page error
                    error_msg = 'Página solicitada não encontrada. [404]';
                } else if (jqXHR.status == 500) {
                    // 500 Internal Server error
                    error_msg = 'Erro interno do servidor [500].';
                } else if (exception === 'parsererror') {
                    // Requested JSON parse
                    error_msg = 'Falha na análise de JSON solicitada.';
                } else if (exception === 'timeout') {
                    // Time out error
                    error_msg = 'Erro de tempo limite.';
                } else if (exception === 'abort') {
                    // request aborte
                    error_msg = 'Solicitação Ajax abortada.';
                } else {
                    error_msg = 'Erro não detectado.\n' + jqXHR.responseText;
                }

                Toast.fire({
                    icon: 'error',
                    title: error_msg
                })
            }
        })
    });
}
