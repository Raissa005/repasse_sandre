function initMap() {

    const initialLat = $('#lat').val();
    const initialLong = $('#lng').val();

    if (initialLat != '' && initialLong != '') {

        const latlng = new google.maps.LatLng(initialLat, initialLong);

        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 16,
            center: latlng,
        });

        //        marker = new google.maps.Marker({
        //            position: latlng,
        //            map,
        //            icon: 'images/pin.png',
        //        });

        new google.maps.Circle({
            strokeColor: '#FF0000',
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: '#FF0000',
            fillOpacity: 0.35,
            map: map,
            center: latlng,
            radius: 100
        });
    }
}