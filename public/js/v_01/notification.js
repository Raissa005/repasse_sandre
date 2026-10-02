// M3: título e ícone vêm do banco; escapa tudo e devolve só o <strong> que o sistema grava no título
const escapeNotificationText = (text, allowStrong = false) => {
    const escaped = String(text ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    return allowStrong ? escaped.replace(/&lt;(\/?)strong&gt;/g, '<$1strong>') : escaped;
};

jQuery(function () {
    $(".dropdown-notification").on("click", function () {
        $.post({
            url: url + "ajax/ajax/dropdownNotification/",
            dataType: 'json',
            data: {},
            async: false,

            success: function (response) {
                const { error, notifications } = response;
                if (!error) {
                    const html = notifications.map(n => {
                        return `<li id="${n.id_notification_read}" class="btn-message-read">
                                   <a href="${url + "notification/index/" + n.id}">
                                        <span class="text-sm ${n.intended_user == 1 ? "text-yellow" : "text-aqua"}">
                                            Nova Mensagem
                                        </span>
                                        <div>
                                        <i class="text-sm ${escapeNotificationText(n.icon)}"> </i> ${escapeNotificationText(n.title, true)}
                                        </div>
                                   </a>
                                </li>`;
                    });

                    $(".dropdown-notification-menu span.count-notification-header").html(notifications.length);
                    $('.dropdown-notification-menu ul.menu').html(html);
                }
            }
        });
    });

    setInterval(() => {
        $.post({
            url: url + "ajax/ajax/dropdownNotificationCount/",
            dataType: 'json',
            data: {
            },
            async: false,

            success: function (response) {
                const { error, dropdownNotifications } = response;
                if (!error) {
                    const count = dropdownNotifications.length;

                    if (count == 0) {
                        $(".dropdown-notification span.label")
                            .removeClass("label-info")
                            .removeClass("label-warning")
                            .hide();
                    } else {
                        const countMyNotifications = dropdownNotifications.filter(mn => mn.intended_user == 1).length;
                        if (countMyNotifications > 0) {
                            $(".dropdown-notification span.label")
                                .removeClass("label-info")
                                .addClass("label-warning")
                                .show()
                                .html(countMyNotifications);
                        } else {
                            $(".dropdown-notification span.label")
                                .removeClass("label-warning")
                                .addClass("label-info")
                                .show()
                                .html(count);
                        }
                    }
                }

                $.post({
                    url: url + "ajax/ajax/deleteItemAfterSevenDays",
                    dataType: 'json',
                    data: {
                    },
                    async: false,
                });
            }
        });
    }, 15000);

});

