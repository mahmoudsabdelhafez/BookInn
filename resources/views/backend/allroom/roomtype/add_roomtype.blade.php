@extends('admin.admin-dashboard')
@section('admin')
{{-- this query is related to the script at the buttom --}}
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <div class="page-content">
        <!--breadcrumb-->
        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="breadcrumb-title pe-3">Add Room Type</div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Add Room Type</li>
                    </ol>
                </nav>
            </div>
           
        </div>
        <!--end breadcrumb-->
        <div class="container">
            <div class="main-body">
                <div class="row">
                   
                    <div class="col-lg-8">
                        <div class="card">
                            {{-- We create this form to update team data in db. --}}
                            <form id="myForm" action="{{route('room.type.store')}}" method="POST" >
                                @csrf
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Room Type Name</h6>
                                    </div>
                                    <div class=" form-group col-sm-9 text-secondary"> {{-- here we add form-group to class to enable valisation script --}}

                                        <input  type="text" name="name" class="form-control"
                                         />
                                    </div>
                                </div>
                                


                                <div class="row">
                                    <div class="col-sm-3"></div>
                                    <div class="col-sm-9 text-secondary">
                                        {{-- button type must be submit --}}
                                        <input type="submit" class="btn btn-primary px-4" value="Save Changes" />
                                    </div>
                                </div>
                            </div>
                        </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- this script it to render the image once it's selected (in update form) --}}
    {{-- note that #image and #showIamge are id's in the code --}}
    <script type="text/javascript">
    $(document).ready(function() {
        $('#image').change(function(e) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#showImage').attr('src', e.target.result);
            }
            reader.readAsDataURL(e.target.files['0']);
        })
    });

    </script>

    {{-- validation script to validate add team form --}}
    <script type="text/javascript">
        $(document).ready(function (){
            $('#myForm').validate({
                rules: {
                    name: { // id="name"
                        required : true,
                    }, 
                  
                    
                },
                messages :{
                    name: {
                        required : 'Please Enter Team Name',
                    }, 
                   
                     
    
                },
                errorElement : 'span', 
                errorPlacement: function (error,element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error); // we will pass id ="form-group" to input field in the form
                },
                highlight : function(element, errorClass, validClass){
                    $(element).addClass('is-invalid');
                },
                unhighlight : function(element, errorClass, validClass){
                    $(element).removeClass('is-invalid');
                },
            });
        });
        
    </script>
@endsection
