@extends('admin.admin-dashboard')
@section('admin') 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

<div class="page-content">
				<!--breadcrumb-->
				<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
					<div class="breadcrumb-title pe-3">Edit Blog Post</div>
					<div class="ps-3">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb mb-0 p-0">
								<li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
								</li>
								<li class="breadcrumb-item active" aria-current="page">Edit Blog Post</li>
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
                
                <form  class="row g-3" action="{{ route('update.blog.post') }}" method="post" enctype="multipart/form-data">
                    @csrf

                    <input type="hidden" name="id" value="{{ $post->id }}">

                    <div class="form-group col-md-6">
                        <label for="input7" class="form-label">Blog Category</label>
                        <select name="blogcat_id" id="input7" class="form-select">
                            <option selected="">Select Category </option>
                            @foreach ( $blogcat as $cat) 
                            <option value="{{ $cat->id }}" {{ $cat->id == $post->blogcat_id ? 'selected' : '' }} >{{ $cat->category_name }}</option>
                            @endforeach
                           
                        </select>
                        @error('blogcat_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                    </div>
                    
                    <div class="form-group col-md-6">
                        <label for="input1" class="form-label">Post Title</label>
                        <input type="text" name="post_title" class="form-control" id="input1" value="{{ $post->post_title }}" >
                        @error('post_title')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                    </div>


                    <div class="form-group col-md-12">
                        <label for="input11" class="form-label">Short Description</label>
                        <textarea name="short_desc" class="form-control" id="input11"   rows="3">{{ $post->short_desc }}</textarea>
                        @error('short_desc')   
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                    </div>


                    
                    <div class="form-group col-md-12">
                        <label for="input11" class="form-label">Post Description</label>
                        <textarea name="long_desc" class="form-control" id="myeditorinstance" >{!! $post->long_desc !!}</textarea>
                        @error('long_desc')   
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                    </div>

                    <div class="form-group col-md-6">
                        <label for="input1" class="form-label">Post Title</label>
                        <input class="form-control" name="post_image" type="file" id="image">
                        @error('post_image')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                    </div>

                    <div class="form-group col-md-6">
                        <label for="input1" class="form-label"> </label>
                        <img id="showImage" src="{{ asset($post->post_image) }}" alt="Admin" class="rounded-circle p-1 bg-primary" width="80">
                    </div>



                 
                    <div class="col-md-12">
                        <div class="d-md-flex d-grid align-items-center gap-3">
                            <button type="submit" class="btn btn-primary px-4">Submit</button>
                            
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

        $(document).ready(function(){
            $('#image').change(function(e){
                var reader = new FileReader();
                reader.onload = function(e){
                    $('#showImage').attr('src',e.target.result);
                }
                reader.readAsDataURL(e.target.files['0']);
            });
        });

        </script>   


<script type="text/javascript">
    $(document).ready(function (){
        $('#myForm').validate({
            rules: {
                blogcat_id: {
                    required : true,
                }, 
                post_title: {
                    required : true,
                }, 
                short_desc: {
                    required : true,
                }, 
                long_desc: {
                    required : true,
                }, 
                post_image: {
                    required : true,
                }, 
               
                
            },
            messages :{
                blogcat_id: {
                    required : 'Please Select Category Name',
                }, 
                post_title: {
                    required : 'Please Enter Post Title',
                }, 
                short_desc: {
                    required : 'Please Enter Short Description',
                },
                long_desc: {
                    required : 'Please Enter post Description',
                },
                post_image: {
                    required : 'Please Select Post Image',
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