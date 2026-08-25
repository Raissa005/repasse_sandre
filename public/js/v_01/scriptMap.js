
function initMap() {
    const initialLat = $('#lat').val();
    const initialLong = $('#lng').val();

    if (initialLat != '' && initialLong != '') {
        const latlng = new google.maps.LatLng(initialLat, initialLong);

        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 15,
            center: latlng
        });

        marker = new google.maps.Marker({
            position: latlng,
            map,
            icon: url + 'img/pin.png'
        });
    }

    google.maps.event.addListener(map, 'click', function (event) {
        const lat = event.latLng;
        
        marker.setPosition(lat);

        $('#latlng').val(lat);
    });
}