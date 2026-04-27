@extends('user.layouts.app')

@section('title', 'Trip Location') <!-- Set your custom title here -->

@section('content')

    <style>
        #map {
            height: 600px;
            width: 100%;
        }
    </style>
    <div class="card mt-4 p-3 ">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-xl-6 mb-9">
                    <h2 class="fw-bold mb-3">Trip Location</h2>
                </div>
                <div class="col-md-4 col-xl-4 mb-3 text-end">
                    <input type="date" name="date" id="date" value="{{ $date }}" class="form-control">
                </div>
                <div class="col-md-2 col-xl-2 mb-3 text-end">
                    <button class="btn btn-primary mt-2" id="filterBtn">Search</button>
                </div>

            </div>
           
            @if(empty(json_decode($location_info)) == false)
                <div id="map"></div>
            @else
                <div class="alert alert-warning mt-4">
                    <p class="text-center">No location data available on the selected date.</p>   
                </div>
            @endif
           
        </div>
    </div>
@endsection
@section('js')
@if(empty(json_decode($location_info)) == false)
@if($map_view == "MapMyIndia")
    <script src="https://apis.mappls.com/advancedmaps/api/916b8e48bbec2f473e9f1b525afcf2b2/map_sdk.js?v=3.0&layer=vector"></script>
<script src="https://apis.mappls.com/advancedmaps/api/916b8e48bbec2f473e9f1b525afcf2b2/map_sdk?v=3.0&layer=vector"></script>
    <script type="text/javascript">
        var map = new mappls.Map('map', {
            //center: [28.7041, 77.1025], // Set initial position (Delhi coordinates)
            zoom: 5
        });

        mappls_polygon = new mappls.Polyline({
            map: map,
            paths: {!! $location_info !!},
            fillColor: "red",
            fillOpacity: 0.8,
            strokeColor: "red",
            strokeOpacity: 0.8,
            fitbounds: true,
            fitboundOptions: {padding: 120,duration:1000},
            popupHtml: 'Route 1',
            popupOptions: {offset: {'bottom': [0, -20]}}
        });

        // Extract start and end points
       var pathPoints = {!! $location_info !!}; // Should be array of {lat: ..., lng: ...}

        if (pathPoints.length > 1) {
            // Start Marker
            new mappls.Marker({
                map: map,
                position: {
                    lat: pathPoints[0].lat,
                    lng: pathPoints[0].lng
                },
                icon_url: '{{asset("img/green_marker.png")}}',
                popupHtml: 'Start Point'
            });

            // End Marker
            new mappls.Marker({
                map: map,
                position: {
                    lat: pathPoints[pathPoints.length - 1].lat,
                    lng: pathPoints[pathPoints.length - 1].lng
                },
                icon_url: '{{asset("img/red_marker.png")}}',
                popupHtml: 'End Point'
            });
        }      

       
    </script>
@else
<script
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB_2La4puFtmD9xYeU2zvL3DEl5aGnoX00&callback=initMap&v=weekly"
      defer
    ></script>
<script>
function initMap() {
    const flightPlanCoordinates = {!! $location_info !!};

    // Default map options
    const mapOptions = {
        zoom: 3,
        center: { lat: 22.5634657, lng: 88.2926876 }, // Default fallback
        mapTypeId: "terrain",
    };

    // Create map instance
    const map = new google.maps.Map(document.getElementById("map"), mapOptions);

    if (flightPlanCoordinates.length > 0) {
        // Create polyline
        const flightPath = new google.maps.Polyline({
            path: flightPlanCoordinates,
            geodesic: true,
            strokeColor: "#FF0000",
            strokeOpacity: 1.0,
            strokeWeight: 2,
        });

        flightPath.setMap(map);

        // Adjust map to fit the polyline bounds
        const bounds = new google.maps.LatLngBounds();
        flightPlanCoordinates.forEach(coord => bounds.extend(coord));
        map.fitBounds(bounds);
    } else {
        // Handle empty data gracefully
        alert("No location data available for this date.");
    }
}

window.initMap = initMap;
</script>

@endif
@endif
<script>

     $('#filterBtn').on('click', function() {
            var date = $('#date').val();
            if (date) {
                window.location.href = "{{ route('trips.location', ['id' => $trip->id]) }}?date=" + date;
            } else {
                alert('Please select a date.');
            }
        });
    </script>
@endsection