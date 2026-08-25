var calendarEl = document.getElementById('calendar');

document.addEventListener('DOMContentLoaded', function() {
    let calendar = new FullCalendar.Calendar(calendarEl, {
        timeZone: 'UTC',
        locale: 'pt',
        handleWindowResize: true,
        expandRows: false,
        dayMaxEvents: true,
        navLinks: false, // can click day/week names to navigate views
        editable: false,
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prevYear,prev,next,nextYear today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        views: {
            dayGridMonth: { buttonText: 'Mês' },
            timeGridWeek: { buttonText: 'Semana' },
            timeGridDay: { buttonText: 'Dia' },
        },
        // dayMaxEventRows: 6,
        events,
        loading: function(bool) {
            document.getElementById('loading').style.display =
                bool ? 'block' : 'none';
        }
    });

    calendar.render();
    calendar.updateSize();
});
