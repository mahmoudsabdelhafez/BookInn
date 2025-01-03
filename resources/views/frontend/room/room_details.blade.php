@extends('frontend.main_master')
@section('main')
    <!-- Inner Banner -->
    <div class="inner-banner inner-bg10">
        <div class="container">
            <div class="inner-title">
                <ul>
                    <li>
                        <a href="{{ url('/') }}">Home</a>
                    </li>
                    <li><i class='bx bx-chevron-right'></i></li>
                    <li>Room Details </li>
                </ul>
                <h3>{{ $roomdetails->type->name }}</h3>
            </div>
        </div>
    </div>
    <!-- Inner Banner End -->

    <!-- Room Details Area End -->
    <div class="room-details-area pt-100 pb-70">
        <div class="container">
            <div class="row">
                <div class="col-lg-4">
                    <div class="room-details-side">
                        <div class="side-bar-form">
                            <h3>Booking Sheet </h3>
                            <form method="get" action="{{ route('search_room_details', $roomdetails->id) }}">
                                <div class="row align-items-center">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>Check in</label>
                                            <div class="input-group">
                                <input autocomplete="off" readonly type="text" required name="check_in" id="check_in"  class="form-control dt_picker" >
                                                <span class="input-group-addon"></span>
                                            </div>
                                            <i class='bx bxs-calendar'></i>
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>Check Out</label>
                                            <div class="input-group">
                               <input autocomplete="off" readonly type="text" required name="check_out" id="check_out"  class="form-control dt_picker"  >
                                                <span class="input-group-addon"></span>
                                            </div>
                                            <i class='bx bxs-calendar'></i>
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>Numbers of Persons</label>
                                            <select class="form-control">
                                                <option>01</option>
                                                <option>02</option>
                                                <option>03</option>
                                                <option>04</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>Numbers of Rooms</label>
                                            <select class="form-control">
                                                <option>01</option>
                                                <option>02</option>
                                                <option>03</option>
                                                <option>04</option>
                                                <option>05</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-12 col-md-12">
                                        <button type="submit" class="default-btn btn-bg-three border-radius-5">
                                            Book Now
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>


                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="room-details-article">

                        <div class="room-details-slider owl-carousel owl-theme">
                            @foreach ($multiImage as $image)
                                <div class="room-details-item">
                                    <img src="{{ asset('upload/roomimg/multi_img/' . $image->multi_img) }}" alt="Images">
                                </div>
                            @endforeach

                        </div>





                        <div class="room-details-title">
                            <h2>{{ $roomdetails->type->name }}</h2>
                            <ul>

                                <li>
                                    <b style="color: #B56952"> Price : ${{ $roomdetails->price }} per night </b>
                                </li>

                            </ul>
                        </div>

                        <div class="room-details-content">
                            <p>
                                {!! $roomdetails->description !!}
                            </p>




                            <div class="side-bar-plan">
                                <h3>Basic Plan Facilities</h3>
                                <ul>
                                    @foreach ($facility as $fac)
                                        <li><a>{{ $fac->facility_name }}</a></li>
                                    @endforeach
                                </ul>


                            </div>







                            <div class="row">
                                <div class="col-lg-6">



                                    <div class="services-bar-widget">
                                        <h3 class="title">Room Details </h3>
                                        <div class="side-bar-list">
                                            <ul>
                                                <li>
                                                    <a> <b>Capacity : </b> {{ $roomdetails->room_capacity }}
                                                        Person </a>
                                                </li>
                                                <li>
                                                    <a> <b>Size : </b> {{ $roomdetails->size }} ft2 </a>
                                                </li>


                                            </ul>
                                        </div>
                                    </div>




                                </div>



                                <div class="col-lg-6">
                                    <div class="services-bar-widget">
                                        <h3 class="title">Room Details </h3>
                                        <div class="side-bar-list">
                                            <ul>
                                                <li>
                                                    <a> <b>View : </b> {{ $roomdetails->view }} </a>
                                                </li>
                                                <li>
                                                    <a> <b>Bad Style : </b> {{ $roomdetails->bed_style }} </a>
                                                </li>

                                            </ul>
                                        </div>
                                    </div>

                                </div>
                            </div>



                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Room Details Area End -->

    <!-- Room Details Other -->
    <div class="room-details-other pb-70">
        <div class="container">
            <div class="room-details-text">
                <h2>Other Rooms </h2>
            </div>

            <div class="row ">

                @foreach ($otherRooms as $item)
                    <div class="col-lg-6">
                        <div class="room-card-two">
                            <div class="row align-items-center">
                                <div class="col-lg-5 col-md-4 p-0">
                                    <div class="room-card-img">
                                        <a href="{{ url('room/details/' . $item->id) }}">
                                            <img src="{{ asset('upload/roomimg/' . $item->image) }}" alt="Images">
                                        </a>
                                    </div>
                                </div>

                                <div class="col-lg-7 col-md-8 p-0">
                                    <div class="room-card-content">
                                        <h3>
                                            <a
                                                href="{{ url('room/details/' . $item->id) }}">{{ $item['type']['name'] }}</a>
                                        </h3>
                                        <span>{{ $item->price }} $ / Per Night </span>
                                        
                                        <p>{{ $item->short_desc }}</p>
                                        <ul>
                                            <li><i class='bx bx-user'></i> {{ $item->room_capacity }} Person</li>
                                            <li><i class='bx bx-expand'></i> {{ $item->size }}ft2</li>
                                        </ul>

                                        <ul>
                                            <li><i class='bx bx-show-alt'></i>{{ $item->view }}</li>
                                            <li><i class='bx bxs-hotel'></i> {{ $item->bed_style }}</li>
                                        </ul>

                                        <a href="{{ url("search/room/details/$item->id") }}" class="book-more-btn">
                                            Book Now
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach



            </div>
        </div>
    </div>
    <!-- Room Details Other End -->
@endsection
