@extends('admin.admin-dashboard')
@section('admin') 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<div class="page-content">
				<!--breadcrumb-->
				<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
					<div class="breadcrumb-title pe-3">Booking Report </div>
					<div class="ps-3">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb mb-0 p-0">
								<li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
								</li>
								<li class="breadcrumb-item active" aria-current="page">Booking Report</li>
							</ol>
						</nav>
					</div>
					 
				</div>
				<!--end breadcrumb-->
				<div class="container">
					<div class="main-body">
						<div class="row">
						 
    <div class="col-lg-12">
        
        <div class="card">
            <div class="card-body p-4">
                
                <form  class="row g-3" action="{{ route('search-by-date') }}" method="post" enctype="multipart/form-data" id="myForm">
                    @csrf
                 
    
    <div class="form-group col-md-6">
        <label for="input1" class="form-label">Start Date</label>
        <input type="date" name="start_date" class="form-control" id="input1"  >
        @error('start_date')
        <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
    <div class="form-group col-md-6">
        <label for="input1" class="form-label">End Date</label>
        <input type="date" name="end_date" class="form-control" id="input1"  >
        @error('end_date')
        <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
  
                 
                    <div class="col-md-12">
                        <div class="d-md-flex d-grid align-items-center gap-3">
                            <button type="submit" class="btn btn-primary px-4">Search </button>
                            
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
						</div>
					</div>
				</div>
			</div>
           
 
            <script type="text/javascript">
                $(document).ready(function (){
                    $('#myForm').validate({
                        rules: {
                            start_date: {
                                required : true,
                            }, 
                            end_date: {
                                required : true,
                            }, 
                           
                            
                        },
                        messages :{
                            start_date: {
                                required : 'Please Select Start Date',
                            }, 
                            end_date: {
                                required : 'Please Select End Date',
                            }, 
                            
                             
            
                        },
                        errorElement : 'span', 
                        errorPlacement: function (error,element) {
                            error.addClass('invalid-feedback');
                            element.closest('.form-group').append(error);
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