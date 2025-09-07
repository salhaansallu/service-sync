@extends('pos.app')

@php
    use Carbon\Carbon;

    // Get values from .env
    $serverCreated = env('SERVER_CREATED', 'now');
    $domainCreated = env('DOMAIN_CREATED', 'now');
    $serverDuration = env('SERVER_DURATION', ($serverCreated == 'now'? '0 days' : '+1 year'));
    $domainDuration = env('DOMAIN_DURATION', ($domainCreated == 'now'? '0 days' : '+1 year'));

    // Calculate end dates
    $serverEndDate = date('Y-m-d', strtotime($serverCreated . ' ' . $serverDuration));
    $domainEndDate = date('Y-m-d', strtotime($domainCreated . ' ' . $domainDuration));

    // Calculate time differences
    $now = Carbon::now();
    $serverDiff = $now->diff(Carbon::parse($serverEndDate));
    $domainDiff = $now->diff(Carbon::parse($domainEndDate));

    // Calculate progress percentages
    $serverTotalDays = Carbon::parse($serverCreated)->diffInDays(Carbon::parse($serverEndDate));
    $serverDaysPassed = Carbon::parse($serverCreated)->diffInDays($now);
    $serverProgress = $serverCreated != 'now'? (($serverTotalDays - $serverDaysPassed) / $serverTotalDays) * 100 : 0;

    $domainTotalDays = Carbon::parse($domainCreated)->diffInDays(Carbon::parse($domainEndDate));
    $domainDaysPassed = Carbon::parse($domainCreated)->diffInDays($now);
    $domainProgress = $domainCreated != 'now'? (($domainTotalDays - $domainDaysPassed) / $domainTotalDays) * 100 : 0;
@endphp

@section('dashboard')
    <div class="content-page">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div id="AlertMessage" class="alert alert-danger alert-dismissible fade show d-none" role="alert">
                        <span></span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card card-transparent card-block card-stretch card-height border-none">
                        <div class="card-body p-0 mt-0">
                            <h3 class="mb-3">Hello {{ Auth::user()->fname }}, Welcome back</h3>
                            <p class="mb-0 mr-4">Your dashboard gives you views of key performance or business process. </p>
                        </div>
                    </div>
                </div>
                @if (isAdmin())
                    <div class="col-lg-9">
                        <div class="row">
                            <div class="col-lg-3 col-md-3">
                                <div class="card card-block card-stretch card-height">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center card-total-sale">
                                            <div class="icon iq-icon-box-2 bg-info-light">
                                                <i class="fa-solid fa-chart-line"></i>
                                            </div>
                                            <div>
                                                <p class="mb-2">Today's Sales</p>
                                                <h4>{{ currency($todaysales, $company->currency) }}</h4>
                                            </div>
                                        </div>
                                        {{-- <div class="iq-progress-bar mt-2">
                                    <span class="bg-info iq-progress progress-1" data-percent="85">
                                    </span>
                                </div> --}}
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3">
                                <div class="card card-block card-stretch card-height">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center card-total-sale">
                                            <div class="icon iq-icon-box-2 bg-danger-light">
                                                <i class="fa-solid fa-coins"></i>
                                            </div>
                                            <div>
                                                <p class="mb-2">Today's Cost</p>
                                                <h4>{{ currency($cost, $company->currency) }}</h4>
                                            </div>
                                        </div>
                                        {{-- <div class="iq-progress-bar mt-2">
                                    <span class="bg-danger iq-progress progress-1" data-percent="70">
                                    </span>
                                </div> --}}
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3">
                                <div class="card card-block card-stretch card-height">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center card-total-sale">
                                            <div class="icon iq-icon-box-2 bg-success-light">
                                                <i class="fa-solid fa-hand-holding-dollar"></i>
                                            </div>
                                            <div>
                                                <p class="mb-2">Today's Profit</p>
                                                <h4>{{ currency((float) ($todaysales - $cost), $company->currency) }}</h4>
                                            </div>
                                        </div>
                                        {{-- <div class="iq-progress-bar mt-2">
                                    <span class="bg-success iq-progress progress-1" data-percent="75">
                                    </span>
                                </div> --}}
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3">
                                <div class="card card-block card-stretch card-height">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center card-total-sale">
                                            <div class="icon iq-icon-box-2 bg-warning-light">
                                                <i class="fa-solid fa-comment-sms"></i>
                                            </div>
                                            <div>
                                                <p class="mb-2">SMS Balance</p>
                                                <h4 id="smsBalance">
                                                    <div class="spinner-border text-warning" role="status">
                                                        <span class="sr-only">Loading...</span>
                                                    </div>
                                                </h4>
                                            </div>
                                        </div>
                                        {{-- <div class="iq-progress-bar mt-2">
                                    <span class="bg-success iq-progress progress-1" data-percent="75">
                                    </span>
                                </div> --}}
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3">
                                <div class="card card-block card-stretch card-height">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center card-total-sale">
                                            <div class="icon iq-icon-box-2 bg-warning-light">
                                                <i class="fa-solid fa-screwdriver-wrench"></i>
                                            </div>
                                            <div>
                                                <p class="mb-2">New Orders</p>
                                                <h4>{{ $todayRepairsIn }}</h4>
                                            </div>
                                        </div>
                                        {{-- <div class="iq-progress-bar mt-2">
                                    <span class="bg-success iq-progress progress-1" data-percent="75">
                                    </span>
                                </div> --}}
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3">
                                <div class="card card-block card-stretch card-height">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center card-total-sale">
                                            <div class="icon iq-icon-box-2 bg-success-light">
                                                <i class="fa-regular fa-circle-check"></i>
                                            </div>
                                            <div>
                                                <p class="mb-2">Repaired Orders</p>
                                                <h4>{{ $todayRepaired }}</h4>
                                            </div>
                                        </div>
                                        {{-- <div class="iq-progress-bar mt-2">
                                    <span class="bg-success iq-progress progress-1" data-percent="75">
                                    </span>
                                </div> --}}
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3">
                                <div class="card card-block card-stretch card-height">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center card-total-sale">
                                            <div class="icon iq-icon-box-2 bg-primary-light">
                                                <i class="fa-solid fa-people-carry-box"></i>
                                            </div>
                                            <div>
                                                <p class="mb-2">Delivered Orders</p>
                                                <h4>{{ $todayRepairsOut }}</h4>
                                            </div>
                                        </div>
                                        {{-- <div class="iq-progress-bar mt-2">
                                    <span class="bg-success iq-progress progress-1" data-percent="75">
                                    </span>
                                </div> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="row py-5 pt-3">
                            <div class="col-lg-6">
                                <div class="countdown-container">
                                    <h3>Server Expiring In</h3>
                                    <div class="row mt-4">
                                        <div class="col-3 countdown-item d-flex align-items-center justify-content-between">
                                            <div class="timer text-center">
                                                <span class="countdown-value fs-2">{{ $serverDiff->y }}</span>
                                                <div class="countdown-label form-text">Years</div>
                                            </div>
                                            <div class="colon fs-2 mx-5">:</div>
                                        </div>
                                        <div class="col-3 countdown-item d-flex align-items-center justify-content-between">
                                            <div class="timer text-center">
                                                <span class="countdown-value fs-2">{{ $serverDiff->m }}</span>
                                                <div class="countdown-label form-text">Months</div>
                                            </div>
                                            <div class="colon fs-2 mx-5">:</div>
                                        </div>
                                        <div class="col-3 countdown-item d-flex align-items-center">
                                            <div class="timer text-center">
                                                <span class="countdown-value fs-2">{{ $serverDiff->d }}</span>
                                                <div class="countdown-label form-text">Days</div>
                                            </div>
                                        </div>
                                        <div class="col-10 mt-4">
                                            <div class="progress-section" style="height: 10px; width: 100%;background-color: #e7e7e7;">
                                                <div class="progress-bar {{ abs($serverProgress) > 50 ? 'bg-success' : (abs($serverProgress) > 25? 'bg-warning' : 'bg-danger') }}" style="width: {{ abs($serverProgress) }}%;height: 10px;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="countdown-container">
                                    <h3>Domain Expiring In</h3>
                                    <div class="row mt-4">
                                        <div class="col-3 countdown-item d-flex align-items-center justify-content-between">
                                            <div class="timer text-center">
                                                <span class="countdown-value fs-2">{{ $domainDiff->y }}</span>
                                                <div class="countdown-label form-text">Years</div>
                                            </div>
                                            <div class="colon fs-2 mx-5">:</div>
                                        </div>
                                        <div class="col-3 countdown-item d-flex align-items-center justify-content-between">
                                            <div class="timer text-center">
                                                <span class="countdown-value fs-2">{{ $domainDiff->m }}</span>
                                                <div class="countdown-label form-text">Months</div>
                                            </div>
                                            <div class="colon fs-2 mx-5">:</div>
                                        </div>
                                        <div class="col-3 countdown-item d-flex align-items-center">
                                            <div class="timer text-center">
                                                <span class="countdown-value fs-2">{{ $domainDiff->d }}</span>
                                                <div class="countdown-label form-text">Days</div>
                                            </div>
                                        </div>
                                        <div class="col-10 mt-4">
                                            <div class="progress-section" style="height: 10px; width: 100%;background-color: #d2d2d2;">
                                                <div class="progress-bar {{ abs($domainProgress) > 50 ? 'bg-success' : (abs($domainProgress) > 25? 'bg-warning' : 'bg-danger') }}" style="width: {{ abs($domainProgress) }}%;height: 10px;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card card-block card-stretch card-height">
                            <div class="card-header d-flex justify-content-between">
                                <div class="header-title">
                                    <h4 class="card-title">Overview</h4>
                                </div>
                                @if (company()->plan == 1)
                                    <span><a href="/pricing">Upgrade premium</a> to use this feature</span>
                                @endif
                                {{-- <div class="card-header-toolbar d-flex align-items-center">
                            <div class="dropdown">
                                <span class="dropdown-toggle dropdown-bg btn" id="dropdownMenuButton001"
                                    data-toggle="dropdown">
                                    This Month<i class="ri-arrow-down-s-line ml-1"></i>
                                </span>
                                <div class="dropdown-menu dropdown-menu-right shadow-none"
                                    aria-labelledby="dropdownMenuButton001">
                                    <a class="dropdown-item" href="#">Year</a>
                                    <a class="dropdown-item" href="#">Month</a>
                                    <a class="dropdown-item" href="#">Week</a>
                                </div>
                            </div>
                        </div> --}}
                            </div>
                            <div class="card-body">
                                <div id="overviewChart">
                                    <dashboard-overview v-bind:overviewsales="{{ $sales }}" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card card-block card-stretch card-height">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <div class="header-title">
                                    <h4 class="card-title">Profit Vs Cost</h4>
                                </div>
                                {{-- <div class="card-header-toolbar d-flex align-items-center">
                            <div class="dropdown">
                                <span class="dropdown-toggle dropdown-bg btn" id="dropdownMenuButton002"
                                    data-toggle="dropdown">
                                    This Month<i class="ri-arrow-down-s-line ml-1"></i>
                                </span>
                                <div class="dropdown-menu dropdown-menu-right shadow-none"
                                    aria-labelledby="dropdownMenuButton002">
                                    <a class="dropdown-item" href="#">Yearly</a>
                                    <a class="dropdown-item" href="#">Monthly</a>
                                    <a class="dropdown-item" href="#">Weekly</a>
                                </div>
                            </div>
                        </div> --}}
                            </div>
                            <div class="card-body">
                                <div id="revenewChart" style="min-height: 360px;">
                                    <dashboard-revenew v-bind:sales="{{ $sales }}"
                                        v-bind:cost="{{ $yearcost }}" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="card card-block card-stretch card-height">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <div class="header-title">
                                    <h4 class="card-title">Low Stock Products</h4>
                                </div>
                                <a href="/dashboard/low-stock-products">View all</a>
                                {{-- <div class="card-header-toolbar d-flex align-items-center">
                            <div class="dropdown">
                                <span class="dropdown-toggle dropdown-bg btn" id="dropdownMenuButton006"
                                    data-toggle="dropdown">
                                    This Month<i class="ri-arrow-down-s-line ml-1"></i>
                                </span>
                                <div class="dropdown-menu dropdown-menu-right shadow-none"
                                    aria-labelledby="dropdownMenuButton006">
                                    <a class="dropdown-item" href="#">Year</a>
                                    <a class="dropdown-item" href="#">Month</a>
                                    <a class="dropdown-item" href="#">Week</a>
                                </div>
                            </div>
                        </div> --}}
                            </div>
                            <div class="table-responsive mb-3">
                                <table class="table mb-0 tbl-server-info">
                                    <thead class="bg-white text-uppercase">
                                        <tr class="ligth ligth-data">
                                            <th>Image</th>
                                            <th>SKU</th>
                                            <th>Product Name</th>
                                            <th>Stock</th>
                                            <th>Edit</th>
                                        </tr>
                                    </thead>
                                    <tbody class="ligth-body">
                                        @if ($low_stock && $low_stock->count() > 0)
                                            @foreach ($low_stock as $product)
                                                <tr>
                                                    <td>
                                                        <img class="low_stock_image"
                                                            src="{{ productImage($product['pro_image']) }}"
                                                            alt="">
                                                    </td>
                                                    <td>{{ $product['sku'] }}</td>
                                                    <td>{{ $product['pro_name'] }}</td>
                                                    <td>{{ $product['qty'] }}</td>
                                                    <td>
                                                        <div class="d-flex align-items-center list-action">
                                                            <a class="badge bg-success mr-2" data-toggle="tooltip"
                                                                data-placement="top" title=""
                                                                data-original-title="Edit" href="#"><i
                                                                    class="ri-pencil-line mr-0"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="fs-2 fw-bold text-center my-5 text-secondary">
                        Welcome to your dashboard
                    </div>

                    <div class="col-lg-12">
                        <div class="row py-5">
                            <div class="col-lg-6">
                                <div class="countdown-container">
                                    <h3>Server Expiring In</h3>
                                    <div class="row mt-4">
                                        <div class="col-3 countdown-item d-flex align-items-center justify-content-between">
                                            <div class="timer text-center">
                                                <span class="countdown-value fs-2">{{ $serverDiff->y }}</span>
                                                <div class="countdown-label form-text">Years</div>
                                            </div>
                                            <div class="colon fs-2 mx-5">:</div>
                                        </div>
                                        <div class="col-3 countdown-item d-flex align-items-center justify-content-between">
                                            <div class="timer text-center">
                                                <span class="countdown-value fs-2">{{ $serverDiff->m }}</span>
                                                <div class="countdown-label form-text">Months</div>
                                            </div>
                                            <div class="colon fs-2 mx-5">:</div>
                                        </div>
                                        <div class="col-3 countdown-item d-flex align-items-center">
                                            <div class="timer text-center">
                                                <span class="countdown-value fs-2">{{ $serverDiff->d }}</span>
                                                <div class="countdown-label form-text">Days</div>
                                            </div>
                                        </div>
                                        <div class="col-10 mt-4">
                                            <div class="progress-section" style="height: 10px; width: 100%;background-color: #e7e7e7;">
                                                <div class="progress-bar {{ abs($serverProgress) > 50 ? 'bg-success' : (abs($serverProgress) > 25? 'bg-warning' : 'bg-danger') }}" style="width: {{ abs($serverProgress) }}%;height: 10px;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="countdown-container">
                                    <h3>Domain Expiring In</h3>
                                    <div class="row mt-4">
                                        <div class="col-3 countdown-item d-flex align-items-center justify-content-between">
                                            <div class="timer text-center">
                                                <span class="countdown-value fs-2">{{ $domainDiff->y }}</span>
                                                <div class="countdown-label form-text">Years</div>
                                            </div>
                                            <div class="colon fs-2 mx-5">:</div>
                                        </div>
                                        <div class="col-3 countdown-item d-flex align-items-center justify-content-between">
                                            <div class="timer text-center">
                                                <span class="countdown-value fs-2">{{ $domainDiff->m }}</span>
                                                <div class="countdown-label form-text">Months</div>
                                            </div>
                                            <div class="colon fs-2 mx-5">:</div>
                                        </div>
                                        <div class="col-3 countdown-item d-flex align-items-center">
                                            <div class="timer text-center">
                                                <span class="countdown-value fs-2">{{ $domainDiff->d }}</span>
                                                <div class="countdown-label form-text">Days</div>
                                            </div>
                                        </div>
                                        <div class="col-10 mt-4">
                                            <div class="progress-section" style="height: 10px; width: 100%;background-color: #d2d2d2;">
                                                <div class="progress-bar {{ abs($domainProgress) > 50 ? 'bg-success' : (abs($domainProgress) > 25? 'bg-warning' : 'bg-danger') }}" style="width: {{ abs($domainProgress) }}%;height: 10px;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            @if (isAdmin())
                <div class="row" id="dashboard_charts">
                    <dashboard-charts v-bind:dashsales="{{ $sales }}" v-bind:expense="{{ $yearexpense }}" />
                </div>
            @endif
            <!-- Page end  -->
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $.ajax({
                type: "post",
                url: "/dashboard/sms/get-balance",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                dataType: "json",
                success: function(response) {
                    if (response.balance != undefined) {
                        $("#smsBalance").text(response.balance);
                        if (response.balance < 50) {
                            $("#AlertMessage").removeClass('d-none');
                            $("#AlertMessage > span").text(
                                "SMS fund balance low. Please reload to use this service without any interruption."
                            );
                        }
                    } else {
                        $("#smsBalance").text(0);
                    }

                }
            });
        });
    </script>
@endsection
