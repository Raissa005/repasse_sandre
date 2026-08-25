
function arrangementAnimation(
    routeAjax,
    valueDom,
    percentageDom,
    taxDom,
    sellerDom,
    positionsDom,
    commissionAmountDom,
    branchDom    
) {
    $(valueDom).addClass('arrangement-state')
    $(percentageDom).addClass('arrangement-state')
    $(taxDom.percentage.real).addClass('arrangement-state')
    $(taxDom.percentage.virtual).addClass('arrangement-state')
    $(sellerDom.origin).addClass('arrangement-state')
    $(sellerDom.percentage).addClass('arrangement-state')

    $(positionsDom).each((index, position) => {
        $(position.origin).addClass('arrangement-state')
        $(position.percentage).addClass('arrangement-state')
    });

    $('.arrangement-state').on('change', function () {        
        const element = $(this);
        
        let positions = [];
        $(positionsDom).each((index, position) => {
            positions.push({
                id: position.id,
                origin_commission: $(position.origin).val(),
                percentage_commission: $(position.percentage).val()
            })
        });

        const data = {
            value: unmaskMoney($(valueDom).val()),
            percentage: $(percentageDom).val(),
            taxes: { real: $(taxDom.percentage.real).val(), virtual: $(taxDom.percentage.virtual).val(), },
            seller: { origin: $(sellerDom.origin).val(), percentage: $(sellerDom.percentage).val() },
            positions: positions
        }

        $.post({
            url: url + "ajax/" + routeAjax,
            dataType: 'json',
            data,
            xhrFields: {
                withCredentials: true
            },
            async: false,

            success: function (response) {
                const { error, message, data } = response;
                if (!error) {

                    if (data.branch.percentage.real >= 0) {
                        $(commissionAmountDom.gross).text(data.commission.gross)
                        $(commissionAmountDom.real).text(data.commission.real)
                        $(commissionAmountDom.virtual).text(data.commission.virtual)
    
                        $(taxDom.amount.real).text(data.taxes.amount.real)
                        $(taxDom.amount.virtual).text(data.taxes.amount.virtual)
    
                        $(sellerDom.amount).text(data.seller.amount)
    
                        data.positions.map((position, index) => {
                            $(positionsDom[index].amount).text(position.amount)
                        })
    
                        $(branchDom.amount.real).text(data.branch.amount.real)
                        $(branchDom.amount.virtual).text(data.branch.amount.virtual)
                        $(branchDom.percentage.real).text(data.branch.percentage.real.toFixed(2))
                        $(branchDom.percentage.virtual).text(data.branch.percentage.virtual.toFixed(2))
                    } else {
                        /**Passou de 100% */
                    }
                }
            },
        });
    });
}