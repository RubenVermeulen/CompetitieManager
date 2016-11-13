var geocoder;
var map;

function initialize() {
    geocoder = new google.maps.Geocoder();

    var mapOptions = {
        zoom: 14,
        zoomControl: false
    }

    map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

    codeAddress();
}

function codeAddress() {
    var address = document.getElementById('google-map-address').innerText;

    if (address === 'Thuis') {
        address = 'Prinsengoeddreef 33, 9910 Knesselare';
    }

    geocoder.geocode( { 'address': address}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            map.setCenter(results[0].geometry.location);
            var marker = new google.maps.Marker({
                map: map,
                position: results[0].geometry.location,
                animation: google.maps.Animation.DROP
            });
        } else {
            alert('Geocode was not successful for the following reason: ' + status);
        }
    });
}

google.maps.event.addDomListener(window, 'load', initialize);
