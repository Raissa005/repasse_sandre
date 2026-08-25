document.addEventListener('DOMContentLoaded', function () {

  var containerEl = document.getElementById('external-events-list');
  var eventEls = Array.prototype.slice.call(
    containerEl.querySelectorAll('.fc-event')
  );
  eventEls.forEach(function (eventEl) {
    let eventDrag = JSON.parse(eventEl.dataset.event);

    $(eventEl).colorpicker({
      color: eventDrag.color
    }).on('hidePicker', function (ev) {
      let color = ev.color.toHex();

      if (eventDrag.color != color) {
        $.post({
          url: url + 'ajax/lead/upDateColorSeller',
          dataType: 'jason',
          data: {
            id_group: eventDrag.id_group,
            color: color,
          }
        });

        window.location.reload(true);
      }
    });

    new FullCalendar.Draggable(eventEl, {
      eventData: {
        title: eventEl.innerText.trim(),
        color: eventDrag.color,
      }
    });
  });

  let events = [];

  $.post({
    url: url + 'ajax/lead/getDutySeller',
    dataType: 'json',
    data: {},
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

  var calendarEl = document.getElementById('calendar');
  var calendar = new FullCalendar.Calendar(calendarEl, {
    headerToolbar: {
      left: 'prevYear,prev,next,nextYear today',
      center: 'title',
      right: 'dayGridMonth'
    },
    locale: 'pt-br',
    editable: true,
    droppable: true,
    dayMaxEvents: true,

    drop: function (info) {
      let eventDrag = JSON.parse(info.draggedEl.dataset.event);
      let start = moment(info.date).format("YYYY-MM-DD HH:mm:ss");

      $.post({
        url: url + 'ajax/lead/insertDutySeller',
        dataType: 'jason',
        data: {
          id_group: eventDrag.id_group,
          title: eventDrag.title,
          color: eventDrag.color,
          start: start,
          allDay: info.allDay,
        },
        xhrFields: {
          withCredentials: true
        },
        async: false,
        dataType: 'json',

        success: function (response) {
          const { error } = response;
          console.log(error);
          if (error) {
            Toast.fire({
              icon: 'warning',
              title: "Vendedor já esta de plantão neste dia " + moment(info.date).format("DD/MM/YYYY")
            });

            setTimeout(function () {
              window.location.reload(1);
            }, 3500);
          } else {
            Toast.fire({
              icon: 'success',
              title: "Vendedor cadastrado no plantão do dia " + moment(info.date).format("DD/MM/YYYY")
            });

            setTimeout(function () {
              window.location.reload(1);
            }, 3500);
          }
        }
      });
    },

    eventDrop: function (info) {
      let start = moment(info.event.start).format("YYYY-MM-DD HH:mm:ss");
      let end = moment(info.event.end).format("YYYY-MM-DD HH:mm:ss");

      $.post({
        url: url + 'ajax/lead/upDateDutySeller',
        dataType: 'jason',
        data: {
          id: info.event.id,
          start: start,
          end: end,
          allDay: info.event.allDay,
        }
      });
    },

    eventResize: function (info) {
      let start = moment(info.event.start).format("YYYY-MM-DD HH:mm:ss");
      let end = moment(info.event.end).format("YYYY-MM-DD HH:mm:ss");

      $.post({
        url: url + 'ajax/lead/upDateDutySeller',
        dataType: 'jason',
        data: {
          id: info.event.id,
          start: start,
          end: end,
          allDay: info.event.allDay,
        }
      });
    },

    eventClick: function (info) {
      $('div#generic-message-modal').modal('show');

      $('h4.modal-title').html("Aviso!");
      $('.modal-body').html("Deseja realmente <strong>EXCLUIR</strong> este evento?");
      $('button#btn-confirm').html("Excluir");
      $('button#btn-confirm').removeClass(['btn-danger', 'btn-success', 'btn-warning', 'btn-info', 'btn-primary']);
      $('button#btn-confirm').addClass('btn-danger');

      $('#generic-message-modal .modal-dialog .modal-footer button#btn-confirm').on("click", function () {
        $.post({
          url: url + 'ajax/lead/deleteDutySeller',
          dataType: 'jason',
          data: {
            id: info.event.id,
          }
        });

        window.location.reload(true);
      })
    },

    events,
  });

  calendar.render();
});