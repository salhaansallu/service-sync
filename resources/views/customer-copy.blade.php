@extends('layouts.app')

@section('content')
    <style>
        body {
            background-color: #f3f3f3 !important;
        }
        .customer-copy {
            padding-top: 110px;
            padding-bottom: 50px;
        }
    </style>

<div class="container customer-copy">
    <div class="row">
        <div class="col-lg-12">
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                <div>
                    <h4 class="mb-3">Customer Copy Of Purchased Invoice</h4>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="table-responsive rounded mb-3">
                <table class="data-table table mb-0 tbl-server-info">
                    <thead class="bg-white text-uppercase">
                        <tr class="ligth ligth-data">
                            <th class="text-start">Order Number</th>
                            <th class="text-start">Customer</th>
                            <th class="text-start">Total</th>
                            <th class="text-start">Service Charge</th>
                            <th class="text-start">Round Up</th>
                            <th class="text-start">Payment Method</th>
                            <th class="text-start">Order Date</th>
                        </tr>
                    </thead>
                    <tbody class="ligth-body">
                        <tr>
                            <td class="text-start">{{ $order->order_number }}</td>
                            <td class="text-start">{{ getCustomer($order->customer)->name }}</td>
                            <td class="text-start">{{ currency($order->total, '') }}</td>
                            <td class="text-start">{{ currency($order->service_charge, '') }}</td>
                            <td class="text-start">{{ currency($order->roundup, '') }}</td>
                            <td class="text-start">
                                <div class="badge badge-{{ paymentMethod($order->payment_method)->class }}">{{ paymentMethod($order->payment_method)->method }}</div>
                            </td>
                            <td class="text-start">{{ date('d-m-Y', strtotime($order->created_at)) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="table-responsive rounded mb-3">
                <table class="data-table table mb-0 tbl-server-info">
                    <thead class="bg-white text-uppercase">
                        <tr class="ligth ligth-data">
                            <th class="text-start">Product</th>
                            <th class="text-start">Code</th>
                            <th class="text-start">Quantity</th>
                            <th class="text-start">Price</th>
                            <th class="text-start">Discount</th>
                            <th class="text-start">Total</th>
                            <th class="text-start">Saved</th>
                        </tr>
                    </thead>
                    <tbody class="ligth-body">
                        @foreach ($products as $item)
                        <tr>
                            <td class="text-start">{{ $item->pro_name }}</td>
                            <td class="text-start">{{  $item->sku }}</td>
                            <td class="text-start">{{ $item->qty }}</td>
                            <td class="text-start">{{ currency($item->price, '') }}</td>
                            <td class="text-start">{{ $item->discount_mod=='am'? currency($item->discount, '') : $item->discount.'%' }}</td>
                            @if ($item->discount == 0)

                            <td class="text-start">{{ currency($item->price * $item->qty, '') }}</td>

                            @else
                            <td class="text-start">{{ currency($item->discounted_price * $item->qty, '') }}</td>
                            @endif
                            <td class="text-start">{{ $item->discount =="0"? '0.00' : currency($item->price - $item->discounted_price, '') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Page end  -->
</div>
@endsection
