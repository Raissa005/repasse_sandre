const valueDom = $("#property-value")
const percentageDom = $("#percentage-commission-sale")
const taxDom = {
    percentage: {
        real: $("#taxes-real"),
        virtual: $("#taxes-virtual")
    },
    amount: {
        real: $("#taxes-amount-real"),
        virtual: $("#taxes-amount-virtual")
    }
}
const sellerDom = {
    origin: $("#origin-commission-seller"),
    percentage: $("#seller-percentage"),
    amount: $('#seller-amount')
}

let positionsDom = [];
$(".position").each((index, position) => {
    positionsDom.push({
        id: $(position).attr('id'),
        origin: $(position).children('td').find('#origin-commission'),
        percentage: $(position).children('td').find('#percentage-commission'),
        amount: $(position).children('td').find('.amount'),
    })
});

const branchDom = {
    percentage: {
        real: $('#branch-percentage-real'),
        virtual: $('#branch-percentage-virtual')
    },
    amount: {
        real: $('#branch-amount-real'),
        virtual: $('#branch-amount-virtual')
    }
}

const commissionAmountDom = { gross: $('#commission-gross'), real: $('#commission-real'), virtual: $('#commission-virtual') }
arrangementAnimation('commissionArrangement/commissionCalculation', valueDom, percentageDom, taxDom, sellerDom, positionsDom, commissionAmountDom, branchDom)