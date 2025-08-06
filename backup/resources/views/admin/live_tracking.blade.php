@extends('admin/layouts/master')
@section('container')
<main class="main-content">
    <div class="container-fluid px-4">
            <div class="row header align-items-center">
                <div class="col-md-6">
                    <h2 class="m-0">Live tracking</h2>
                </div>
                <div class="col-md-6 text-end">
                    <div class="row">
                        <div class="col-md-6">
                            <select id="group-select" class="form-select">
                                <option value="">Group</option>
                                <option value="buses">Buses</option>
                                <option value="trucks">Trucks</option>
                                <option value="delivery">Delivery</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <select id="vehicle-select" class="form-select">
                                <option value="">Vehicle</option>
                                <option value="bus1">Bus 1</option>
                                <option value="bus2">Bus 2</option>
                                <option value="truck1">Truck 1</option>
                                <option value="truck2">Truck 2</option>
                                <option value="delivery1">Delivery 1</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            
            
                <div class="row mt-4">
                <div class="col-lg-8 col-md-8 col-12">
                   <div class="row"> 
                    <div class="col-md-6">
                        <div class="info-card">
                            <h5 class="location-name">N/A</h5>
                            <p class="text-muted mb-0 location-address">Address LAT</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-card">
                            <h5 class="speed-value">N/A</h5>
                            <p class="text-muted mb-0">Login Speed</p>
                        </div>
                    </div>
                    <div class="col-md-12">
                    <div id="map"></div>
                    </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-4 col-12">
                    <div class="col-md-12 info-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5>User activity</h5>
                                    <p class="text-muted mb-0">Lorem ipsum</p>
                                </div>
                                <div class="dots-menu">
                                    <i class="fas fa-ellipsis-v"></i>
                                </div>
                            </div>  
                            <div class="activity-list ">
                                    <div class="activity-item">
                                        <p>Lorem ipsum dolor sit amet</p>
                                    </div>
                                    <div class="activity-item">
                                        <p>Lorem ipsum dolor sit amet</p>
                                    </div>
                                    <div class="activity-item">
                                        <p>Lorem ipsum dolor sit amet</p>
                                    </div>
                                    <div class="activity-item">
                                        <p>Lorem ipsum dolor sit amet</p>
                                    </div>
                                    <div class="activity-item">
                                        <p>Lorem ipsum dolor sit amet</p>
                                    </div>
                                    <div class="activity-item">
                                        <p>Lorem ipsum dolor sit amet</p>
                                    </div>
                                    <div class="activity-item">
                                        <p>Lorem ipsum dolor sit amet</p>
                                    </div>
                            </div>
                            
                    </div>
                </div>
                </div>
            </div>
    </div>
</main>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.GOOGLE_MAP_KEY') }}&libraries=places&callback=initMap" async defer></script>

<script type="text/javascript">

        function initMap() {

          const myLatLng = { lat: 22.2734719, lng: 70.7512559 };

          const map = new google.maps.Map(document.getElementById("map"), {

            zoom: 5,

            center: myLatLng,

          });

  

          new google.maps.Marker({

            position: myLatLng,

            map,

            title: "Hello Rajkot!",

          });

        }

  

        window.initMap = initMap;

    </script>


@endsection