@extends('admin.admin-dashboard')
@section('admin') 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<div class="page-content">
				<!--breadcrumb-->
				<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
					<div class="breadcrumb-title pe-3">Add Admin User </div>
					<div class="ps-3">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb mb-0 p-0">
								<li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
								</li>
								<li class="breadcrumb-item active" aria-current="page">Add Admin User</li>
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
                
                <form  class="row g-3" action="{{ route('store.admin') }}" method="post" enctype="multipart/form-data">
                    @csrf
                 
    
    <div class="col-md-6">
        <label for="input1" class="form-label">Admin User Name </label>
        <input required type="text" name="name" class="form-control"   >
        @error('name')
        <small class="text-danger">{{ $message }}</small>
        @enderror
         
    </div>
    <div class="col-md-6">
        <label for="input1" class="form-label">Admin User Email </label>
        <input required type="email" name="email" class="form-control"   >
        @error('email')
        <small class="text-danger">{{ $message }}</small>
        @enderror
         
    </div>
    <div class="col-md-6">
        <label for="input1" class="form-label">Admin User Phone </label>
        <input type="text" name="phone" class="form-control"   >
        @error('phone')
        <small class="text-danger">{{ $message }}</small>
        @enderror
         
    </div>
    <div class="col-md-6">
        <label for="input1" class="form-label">Admin User Address </label>
        <input type="text" name="address" class="form-control"   >
        @error('address')
        <small class="text-danger">{{ $message }}</small>
        @enderror
         
    </div>
    <div class="col-md-6">
        <label for="input1" class="form-label">Admin Password </label>
        <input required type="password" name="password" class="form-control"   >
        @error('password')
        <small class="text-danger">{{ $message }}</small>
        @enderror
         
    </div>
    <div class="col-md-6">
        <label for="input1" class="form-label">Role Name </label>
        <select required name="roles" class="form-select mb-3" aria-label="Default select example">
            <option selected value="" >Select Role </option>
            @foreach ($roles as $role)
            <option value="{{ $role->id }}">{{ $role->name }} </option> 
            @endforeach
            
        </select>
        @error('roles')
        <small class="text-danger">{{ $message }}</small>
        @enderror
         
    </div>
    
 
                 
                    <div class="col-md-12">
                        <div class="d-md-flex d-grid align-items-center gap-3">
                            <button type="submit" class="btn btn-primary px-4">Save Changes </button>
                            
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
            
@endsection