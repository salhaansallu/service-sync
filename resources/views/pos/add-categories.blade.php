@extends('pos.app')

@section('dashboard')
    <div class="content-page">
        <div class="container-fluid add-form-list">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="header-title">
                                <h4 class="card-title">Add Category</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="ProductCreate" action="" data-toggle="validator" onsubmit="return false;">
                                @csrf

                                @isset($category)
                                <input type="hidden" name="modelid" value="{{ $category->id }}">
                                @endisset

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Category Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" placeholder="Enter Name"
                                                name="name"
                                                value="@isset($category){{ $category->category_name }}@endisset"
                                                data-errors="Please Enter Name." required>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" id="save_btn" class="btn btn-primary mr-2">@isset($category) Update Category @else Add Category @endisset</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Page end  -->
        </div>
    </div>

    @isset($category)
        <script>
            $("#ProductCreate").submit(function(e) {
                e.preventDefault();

                // if (document.getElementById("product_image").value != "" && !['png', 'jpeg', 'jpg'].includes(checkFileExtension('product_image'))) {
                //     return toastr.error("Please select 'png', 'jpeg', or 'jpg' image", 'Error');
                // }

                $('#save_btn').prop('disabled', true);

                var formData = new FormData(this);
                $.ajax({
                    type: "post",
                    url: '/dashboard/category/edit',
                    data: formData,
                    dataType: "json",
                    contentType: false,
                    processData: false,

                    success: function(response) {
                        if (response.error == 0) {
                            toastr.success(response.msg, 'Success');
                        } else {
                            toastr.error(response.msg, 'Error');
                        }
                    }
                });
                $('#save_btn').prop('disabled', false);
            });
        </script>
    @else
        <script>
            $("#ProductCreate").submit(function(e) {
                e.preventDefault();

                // if (document.getElementById("product_image").value != "" && !['png', 'jpeg', 'jpg'].includes(checkFileExtension('product_image'))) {
                //     return toastr.error("Please select 'png', 'jpeg', or 'jpg' image", 'Error');
                // }

                $('#save_btn').prop('disabled', true);

                var formData = new FormData(this);
                $.ajax({
                    type: "post",
                    url: '/dashboard/category/create',
                    data: formData,
                    dataType: "json",
                    contentType: false,
                    processData: false,

                    success: function(response) {
                        if (response.error == 0) {
                            toastr.success(response.msg, 'Success');
                            $("#ProductCreate")[0].reset();
                        } else {
                            toastr.error(response.msg, 'Error');
                        }
                    }
                });
                $('#save_btn').prop('disabled', false);
            });
        </script>
    @endisset
@endsection
