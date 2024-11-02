<?php

namespace App\Http\Controllers;

use App\Models\spareSaleHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SpareSaleHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function getReport(Request $request)
    {
        if (Auth::check() && DashboardController::check(true)) {
            $fromdate = sanitize($request->input('fromdate'));
            $todate = sanitize($request->input('todate'));

            $result = DB::table('spare_sale_histories')
                ->select('spare_id', 'spare_code', 'spare_name', DB::raw('SUM(qty) as total_sold'), DB::raw('SUM(qty*cost) as total_revenew'))
                ->where('pos_code', company()->pos_code) // filter by POS code if necessary
                ->whereBetween('created_at', [$fromdate." 00:00:00", $todate." 23:59:59"]) // filter by date range
                ->groupBy('spare_id', 'spare_name') // group by unique item
                ->get();

            return response(json_encode($result));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(spareSaleHistory $spareSaleHistory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(spareSaleHistory $spareSaleHistory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, spareSaleHistory $spareSaleHistory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(spareSaleHistory $spareSaleHistory)
    {
        //
    }
}
