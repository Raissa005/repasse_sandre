$('#typeSelect').on('change', function () {
    var element = $('#typeSelect').val()
    if(element == 0){
        $('#nameType').prop('readonly', false);
    } 
    else{
        $('#nameType').prop('readonly', true);
    }
});
