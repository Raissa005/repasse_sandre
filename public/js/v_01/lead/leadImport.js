jQuery(function () {
    $('#modalLeadImport').click(function () {
        $('#import-item-modal').modal('show');

        $('#import').click(function () {
            var csv = $('#filename');
            var csvFile = csv[0].files[0];
            var ext = csv.val().split(".").pop().toLowerCase();

            if ($.inArray(ext, ["csv"]) === -1) {
                alert('Selecione um arquivo csv!');
                return false;
            }

            if (csvFile != undefined) {
                reader = new FileReader();
                reader.onload = function (e) {
                    csvResult = e.target.result.split(/\r|\n|\r\n/).map(function (line) {
                        return line.split('\t');
                    });

                    $.ajax({
                        url: `${url}ajax/lead/saveImportedLeads`,
                        dataType: 'json',
                        method: 'POST',
                        data: {
                            csvResult
                        }
                    });
                }
                reader.readAsText(csvFile);
            }
        });
    });
});