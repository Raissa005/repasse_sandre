$("#printReport").on("click", function (e) {
    Toast.fire({
        icon: 'info',
        title: 'Gerando relatório, por favor aguarde...',
        position: 'top-end',
        timer: 5000,
        didOpen: function () {
            Swal.showLoading();
        }
    })
});
