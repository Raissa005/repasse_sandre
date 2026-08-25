jQuery(function () {
    /**Index.php */
    $(".kanban-column").sortable({
        connectWith: ".kanban-column",
        cursor: "move",
        distance: 5,
        delay: 100,
        revert: true,
        scrollSpeed: 70,
        opacity: 0.8,
        tolerance: "intersect",
    });

    $(".disableAttendance").sortable("disable");

    $(".kanban-column").on("sortreceive", function (event, ui) {
        let statusId = $(this).data("status-name");
        let attendanceId = $(ui.item).data("attendance-id");

        $.ajax({
            url: url + "ajax/global/getGenericoById/",
            dataType: "json",
            method: "POST",
            data: { id: statusId, table: "attendance_status" },
            async: false,
            success: function (response) {
                const { error, message, data } = response;
                if (!error) {
                    const newAttendanceStatus = data;

                    $.ajax({
                        url: url + "ajax/global/getGenericoById/",
                        dataType: "json",
                        method: "POST",
                        data: { id: attendanceId, table: "attendance" },
                        async: false,
                        success: function (response) {
                            const { error, message, data } = response;
                            if (!error) {
                                const attendance = data;

                                $.ajax({
                                    url: url + "ajax/global/getGenericoById/",
                                    dataType: "json",
                                    method: "POST",
                                    data: { id: attendance.id_status, table: "attendance_status" },
                                    async: false,
                                    success: function (response) {
                                        const { error, message, data } = response;
                                        if (!error) {
                                            const attendanceStatus = data;

                                            $.post(
                                                url + "ajax/ajax/addCommentToTheTimelineInAttendance/", {
                                                attendanceId: attendance.id,
                                                comment: `Alterou o status de ${attendanceStatus.name} para ${newAttendanceStatus.name}.`,
                                                statusIcon: 2,
                                                statusTimeline: 3,
                                                createdBy: userSession.user.id,
                                            },
                                                function (o) { },
                                                "json"
                                            );

                                            $.post(
                                                url + "ajax/ajax/updateAttendanceStatus/", {
                                                attendanceId,
                                                statusId,
                                            },
                                                function (o) { },
                                                "json"
                                            );
                                        }
                                    },
                                });
                            }
                        },
                    });
                }
            },
        });
    });

});