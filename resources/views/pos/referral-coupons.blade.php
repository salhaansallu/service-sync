@extends('pos.app')

@section('dashboard')
<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-2">Referral Coupons</h4>
                        <p class="mb-0">Create referral cashback coupons and verify redemptions by code or phone number.</p>
                    </div>
                    @php
                        $couponFeatureEnabled = !isset(POSSettings()->referral_coupons_enabled)
                            || POSSettings()->referral_coupons_enabled !== 'unactive';
                    @endphp
                    <form method="post" action="/dashboard/referral-coupons/toggle">
                        @csrf
                        <input type="hidden" name="enabled" value="{{ $couponFeatureEnabled ? '0' : '1' }}">
                        <button class="btn {{ $couponFeatureEnabled ? 'btn-danger' : 'btn-success' }}" type="submit">
                            {{ $couponFeatureEnabled ? 'Turn Off in POS' : 'Turn On in POS' }}
                        </button>
                        <div class="text-center mt-1">
                            <small>POS: {{ $couponFeatureEnabled ? 'Enabled' : 'Disabled' }}</small>
                        </div>
                    </form>
                </div>
                @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
                @if($errors->any()) <div class="alert alert-danger">{{ $errors->first() }}</div> @endif
            </div>

            <div class="col-lg-4">
                <div class="card p-3 mb-4">
                    <h5 class="mb-3">Create Coupon</h5>
                    <form method="post" action="/dashboard/referral-coupons">
                        @csrf
                        <div class="form-group">
                            <label>Referrer phone number</label>
                            <input class="form-control" name="referrer_phone" value="{{ old('referrer_phone') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Cashback amount</label>
                            <input class="form-control" type="number" min="0.01" step="0.01" name="amount" value="{{ old('amount') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Coupon code <small>(optional)</small></label>
                            <input class="form-control text-uppercase" name="code" maxlength="32" value="{{ old('code') }}" placeholder="Auto-generated if empty">
                        </div>
                        <button class="btn btn-primary" type="submit">Create Coupon</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-8">
                <form class="d-flex mb-3" method="get">
                    <input class="form-control mr-2" name="search" value="{{ $search }}" placeholder="Search coupon code or phone number">
                    <button class="btn btn-primary" type="submit">Search</button>
                </form>
                <div class="table-responsive rounded mb-3">
                    <table class="table mb-0">
                        <thead class="bg-white"><tr><th>Code</th><th>Phone</th><th>Amount</th><th>Status</th><th>Bill</th><th>Redeemed</th><th>Action</th></tr></thead>
                        <tbody>
                        @forelse($coupons as $coupon)
                            <tr>
                                <td><strong>{{ $coupon->code }}</strong></td>
                                <td>{{ $coupon->referrer_phone }}</td>
                                <td>{{ number_format($coupon->amount, 2) }}</td>
                                <td><span class="badge {{ $coupon->status === 'paid' ? 'bg-success' : ($coupon->status === 'redeemed' ? 'bg-warning' : 'bg-primary') }}">{{ ucfirst($coupon->status) }}</span></td>
                                <td>{{ optional($coupon->redemption)->bill_no ?: '-' }}</td>
                                <td>{{ optional(optional($coupon->redemption)->redeemed_at)->format('Y-m-d H:i') ?: '-' }}</td>
                                <td>
                                    @if($coupon->status === 'redeemed')
                                    <form method="post" action="/dashboard/referral-coupons/{{ $coupon->id }}/paid" onsubmit="return confirm('Confirm that this cashback was paid?')">
                                        @csrf
                                        <button class="btn btn-sm btn-success">Mark Paid</button>
                                    </form>
                                    @else - @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center">No coupons found.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
