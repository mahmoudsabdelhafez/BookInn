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
                <a href="{{route('add.team')}}" class="btn btn-outline-primary px-5 radius-30">Add Team</a>

              
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <h6 class="mb-0 text-uppercase">All Team</h6>
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
                            <th>Position</th>
                            <th>Facebook</th>
                            <th>Instagram</th>
                            <th>Twitter</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        {{-- loop over team members and show them --}}
                        {{-- key is like an index (start from 0) and increment by 1 to represent the serial umber --}}
                        @foreach($team as $key => $item)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td><img src="{{asset($item->image)}}" alt="image"
                                style="width: 70px; height: 40px;"></td>
                            <td>{{$item->name}}</td>
                            <td>{{$item->position}}</td>
                            <td>{{$item->facebook}}</td>
                            <td>{{$item->twitter}}</td>
                            <td>{{$item->instagram}}</td>
                            {{-- here we pass the id of the team member --}}
                            <td><a href="{{route('edit.team', $item->id)}}" class="btn btn-warning px-3 radius-30">Edit</a>
                                <a href="{{route('delete.team', $item->id)}}" class="btn btn-danger  px-3 radius-30" id="delete">Delete</a></td> {{-- we have id= "delete" to enable sweetalert on delete --}}

                            
                        </tr>
                        @endforeach
                        
                </table>
            </div>
        </div>
    </div>
    <hr/>
 
</div>


@endsection