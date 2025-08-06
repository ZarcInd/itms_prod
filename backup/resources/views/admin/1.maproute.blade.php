@extends('admin/layouts/master')
@section('container')
<main class="main-content">
    <div class="container-fluid px-4">
    <div class="row header align-items-center">
                <div class="col-md-5">
                    <h2 class="m-0">Route replay</h2>
                </div>
                <div class="col-md-7 text-end">
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-2">
                            <select id="group-select" class="form-select">
                                <option value="">Group</option>
                                <option value="buses">Buses</option>
                                <option value="trucks">Trucks</option>
                                <option value="delivery">Delivery</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select id="vehicle-select" class="form-select">
                                <option value="">Vehicle</option>
                                <option value="bus1">Bus 1</option>
                                <option value="bus2">Bus 2</option>
                                <option value="truck1">Truck 1</option>
                                <option value="truck2">Truck 2</option>
                                <option value="delivery1">Delivery 1</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select id="vehicle-select" class="form-select">
                                <option value="">Date</option>
                                <option value="bus1">Bus 1</option>
                                <option value="bus2">Bus 2</option>
                                <option value="truck1">Truck 1</option>
                                <option value="truck2">Truck 2</option>
                                <option value="delivery1">Delivery 1</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select id="vehicle-select" class="form-select">
                                <option value="">Start</option>
                                <option value="bus1">Bus 1</option>
                                <option value="bus2">Bus 2</option>
                                <option value="truck1">Truck 1</option>
                                <option value="truck2">Truck 2</option>
                                <option value="delivery1">Delivery 1</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select id="vehicle-select" class="form-select">
                                <option value="">End</option>
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
       <!-- #region -->

       <div class="row mt-4">
         <div class="col-lg-8 col-md-8 col-12">
         <div id="map"></div>
         </div>

         <div class="col-lg-4 col-md-4 col-12">
         <div class="col-md-12 info-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5>Vehicle activity</h5>
                                    <p class="text-muted mb-0">Lorem ipsum</p>
                                </div>
                                <div class="dots-menu">
                                    <i class="fas fa-ellipsis-v"></i>
                                </div>
                            </div>  
                            <div class="timeline-container mt-4">
                                <div class="timeline-item">
                                <div class="timeline-point"></div>
                                <div class="timeline-line"></div>
                                <div class="timeline-content">
                                    <div class="timeline-time">10:00 am to 11:00 am</div>
                                    <p class="timeline-desc">Delhi to Gurgaon</p>
                                </div>
                                </div>
                                
                                <div class="timeline-item">
                                <div class="timeline-point"></div>
                                <div class="timeline-line"></div>
                                <div class="timeline-content">
                                    <div class="timeline-time">11:00 am to 11:30 am</div>
                                    <p class="timeline-desc">Gurgaon to restaurant</p>
                                </div>
                                </div>
                                
                                <div class="timeline-item">
                                <div class="timeline-point"></div>
                                <div class="timeline-line"></div>
                                <div class="timeline-content">
                                    <div class="timeline-time">11:30 am to 12:30 pm</div>
                                    <p class="timeline-desc">Restaurant breakfast</p>
                                </div>
                                </div>
                                
                                <div class="timeline-item">
                                <div class="timeline-point"></div>
                                <div class="timeline-line"></div>
                                <div class="timeline-content">
                                    <div class="timeline-time">12:30 pm to 1:30 pm</div>
                                    <p class="timeline-desc">Restaurant sightseeing</p>
                                </div>
                                </div>
                                
                                <div class="timeline-item">
                                <div class="timeline-point"></div>
                                <div class="timeline-line"></div>
                                <div class="timeline-content">
                                    <div class="timeline-time">1:30 pm to 2:30 pm</div>
                                    <p class="timeline-desc">Visit local market</p>
                                </div>
                                </div>
                                
                                <div class="timeline-item">
                                <div class="timeline-point"></div>
                                <div class="timeline-line"></div>
                                <div class="timeline-content">
                                    <div class="timeline-time">2:30 pm to 3:30 pm</div>
                                    <p class="timeline-desc">Lunch at local cafe</p>
                                </div>
                                </div>
                                
                                <div class="timeline-item">
                                <div class="timeline-point"></div>
                                <div class="timeline-line"></div>
                                <div class="timeline-content">
                                    <div class="timeline-time">3:30 pm to 4:30 pm</div>
                                    <p class="timeline-desc">Explore historical museum</p>
                                </div>
                                </div>
                                
                                <div class="timeline-item">
                                <div class="timeline-point"></div>
                                <div class="timeline-line"></div>
                                <div class="timeline-content">
                                    <div class="timeline-time">4:30 pm to 5:30 pm</div>
                                    <p class="timeline-desc">Walk in the park</p>
                                </div>
                                </div>
                                
                                <div class="timeline-item">
                                <div class="timeline-point"></div>
                                <div class="timeline-line"></div>
                                <div class="timeline-content">
                                    <div class="timeline-time">5:30 pm to 6:30 pm</div>
                                    <p class="timeline-desc">Coffee break</p>
                                </div>
                                </div>
                                
                                <div class="timeline-item">
                                <div class="timeline-point"></div>
                                <div class="timeline-line"></div>
                                <div class="timeline-content">
                                    <div class="timeline-time">6:30 pm to 7:30 pm</div>
                                    <p class="timeline-desc">Dinner reservation</p>
                                </div>
                                </div>
                                
                                <div class="timeline-item">
                                <div class="timeline-point"></div>
                                <div class="timeline-line"></div>
                                <div class="timeline-content">
                                    <div class="timeline-time">7:30 pm to 8:30 pm</div>
                                    <p class="timeline-desc">Evening entertainment show</p>
                                </div>
                                </div>
                                
                                <div class="timeline-item">
                                <div class="timeline-point"></div>
                                <div class="timeline-line"></div>
                                <div class="timeline-content">
                                    <div class="timeline-time">8:30 pm to 9:30 pm</div>
                                    <p class="timeline-desc">Nighttime stroll</p>
                                </div>
                                </div>
                                
                                <div class="timeline-item">
                                <div class="timeline-point"></div>
                                <div class="timeline-content">
                                    <div class="timeline-time">9:30 pm to 10:30 pm</div>
                                    <p class="timeline-desc">Return to hotel</p>
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