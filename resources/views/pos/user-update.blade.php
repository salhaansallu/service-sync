@extends('pos.app')

@section('dashboard')
    <div class="content-page">
        <div class="container-fluid add-form-list">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="header-title">
                                <h4 class="card-title">Update Company Profile</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="updateProfile" action="" data-toggle="validator" onsubmit="return false;">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Company name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" placeholder="Enter Company name" 
                                            name="name" value="{{ $posData->company_name }}" data-errors="Please Enter Company name." required>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Industry <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" placeholder="Enter Industry" 
                                            name="industry" value="{{ $posData->industry }}" data-errors="Please Enter Industry." required>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Country <span class="text-danger">*</span></label>
                                            <select name="country" id="" class="form-control" required>
                                                @foreach (country('get') as $country)
                                                    <option {{ $posData->country == $country? 'selected' : '' }} value="{{ $country }}">{{ $country }}</option>
                                                @endforeach
                                            </select>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>City <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" placeholder="Enter City" 
                                            name="city" value="{{ $posData->city }}" data-errors="Please Enter City." required>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Currency <span class="text-danger">*</span></label>
                                            <select name="currency" id="" class="form-control" data-errors="Please Select Currency." required>
                                                <option {{ $posData->currency == 'LKR'? 'selected' : '' }} value="LKR">LKR</option>
                                                <option {{ $posData->currency == 'USD'? 'selected' : '' }} value="USD">USD</option>
                                                <option {{ $posData->currency == 'GBP'? 'selected' : '' }} value="GBP">GBP</option>
                                                <option {{ $posData->currency == 'EUR'? 'selected' : '' }} value="EUR">EUR</option>
                                                <option {{ $posData->currency == 'INR'? 'selected' : '' }} value="INR">INR</option>
                                            </select>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary mr-2">Update</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="header-title">
                                <h4 class="card-title">Update User Profile</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="updateUserProfile" enctype="multipart/form-data" action="" data-toggle="validator" onsubmit="return false;">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Profile Image </label>
                                            <input type="file" class="form-control image-file" name="profile_pic" id="profile_pic" accept="image/*">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Country <span class="text-danger">*</span></label>
                                            <select name="country" id="" class="form-control" required>
                                                @foreach (country('get') as $country)
                                                    <option {{ $userData->country == $country? 'selected' : '' }} value="{{ $country }}">{{ $country }}</option>
                                                @endforeach
                                            </select>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Address <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" placeholder="Enter Address" 
                                            name="address" value="{{ $userData->address }}" data-errors="Please Enter Address." required>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>City <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" placeholder="Enter City" 
                                            name="city" value="{{ $userData->city }}" data-errors="Please Enter City." required>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Postal/Zip code <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" placeholder="Enter Postal/Zip Code" 
                                            name="zip" value="{{ $userData->zip }}" data-errors="Please Enter Postal/Zip Code." required>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Phone number <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" placeholder="Enter Phone Number" 
                                            name="phone" value="{{ $userData->phone }}" data-errors="Please Enter Phone Number." required>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>

                                </div>
                                <button type="submit" class="btn btn-primary mr-2">Update</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Page end  -->
        </div>
    </div>

    <script>
        $("#updateProfile").submit(function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            $.ajax({
                type: "post",
                url: '/dashboard/company/update',
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
        });

        $("#updateUserProfile").submit(function(e) {
            e.preventDefault();

            if (document.getElementById("profile_pic").value != "" && !['png', 'jpeg', 'jpg'].includes(checkFileExtension('profile_pic'))) {
                return toastr.error("Please select 'png', 'jpeg', or 'jpg' image", 'Error');
            }

            var formData = new FormData(this);
            $.ajax({
                type: "post",
                url: '/dashboard/user/update',
                data: formData,
                dataType: "json",
                contentType: false,
                processData: false,

                success: function(res) {
                    if (res.error == 0) {
                        toastr.success(res.msg, 'Success');
                    } else {
                        toastr.error(res.msg, 'Error');
                    }
                }
            });
        });
    </script>
@endsection
