<?php

namespace App\Http\Controllers;

use App\Models\ReferralCoupon;
use App\Models\POSSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReferralCouponController extends Controller
{
    public function index(Request $request)
    {
        login_redirect('/' . request()->path());
        if (!Auth::check() || !isAdmin()) {
            return redirect('/signin');
        }

        $search = trim((string) $request->query('search', ''));
        $coupons = ReferralCoupon::with('redemption')
            ->where('pos_code', company()->pos_code)
            ->when($search !== '', fn ($query) => $query->where(function ($inner) use ($search) {
                $inner->where('code', 'like', "%{$search}%")
                    ->orWhere('referrer_phone', 'like', "%{$search}%");
            }))
            ->latest()
            ->get();

        return view('pos.referral-coupons', compact('coupons', 'search'));
    }

    public function store(Request $request)
    {
        abort_unless(Auth::check() && isAdmin(), 403);
        $validated = $request->validate([
            'referrer_phone' => ['required', 'string', 'max:30'],
            'amount' => ['required', 'numeric', 'gt:0', 'max:9999999999.99'],
            'code' => ['nullable', 'string', 'max:32', 'regex:/^[A-Za-z0-9_-]+$/'],
        ]);

        $code = strtoupper($validated['code'] ?? '');
        if ($code === '') {
            do {
                $code = 'REF-' . strtoupper(Str::random(8));
            } while (ReferralCoupon::where('pos_code', company()->pos_code)->where('code', $code)->exists());
        }

        if (ReferralCoupon::where('pos_code', company()->pos_code)->where('code', $code)->exists()) {
            return back()->withErrors(['code' => 'This coupon code already exists.'])->withInput();
        }

        ReferralCoupon::create([
            'pos_code' => company()->pos_code,
            'code' => $code,
            'referrer_phone' => trim($validated['referrer_phone']),
            'amount' => $validated['amount'],
            'created_by' => Auth::id(),
        ]);

        return redirect('/dashboard/referral-coupons')->with('success', "Coupon {$code} created.");
    }

    public function verify(Request $request)
    {
        abort_unless(Auth::check() && isCashier(), 403);
        $settings = POSSettings::where('pos_code', company()->pos_code)->first();
        if ($settings && $settings->referral_coupons_enabled === 'unactive') {
            return response()->json(['error' => 1, 'msg' => 'Referral coupons are disabled in POS.'], 422);
        }

        $code = strtoupper(trim((string) $request->input('code')));
        $coupon = ReferralCoupon::where('pos_code', company()->pos_code)->where('code', $code)->first();

        if (!$coupon || $coupon->status !== 'active') {
            return response()->json(['error' => 1, 'msg' => 'Coupon is invalid or has already been redeemed.'], 422);
        }

        return response()->json([
            'error' => 0,
            'coupon' => ['code' => $coupon->code, 'amount' => $coupon->amount],
        ]);
    }

    public function toggle(Request $request)
    {
        abort_unless(Auth::check() && isAdmin(), 403);
        $enabled = $request->boolean('enabled');
        $settings = POSSettings::where('pos_code', company()->pos_code);

        if ($settings->exists()) {
            $settings->update(['referral_coupons_enabled' => $enabled ? 'active' : 'unactive']);
        } else {
            POSSettings::insert([
                'pos_code' => company()->pos_code,
                'referral_coupons_enabled' => $enabled ? 'active' : 'unactive',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return back()->with('success', 'Referral coupons are now ' . ($enabled ? 'enabled' : 'disabled') . ' in POS.');
    }

    public function markPaid(ReferralCoupon $coupon)
    {
        abort_unless(Auth::check() && isAdmin(), 403);
        abort_unless($coupon->pos_code === company()->pos_code, 404);

        if ($coupon->status !== 'redeemed' || !$coupon->redemption) {
            return back()->withErrors(['coupon' => 'Only redeemed coupons can be marked as paid.']);
        }

        DB::transaction(function () use ($coupon) {
            $coupon->update(['status' => 'paid']);
            $coupon->redemption->update(['paid_by' => Auth::id(), 'paid_at' => now()]);
        });

        return back()->with('success', "Cashback for {$coupon->code} marked as paid.");
    }
}
