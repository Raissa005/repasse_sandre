const id_sale = $('#sale').data('id');

const handleAdd = async (name, amount) => {
    const response = await post('sales/handleExpenses', { name, amount, id_sale });

    Toast.fire({
        icon: response.data.error ? 'error' : 'success',
        title: response.data.message,
    })

    if (response.data.error) return;

    $('#expenses-list').append(`
        <tr data-id="${response.data.item.id}">
            <td class="text-center">${name}</td>
            <td class="text-center">${formatMoney(amount)}</td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-delete" data-id="${response.data.item.id}">
                    <i class="fa fa-trash"></i>
                </button>
            </td>
        </tr>
    `);
}

const handleDelete = async (id_expense) => {
    const response = await post('sales/handleDelete', { id_expense });

    Toast.fire({
        icon: response.data.error ? 'error' : 'success',
        title: response.data.message,
    })

    if (response.data.error) return;

    $(`#expenses-list tr[data-id="${id_expense}"]`).remove();
}

$("#sale").on('submit', function (e) {
    e.preventDefault();

    const expense = {
        name: $("#expense").val(),
        amount: $("#amount").val(),
    }

    if (expense.name === '' || expense.amount === '') {
        Toast.fire({
            icon: 'error',
            title: 'Por favor, preencha todos os campos',
        })
        return;
    }

    handleAdd(expense.name, unmaskMoney(expense.amount));

    $("#expense").val("");
    $("#amount").val("");
    $("#expense").focus();
});

$(document).on('click', '.btn-delete', function () {
    const id_expense = $(this).data('id');

    Swal.fire({
        title: 'Você tem certeza?',
        text: "Você não poderá reverter isso!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sim, apague!',
        cancelButtonText: 'Não, cancele!',
    }).then((result) => {
        if (result.isConfirmed) {
            handleDelete(id_expense);
        }
    })
});
