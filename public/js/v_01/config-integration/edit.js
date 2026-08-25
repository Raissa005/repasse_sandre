$('#status').on('change', function(){
    var element = $('#status').val()
    if(element == 0){
        $('#token').prop('readonly', true);
        $('#token').prop('required', false);
    }
    else
        $('#token').prop('readonly', false);

})