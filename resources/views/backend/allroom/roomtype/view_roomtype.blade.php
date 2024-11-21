@extends('admin.admin-dashboard')
@section('admin')


<div class="page-content">
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                
            </nav>
        </div>
        <div class="ms-auto">
            <div class="btn-group">
                <a href="{{route('add.room.type')}}" class="btn btn-outline-primary px-5 radius-30">Add Room Type</a>

              
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <h6 class="mb-0 text-uppercase">Room Type List</h6>
    <hr/>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="example" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        {{-- loop over room types and show them --}}
                        {{-- key is like an index (start from 0) and increment by 1 to represent the serial umber --}}
                    @foreach($allData as $key => $item)

        {{-- ========================================================================================================= --}}
                        {{-- I want to get room data from Data model that is related to this RoomType model --}}
                        @php
                        $rooms = App\Models\Room::where('roomtype_id', $item->id)->get();

                        @endphp
        {{-- ========================================================================================================= --}}


                        <tr>
                            <td>{{$key+1}}</td>
                            <td> <img src="{{ (!empty($item->room->image)) ? url('upload/roomimg/'.$item->room->image) : url('upload/no_image.jpg') }}" alt="" style="width: 50px; height:30px;" >   </td>
                            <td>{{$item->name}}</td>
                            
                            {{-- here we pass the id of the room --}}
                            <td>
                            @foreach ($rooms as $roo) 
                            <a href="{{ route('edit.room',$roo->id) }}" class="btn btn-warning px-3 radius-30"> Edit</a>
                            <a href=" " class="btn btn-danger px-3 radius-30" id="delete"> Delete</a>
                            @endforeach 
                            
                    @endforeach
                        </td>
                    </tr>
                        
                </table>
            </div>
        </div>
    </div>
    <hr/>
 
</div>


@endsection