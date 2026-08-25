
$(document).on("click", ".click_branch", function () {
	let id_branch = $(this).attr("id_branch");
	if (!$(this).hasClass("opened")) {
		$(this).addClass("opened");
		$(`.id_branch_${id_branch}`).each(function (index, value) {
			$(value).show();
		});
		$(this).find(`#id_branch_${id_branch}`).removeClass("fa-chevron-down").addClass("fa-chevron-up");
	} else {
		$(this).removeClass("opened");
		$(`.id_branch_${id_branch}`).each(function (index, value) {
			$(value).hide();
			fechaFilhosTodos(value);
		});
		$(this).find(`#id_branch_${id_branch}`).removeClass("fa-chevron-up").addClass("fa-chevron-down");
	}
	pintaTrs();
})

$(document).on("click", ".click_center", function () {
	verificarFilhos(this);
	pintaTrs();
})

$(document).on("click", '.click_expenditure', function () {
	let id_branch = $(this).attr("id_branch");
	if (!$(this).hasClass("opened")) {
		$(this).addClass("opened");
		$(`.id_branch_${id_branch}_expenditure`).each(function (index, value) {
			$(value).show();
		});
		$(this).find(`#id_branch_${id_branch}_expenditure`).removeClass("fa-chevron-down").addClass("fa-chevron-up");
	} else {
		$(this).removeClass("opened");
		$(`.id_branch_${id_branch}_expenditure`).each(function (index, value) {
			$(value).hide();
			fechaFilhosTodos(value);
		});
		$(this).find(`#id_branch_${id_branch}_expenditure`).removeClass("fa-chevron-up").addClass("fa-chevron-down");
	}
	pintaTrs();
})

function verificarFilhos(elemento) {
	let center = $(elemento).attr("id_cost_center");
	let branch = $(elemento).attr("id_branch_pai");
	if (!$(elemento).hasClass("opened")) {
		$(elemento).addClass("opened");
		$(`.id_pai_${center}`).each(function (index, value) {
			if ($(value).hasClass(`id_branch_pai_${branch}`)) {
				$(value).show();
			}
		});
		$(elemento).find(`#cost_center_${center}`).removeClass("fa-chevron-down").addClass("fa-chevron-up");
	} else {
		$(elemento).removeClass("opened");
		$(`.id_pai_${center}`).each(function (index, value) {
			if ($(value).hasClass(`id_branch_pai_${branch}`)) {
				$(value).hide();
				fechaFilhosTodos(value);
			}
		});
		$(elemento).find(`#cost_center_${center}`).removeClass("fa-chevron-up").addClass("fa-chevron-down");
	}
}

function fechaFilhosTodos(elemento) {
	let center = $(elemento).attr("id_cost_center");
	let id_branch = $(elemento).attr("id_branch");
	let branch = $(elemento).attr("id_branch_pai");
	if (id_branch != undefined) {
		if ($(elemento).hasClass("opened")) {
			$(elemento).removeClass("opened");
			$(`.id_branch_${id_branch}_expenditure`).each(function (index, value) {
				$(value).hide();
				fechaFilhosTodos(value);
			});
			$(elemento).find(`#id_branch_${id_branch}_expenditure`).removeClass("fa-chevron-up").addClass("fa-chevron-down");
		}
	} else {
		if ($(elemento).hasClass("opened")) {
			$(elemento).removeClass("opened");
			$(`.id_pai_${center}`).each(function (index, value) {
				if ($(value).hasClass(`id_branch_pai_${branch}`)) {
					$(value).hide();
					fechaFilhosTodos(value);
				}
			});
			$(elemento).find(`#cost_center_${center}`).removeClass("fa-chevron-up").addClass("fa-chevron-down");
		}
	}
}

function pintaTrs() {
	let contador = 0;
	$('.tbody-trs > tr').each(function (index, value) {
		$(value).removeClass('cor-nao');
		$(value).removeClass('cor-sim');
		if (!$(value).hasClass("tr_branch") && !$(value).hasClass("tr_resume") && $(value).css('display') != 'none') {
			contador % 2 == 0 ? $(value).addClass('cor-nao') : $(value).addClass('cor-sim');

			contador++;
		}
	})
}

pintaTrs();