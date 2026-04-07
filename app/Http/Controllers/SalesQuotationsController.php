<?php

namespace App\Http\Controllers;

use App\Models\SalesQuotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesQuotationsController extends Controller
{
    public function create()
    {
        if (!Auth::check() || !isCashier()) {
            return redirect('/signin');
        }

        $suggestions = $this->getItemSuggestions();
        return view('pos.add-sales-quotation')->with(['suggestions' => $suggestions]);
    }

    public function store(Request $request)
    {
        if (!Auth::check() || !isCashier()) {
            return response(json_encode(['error' => 1, 'msg' => 'Not logged in']));
        }

        $customer_name  = sanitize($request->input('customer_name'));
        $customer_phone = sanitize($request->input('customer_phone'));
        $note           = sanitize($request->input('note'));

        if (empty($customer_name) || empty($customer_phone)) {
            return response(json_encode(['error' => 1, 'msg' => 'Customer name and phone are required']));
        }

        $items = $this->parseItems($request);

        if (empty($items)) {
            return response(json_encode(['error' => 1, 'msg' => 'At least one item is required']));
        }

        $total = array_sum(array_column($items, 'total'));

        $last  = SalesQuotation::orderBy('id', 'DESC')->first();
        $sq_no = $last ? 'SQ' . ((int)substr($last->sq_no, 2) + 1) : 'SQ10001';

        $sq = SalesQuotation::create([
            'sq_no'          => $sq_no,
            'customer_name'  => $customer_name,
            'customer_phone' => $customer_phone,
            'items'          => $items,
            'total'          => $total,
            'note'           => $note,
            'pos_code'       => company()->pos_code,
        ]);

        if ($sq) {
            generateSalesQuotation($sq_no);
            return response(json_encode(['error' => 0, 'msg' => 'Sales Quotation created successfully', 'sq_no' => $sq_no]));
        }

        return response(json_encode(['error' => 1, 'msg' => 'Something went wrong, please try again']));
    }

    public function edit($id)
    {
        if (!Auth::check() || !isCashier()) {
            return redirect('/signin');
        }

        $quotation = SalesQuotation::where('sq_no', sanitize($id))
            ->where('pos_code', company()->pos_code)
            ->first();

        if (!$quotation) {
            return display404();
        }

        $suggestions = $this->getItemSuggestions();
        return view('pos.add-sales-quotation')->with(['quotation' => $quotation, 'suggestions' => $suggestions]);
    }

    public function update(Request $request)
    {
        if (!Auth::check() || !isCashier()) {
            return response(json_encode(['error' => 1, 'msg' => 'Not logged in']));
        }

        $id             = sanitize($request->input('modelid'));
        $customer_name  = sanitize($request->input('customer_name'));
        $customer_phone = sanitize($request->input('customer_phone'));
        $note           = sanitize($request->input('note'));

        if (empty($customer_name) || empty($customer_phone)) {
            return response(json_encode(['error' => 1, 'msg' => 'Customer name and phone are required']));
        }

        $items = $this->parseItems($request);

        if (empty($items)) {
            return response(json_encode(['error' => 1, 'msg' => 'At least one item is required']));
        }

        $total  = array_sum(array_column($items, 'total'));

        $result = SalesQuotation::where('id', $id)
            ->where('pos_code', company()->pos_code)
            ->update([
                'customer_name'  => $customer_name,
                'customer_phone' => $customer_phone,
                'items'          => json_encode($items),
                'total'          => $total,
                'note'           => $note,
            ]);

        if ($result) {
            $sq_no = SalesQuotation::where('id', $id)->value('sq_no');
            generateSalesQuotation($sq_no);
            return response(json_encode(['error' => 0, 'msg' => 'Sales Quotation updated successfully']));
        }

        return response(json_encode(['error' => 1, 'msg' => 'Something went wrong, please try again']));
    }

    public function destroy(Request $request)
    {
        if (!Auth::check() || !isAdmin()) {
            return response(json_encode(['error' => 1, 'msg' => 'Only admins can delete quotations']));
        }

        $id     = sanitize($request->input('id'));
        $record = SalesQuotation::where('id', $id)->where('pos_code', company()->pos_code);

        if ($record && $record->count() > 0) {
            if ($record->delete()) {
                return response(json_encode(['error' => 0, 'msg' => 'Sales Quotation deleted successfully']));
            }
        }

        return response(json_encode(['error' => 1, 'msg' => 'Quotation not found']));
    }

    public function pdf($id)
    {
        if (!Auth::check() || !isCashier()) {
            return redirect('/signin');
        }

        $sq_no = sanitize($id);
        $result = generateSalesQuotation($sq_no);

        if (!$result) {
            abort(404);
        }

        return response()->download(public_path($result->url));
    }

    private function parseItems(Request $request): array
    {
        $names       = $request->input('item_name', []);
        $qtys        = $request->input('item_qty', []);
        $unit_prices = $request->input('item_unit_price', []);

        $items = [];
        foreach ($names as $i => $name) {
            $name = sanitize($name);
            if (empty($name)) {
                continue;
            }
            $qty        = (float)(isset($qtys[$i]) ? sanitize($qtys[$i]) : 0);
            $unit_price = (float)(isset($unit_prices[$i]) ? sanitize($unit_prices[$i]) : 0);
            $items[]    = [
                'name'       => $name,
                'qty'        => $qty,
                'unit_price' => $unit_price,
                'total'      => round($qty * $unit_price, 2),
            ];
        }

        return $items;
    }

    private function getItemSuggestions(): array
    {
        $all = SalesQuotation::where('pos_code', company()->pos_code)->pluck('items')->toArray();
        $names = [];
        foreach ($all as $itemsJson) {
            $items = is_array($itemsJson) ? $itemsJson : json_decode($itemsJson, true);
            if (is_array($items)) {
                foreach ($items as $item) {
                    if (!empty($item['name'])) {
                        $names[] = $item['name'];
                    }
                }
            }
        }
        return array_values(array_unique($names));
    }
}
