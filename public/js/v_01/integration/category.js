$('#categorySelect').on('change', function () {
    var element = $('#categorySelect').val()
    if(element == 0){
        $('#nameCategory').prop('readonly', false);
    } 
    else{
        $('#nameCategory').prop('readonly', true);
    }
});