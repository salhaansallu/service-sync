<?php

namespace App\Http\Controllers;

use App\Models\employee_expenses;
use App\Models\User;
use Illuminate\Http\Request;

class hrController extends Controller
{
    public function index()
    {
        login_redirect('/' . request()->path());

        if (isCashier()) {
            return view('pos.hr');
        }

        return redirect('/signin');
    }

    public function getUsers()
    {
        if (isCashier()) {
            return response(json_encode(User::all()));
        }
    }

    public function getReport(Request $request)
    {
        if (isCashier()) {
            $fromdate = sanitize($request->input('fromdate'));
            $todate = sanitize($request->input('todate'));
            $cashier = sanitize($request->input('cashier'));
            $type = sanitize($request->input('type'));
            $fromdate = date('Y-m-d ', strtotime($fromdate)).' 00:00:00';
            $todate = date('Y-m-d ', strtotime($todate)).' 23:59:59';
            $qry = employee_expenses::whereBetween('created_at', [$fromdate, $todate]);
            if ($cashier != '') {
                $qry->where('user', $cashier);
            }
            if ($type != '') {
                $qry->where('type', $type);
            }
            $qry->orderBy('created_at', 'desc');
            $data = $qry->get();
            return response(json_encode($data));
        }
    }

    public function addExpense(Request $request)
    {
        if (isCashier()) {
            $user = sanitize($request->input('user'));
            $date = sanitize($request->input('date'));
            $note = sanitize($request->input('note'));

            $types = ['salary', 'food', 'transport', 'bonus', 'commission', 'medical', 'accommodation', 'ot', 'loan'];

            foreach ($types as $key => $type) {
                if ($request->has($type) && !empty(sanitize($request->input($type))) && is_numeric(sanitize($request->input($type))) && sanitize($request->input($type)) > 0) {
                    $data = new employee_expenses();
                    $data->user = $user;
                    $data->note = $note;
                    $data->amount = sanitize($request->input($type));
                    $data->type = ucfirst($type);
                    $data->created_at = date('Y-m-d H:i:s', strtotime($date));
                    $data->updated_at = date('Y-m-d H:i:s', strtotime($date));
                    $data->save();
                }
            }

            return response(json_encode(['error' => 0, 'message' => 'Expenses added successfully.']));
        }

        return response(json_encode(['error' => 1, 'message' => 'Error while adding expense.']));
    }

    public function printExpense(Request $request)
    {
        if (isCashier()) {
            $fromdate = sanitize($request->input('fromdate'));
            $todate = sanitize($request->input('todate'));
            $cashier = sanitize($request->input('cashier'));
            $type = sanitize($request->input('type'));
            $fromdate = date('Y-m-d ', strtotime($fromdate)).' 00:00:00';
            $todate = date('Y-m-d ', strtotime($todate)).' 23:59:59';
            $qry = employee_expenses::whereBetween('created_at', [$fromdate, $todate]);
            if ($cashier != '') {
                $qry->where('user', $cashier);
            }
            if ($type != '') {
                $qry->where('type', $type);
            }
            $qry->orderBy('created_at', 'desc');
            $data = $qry->get();

            if ($data->count() > 0) {
                $pdf = generateEmployeeExpenses($data);
                if ($pdf->generated) {
                    return response(json_encode(['error' => 0, 'message' => 'Report generated successfully.', 'url' => $pdf->url]));
                }
                return response(json_encode(['error' => 1, 'message' => 'Error while generating report.']));
            }

            return response(json_encode(['error' => 1, 'message' => 'No data found.']));
        }
    }

    public function removeExpense(Request $request)
    {
        if (isAdmin()) {
            $id = sanitize($request->input('id'));

            if (employee_expenses::where('id', $id)->delete()) {
                return response(json_encode(['error' => 0, 'message' => 'Expense removed successfully.']));
            }

            return response(json_encode(['error' => 1, 'message' => 'Error removing expense.']));
        }
    }

    public function payExpense(Request $request)
    {
        if (isCashier()) {
            $id = sanitize($request->input('id'));
            $all = sanitize($request->input('all'));

            if ($all) {
                $data = employee_expenses::where('user', $id)->where('type', 'Loan')->update(['status'=> 'paid']);
                if ($data) {
                    return response(json_encode(['error' => 0, 'message' => 'All loans paid successfully.']));
                }
                return response(json_encode(['error' => 1, 'message' => 'Error paying all loans.']));
            }

            if (employee_expenses::where('id', $id)->update(['status'=> 'paid'])) {
                return response(json_encode(['error' => 0, 'message' => 'Expense paid successfully.']));
            }

            return response(json_encode(['error' => 1, 'message' => 'Error paying expense.']));
        }
    }

    public function getLoanBalance(Request $request)
    {
        if (isCashier()) {
            $id = sanitize($request->input('id'));
            return json_encode(['error' => 0, 'balance' => employee_expenses::where('user', $id)->where('type', 'Loan')->where(function ($q) {
                $q->where('status', '!=', 'paid')->orWhereNull('status');
            })->sum('amount')]);
        }
    }
}
