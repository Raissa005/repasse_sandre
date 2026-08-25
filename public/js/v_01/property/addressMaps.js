jQuery(function () {
    $("#address, #neighborhood, #id_city, #uf_state").on("change", function () {
        let uf = $("#uf_state").val();
        let city = "";
        let neighborhood = $("#neighborhood").val().trim().toLowerCase().replace(/\s/g, "+");
        let address = $("#address").val().trim().toLowerCase().replace(/\s/g, "+");

        if ((neighborhood != "") && (address != "")) {

            $.ajax({
                url: url + "ajax/global/getGenericoById/",
                dataType: 'json',
                method: 'POST',
                data: {
                    id: $("#id_city").val(),
                    table: "cities",
                },
                async: false,

                success: function (response) {
                    const { error, message, data } = response;
                    city = data.name;
                }
            });

            city.toLowerCase().replace(/\s/g, "+");
            $.ajax({
                url: `https://maps.googleapis.com/maps/api/geocode/json?address=${address},${neighborhood},${city},${uf}&key=AIzaSyBXb984jOma4yop-zX7bqsy7Hcsgm5CCok`,
                type: 'GET',
                dataType: 'json',
                success: function (response) {
                    $("#lat").val(response.results[0].geometry.location.lat);
                    $("#lng").val(response.results[0].geometry.location.lng);
                },
            });
        }
    })
});