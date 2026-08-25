$('[porcentagem-mask]').inputmask({
    alias: 'percentage',
    integerDigits: 9,
    digits: 2,
    allowMinus: false,
    digitsOptional: false,
    placeholder: "0"
});

$('[data-mask-money]').maskMoney({
    decimal: ',',
    thousands: '.',
    prefix: 'R$ ',
    affixesStay: true
});

$('[data-mask-decimal]').maskMoney({
    decimal: ',',
    thousands: '.',
    affixesStay: true
});

$("[two-verify-number]").inputmask({
    mask: ['(99) 9999-9999', '(99) 9 9999-9999'],
    keepStatic: true
});

$("[cellphone]").inputmask({
    mask: ['(99) 9 9999-9999'],
    keepStatic: true
});

$("[phone]").inputmask({
    mask: ['(99) 9999-9999'],
    keepStatic: true
});

$('.data').datepicker({
    autoclose: true,
    format: 'dd/mm/yyyy',
    language: "pt-BR"
});

$(".dataMask").inputmask("99/99/9999");

$(".mes").datepicker({
    autoclose: true,
    format: "mm/yyyy",
    viewMode: "months",
    minViewMode: "months",
    language: "pt-BR"
});

$("[cpf_mask]").inputmask({
    mask: ['999.999.999-99'],
    keepStatic: true
});

$("[rg_mask]").inputmask({
    mask: ['9.999.999', '9.999.999-9', '99.999.999-9'],
    keepStatic: true
});

$("[cep_mask]").inputmask({
    mask: ['99999-999'],
    keepStatic: true
});

$("[cpfcnpj]").inputmask({
    mask: ['999.999.999-99', '99.999.999/9999-99'],
    keepStatic: true
});

$("[cnpj]").inputmask({
    mask: ['99.999.999/9999-99'],
    keepStatic: true
});

$("[account_number]").inputmask({
    mask: ['9999-9', '99999-9', '999999-9', '9999999-9', '99999999-9', '999999999-9', '9999999999-9'],
    keepStatic: true
});

$("[agency]").inputmask({
    mask: ['####', '9999-9', '9999-99'],
    keepStatic: true
});