$(document).on('click', '.attendance_row', function () {
    const id = $(this).data('id')

    window.open(url + `attendance/attendance/${id}`, '_blank')
})
