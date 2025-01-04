@extends('admin.admin-dashboard')
@section('admin')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>


    <div class="page-content">
        <!--breadcrumb-->
        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">

            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">All Blog Category</li>
                    </ol>
                </nav>
            </div>
            <div class="ms-auto">
                <div class="btn-group">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">Add
                        Blog Category</button>
                </div>
            </div>
        </div>
        <!--end breadcrumb-->



        <hr />
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    @error('category_name')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
                    <table id="example" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Category Name </th>
                                <th>Category Slug</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($category as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $item->category_name }}</td>
                                    <td>{{ $item->category_slug }}</td>
                                    <td>
                                        <button type="button" class="btn btn-warning px-3 radius-30" data-bs-toggle="modal"
                                            data-bs-target="#category" id="{{ $item->id }}"
                                            onclick="categoryEdit(this.id)">Edit</button>
                                        <a href="{{ route('delete.blog.category', $item->id) }}"
                                            class="btn btn-danger px-3 radius-30" id="delete"> Delete</a>

                                    </td>
                                </tr>
                            @endforeach

                        </tbody>

                    </table>
                </div>
            </div>
        </div>

        <hr />

    </div>

    <!-- Modal -->
    {{-- This model to handle add blog category --}}
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Blog Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <form action="{{ route('store.blog.category') }}" method="post" class="myForm">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="" class="form-label">Blog Category Name</label>
                            <input type="text" name="category_name" class="form-control">
                            
                        </div>

                </div>
                <div class="modal-footer">

                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Edit Modal, To show Edit box-->
    <div class="modal fade" id="category" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Blog Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <form action="{{ route('update.blog.category') }}" method="post" class="myForm2">
                        @csrf
                        {{-- here we pass the id --}}
                        <input type="hidden" name="cat_id" id="cat_id">

                        <div class="form-group mb-3">
                            <label for="" class="form-label">Blog Category Name</label>
                            <input type="text" name="category_name" class="form-control" id="cat">
                        </div>

                </div>
                <div class="modal-footer">

                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        function categoryEdit(id) {
            $.ajax({
                type: 'GET',
                url: '/edit/blog/category/' + id,
                dataType: 'json',
                success: function(data) {
                    // console.log(data)
                    $('#cat').val(data.category_name); // pass the category name to the modal
                    $('#cat_id').val(data.id); // pass the category id to the modal
                }
            })
        }
    </script>


    <script type="text/javascript">
        $(document).ready(function() {
            $('.myForm').validate({
                rules: {
                    category_name: {
                        required: true,
                    }
                },
                messages: {
                    category_name: {
                        required: 'Please Enter Category Name',
                    }
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                },
            });
        });
    </script>


    <script type="text/javascript">
        $(document).ready(function() {
            $('.myForm2').validate({
                rules: {
                    category_name: {
                        required: true,
                    }
                },
                messages: {
                    category_name: {
                        required: 'Please Enter Category Name',
                    }
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                },
            });
        });
    </script>
@endsection
