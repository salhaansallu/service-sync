@extends('partner-portal.app')

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
                            <h3 class="mb-3">Hello {{ partner()->name }}, Welcome back</h3>
                            <p class="mb-0 mr-4">Your dashboard gives you views of key performance or business process. </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="row">
                        <div class="col-lg-3 col-md-3">
                            <div class="card card-block card-stretch card-height">
                                <div class="card-body">
                                    <div class="d-flex align-items-center card-total-sale">
                                        <div class="icon iq-icon-box-2 bg-warning-light">
                                            <i class="fa-solid fa-screwdriver-wrench"></i>
                                        </div>
                                        <div>
                                            <p class="mb-2">Pending Repairs</p>
                                            <h4>{{ $repairs->pending }}</h4>
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
                                        <div class="icon iq-icon-box-2 bg-info-light">
                                            <i class="fa-solid fa-check"></i>
                                        </div>
                                        <div>
                                            <p class="mb-2">Finished Repairs</p>
                                            <h4>{{ $repairs->repaired }}</h4>
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
                                            <i class="fa-solid fa-truck-fast"></i>
                                        </div>
                                        <div>
                                            <p class="mb-2">Delivered Repairs</p>
                                            <h4>{{ $repairs->delivered }}</h4>
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
                                            <i class="fa-solid fa-boxes-stacked"></i>
                                        </div>
                                        <div>
                                            <p class="mb-2">Total Repairs</p>
                                            <h4>{{ $repairs->all }}</h4>
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
                <div class="col-lg-3 col-md-3">
                    <div class="card card-block card-stretch card-height">
                        <div class="card-body">
                            <div class="d-flex align-items-center card-total-sale">
                                <div class="icon iq-icon-box-2 bg-warning-light">
                                    <i class="fa-solid fa-receipt"></i>
                                </div>
                                <div>
                                    <p class="mb-2">Pending Payments</p>
                                    <h4>{{ currency($payments->pending) }}</h4>
                                </div>
                            </div>
                            {{-- <div class="iq-progress-bar mt-2">
                            <span class="bg-info iq-progress progress-1" data-percent="85">
                            </span>
                        </div> --}}
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="card card-block card-stretch card-height">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <div class="header-title">
                                <h4 class="card-title">Latest Finished Repairs</h4>
                            </div>
                        </div>
                        <div class="table-responsive mb-3">
                            <table class="table mb-0 tbl-server-info">
                                <thead class="bg-white text-uppercase">
                                    <tr class="ligth ligth-data">
                                        <th class="text-start">Bill No</th>
                                        <th>Model No</th>
                                        <th>Serial No</th>
                                        <th>Customer</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody class="ligth-body">
                                    @if (isset($finishedRepairs) && $finishedRepairs->count() > 0)
                                        @foreach ($finishedRepairs as $repair)
                                            <tr>
                                                <td>{{ $repair->bill_no }}</td>
                                                <td>{{ $repair->model_no }}</td>
                                                <td>{{ $repair->serial_no }}</td>
                                                <td>{{ getCustomer($repair->customer)->phone }} ({{ getCustomer($repair->customer)->name }})</td>
                                                <td>{{ $repair->created_at }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center list-action">
                                                        <a class="badge bg-success mr-2 p-2" data-toggle="tooltip"
                                                            data-placement="top" title="" data-original-title="Edit"
                                                            href="/partner-portal/repair/{{ $repair->id }}"><i class="ri-eye-line mr-0"></i></a>
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
            </div>
        </div>
    </div>
@endsection
