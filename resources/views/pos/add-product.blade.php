@extends('pos.app')

@section('dashboard')
    <div class="content-page">
        <div class="container-fluid add-form-list">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="header-title">
                                <h4 class="card-title">Add Spare Part</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="ProductCreate" action="" data-toggle="validator" onsubmit="return false;">
                                @csrf

                                @isset($product)
                                <input type="hidden" name="modelid" value="{{ $product->id }}">
                                @endisset

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Product Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" placeholder="Enter Name"
                                                name="name"
                                                value="@isset($product){{ $product->pro_name }}@endisset"
                                                data-errors="Please Enter Name." required>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Code <span class="text-danger">*</span> <small
                                                    class="text-secondary">(Scan barcode from scanner to enter
                                                    automatically)</small></label>
                                            <input type="text" class="form-control" placeholder="Enter Code"
                                                name="code"
                                                value="@isset($product){{ $product->sku }}@endisset"
                                                data-errors="Please Enter Code." id="BarCodeValue" required>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    {{-- <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Category</label>
                                            <select name="category" class="selectpicker form-control" data-style="py-0">
                                                <option value="other">Other</option>
                                                @foreach (getCategory('all') as $cate)
                                                    <option
                                                        @isset($product) {{ $cate->id == $product->category ? 'selected' : '' }} @endisset
                                                        value="{{ $cate->id }}">{{ $cate->category_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div> --}}
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Cost <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" placeholder="Enter Cost"
                                                name="cost"
                                                value="{{ isset($_GET['cost'])?sanitize($_GET['cost']): (isset($product)?$product->cost:0) }}"
                                                data-errors="Please Enter Cost." required>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Price (only if the part is listed for sale)</label>
                                            <input type="text" class="form-control" placeholder="Enter Price"
                                                name="price"
                                                value="@isset($product){{ $product->price }}@else{{ '0' }}@endisset">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Stock <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" placeholder="Enter Stock"
                                                name="stock"
                                                value="{{ isset($_GET['qty'])? sanitize($_GET['qty'])+(isset($product)?$product->qty:0) : (isset($product)?$product->qty:0) }}"
                                                data-errors="Please Enter Stock." required>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Supplier</label>
                                            <select name="supplier" class="selectpicker form-control" data-style="py-0">
                                                <option value="other">Other</option>
                                                @foreach (getSupplier('all') as $supplier)
                                                    <option
                                                        @isset($product) {{ $supplier->id == $product->supplier ? 'selected' : '' }} @endisset
                                                        value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Image</label>
                                            <input type="file" class="form-control image-file" name="product_image"
                                                accept="image/*" id="product_image">
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" id="save_btn" class="btn btn-primary mr-2">@isset($product) Update product @else Add Product @endisset</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Page end  -->
        </div>
    </div>

    @isset($product)
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
                    url: '/dashboard/products/edit',
                    data: formData,
                    dataType: "json",
                    contentType: false,
                    processData: false,

                    success: function(response) {
                        if (response.error == 0) {
                            toastr.success(response.msg, 'Success');
                            setInterval(() => {
                                location.href="/dashboard/products/edit/"+response.sku;
                            }, 3000);
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
                    url: '/dashboard/products/create',
                    data: formData,
                    dataType: "json",
                    contentType: false,
                    processData: false,

                    success: function(response) {
                        if (response.error == 0) {
                            toastr.success(response.msg, 'Success');
                            setInterval(() => {
                                location.reload();
                            }, 3000);
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
