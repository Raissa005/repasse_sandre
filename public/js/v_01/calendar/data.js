let events = [];

let filters = {
    "created_by": (userSession.profile.access > 10 ? parseInt(userSession.user.id) : $('#userId').val())
};

$.post({
    url: url + 'ajax/calendar/getTheAppointmentsForTheCalendar',
    dataType: 'json',
    data: {
        created_by: filters.created_by
    },
    xhrFields: {
        withCredentials: true
    },
    async: false,
    dataType: 'json',

    success: function (response) {
        const { error, message, data } = response;
        if (!error) {
            events = data;
        }
    }
});
