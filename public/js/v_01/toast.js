$(document).ready(function () {
    $.post({
        url: url + "api/ajax/toast",
        dataType: 'json',
        data: {},
        async: false,
        success: function (response) {
            const { error, toast } = response;

            if (toast) {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });

                Toast.fire({
                    icon: toast.icon,
                    title: toast.title,
                    // padding: '.8em',
                });
            }
        }
    });
});