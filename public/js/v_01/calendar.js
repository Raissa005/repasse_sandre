$(function() {
    function init_events(ele) {
        ele.each(function() {
            var eventObject = {
                title: $.trim($(this).text())
            };
            $(this).data('eventObject', eventObject);
        })
    }

    init_events($('#external-events div.external-event'));
    var currColor = '#6c757d';

    $('#color-chooser > li > a').click(function(e) {
        e.preventDefault();
        currColor = $(this).css('color');
        $('#add-new-event').css({ 'background-color': currColor, 'border-color': currColor })
    });

    $('#add-new-event').click(function(e) {
        e.preventDefault();
        var val = $('#new-event').val();

        if (val.length == 0) {
            return
        }

        var event = $('<div/>');

        event.css({
            'background-color': currColor,
            'border-color': currColor,
            'color': '#fff'
        }).addClass('external-event');

        event.html(val);

        $('#external-events').prepend(event);

        init_events(event);

        $('#new-event').val('');
    })

    $('#calendar').fullCalendar({

        header: {
            left: 'prev, next',
            center: 'title',
            right: 'today, myCustomButton'
        },

        buttonText: {
            today: 'Hoje'
        },

        events: function(start, end, timezone, callback) {
            var month = $("#calendar").fullCalendar('getDate').month() + 1;
            var year = $("#calendar").fullCalendar('getDate').year();

            var filterAttendance = {
                "id_branch": parseInt(userSession.branch.current.id),
                "month": month,
                "year": year,
                "status": true,
            };

            var filterProduct = {
                'id_branch': parseInt(userSession.branch.current.id),
                "month": month,
                "year": year,
                "status": 1,
            };


            if (userSession.profile.access > 10) {
                filterProduct['created_by'] = parseInt(userSession.user.id);
                filterAttendance['created_by'] = parseInt(userSession.user.id);
            } else if (location.search != undefined && location.search != "?id_user=0") {
                let id_user = location.search.split("=").pop();
                filterProduct['created_by'] = id_user;
                filterAttendance['created_by'] = id_user;
            }

            var urlAjax = url + "ajax/ajax/getAndFilterAllAttendanceForCalendar/";

            $.ajax({
                url: urlAjax,
                type: 'POST',
                data: {
                    filterAttendance,
                    filterProduct,
                },
                dataType: 'json',

                success(data) {
                    var events = [];

                    $.each(data, function(category, item) {
                        var id = "";
                        var title = "";
                        var start = "";
                        var background_color = '';
                        var iconAction = "";
                        var icon = "";
                        var urlRequired = "";

                        let today = new Date();
                        let dd = String(today.getDate()).padStart(2, '0');
                        let mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
                        let yyyy = today.getFullYear();
                        today = yyyy + '-' + mm + '-' + dd;

                        switch (category) {
                            case "attendance":
                                item.forEach(element => {
                                    id = element.id;
                                    title = element.name;
                                    start = element.return_date;
                                    iconAction = "fa-clock";
                                    icon = "fas fa-id-badge";
                                    urlRequired = url + 'attendance/attendance/' + element.id;

                                    let dataCalendar = element.return_date.split(" ").shift();

                                    if (today > dataCalendar) {
                                        background_color = '#dc3545';
                                    } else if (today < dataCalendar) {
                                        background_color = '#0a58ca';
                                    } else { //Today
                                        background_color = '#FF851B';
                                    }
                                    events.push({
                                        id: id,
                                        title,
                                        start: start,
                                        color: background_color,
                                        description: element.user_name,
                                        iconAction: ` <i class="fa ${iconAction}"></i> `,
                                        icon: ` <i class="${icon}"></i>  `,
                                        url: urlRequired
                                    })
                                });
                                break;
                            case "products":
                                item.forEach(element => {
                                    id = element.id;
                                    title = element.name;
                                    start = element.re_registered_at;
                                    iconAction = "fas fa-pencil-alt";
                                    icon = "fas fa-building";
                                    urlRequired = url + 'property/editItem/' + element.id;
                                    let dataCalendar = element.re_registered_at.split(" ").shift();

                                    if (today > dataCalendar) {
                                        background_color = '#dc3545';
                                    } else if (today < dataCalendar) {
                                        background_color = '#0a58ca';
                                    } else { //Today
                                        background_color = '#FF851B';
                                    }
                                    events.push({
                                        id,
                                        title,
                                        start,
                                        color: background_color,
                                        description: element.user_name,
                                        iconAction: ` <i class="fa ${iconAction}"></i> `,
                                        icon: ` <i class="${icon}"></i> `,
                                        url: urlRequired
                                    })
                                });
                                break;
                        }
                    });

                    callback(events);
                }
            });
        },

        eventRender: function(event, element, view) {
            element.find('.fc-content').attr("title", event.title);
            element.find('.fc-content').prepend(event.iconAction);
            element.find('.fc-time').append('<br>');
            element.find('.fc-title').prepend(event.icon);
            element.find('.fc-title').prepend(`<i class="fa fa-user"></i>  ` + event.description + `<br>`);
            // element.find('.fc-title').css("white-space", "pre-wrap");
        },

        eventClick: function(calEvent, jsEvent, view) {
            $(this).attr('target', '_blank');
        },
        locale: 'pt-BR'
    });
})