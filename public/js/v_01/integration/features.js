
$('.featuresSelect').on('change', function () {
    let array = []
    $('.featuresTr').each((index,value) => {
        array = [...array,{
            id: $(value).attr('id'),
            name: $(value).children('td').children('.nameFeature'),
            option: $(value).children('td').children('select.featuresSelect').val(),
            tdSys: $(value).children('td.tdSys'),
            optionSys: $(value).children('td').children('select.selectFeatureSys'),
        }]

    })
    array.forEach(element => {
        if(element.option == 0){
            $(element.optionSys).prop('disabled', true);
            $(element.tdSys).prop('hidden', true);
            $(element.name).prop('readonly', false);
        } 
        else{
            $(element.tdSys).prop('hidden', false);
            $(element.optionSys).prop('disabled', false);
            $(element.name).prop('readonly', true);
        }
    });
})
