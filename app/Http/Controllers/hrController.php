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

        if (isAdmin()) {
            return view('pos.hr');
        }

        return redirect('/signin');
    }

    public function getUsers()
    {
        if (isAdmin()) {
            return response(json_encode(User::all()));
        }
    }

    public function getReport(Request $request)
    {
        if (isAdmin()) {
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
        if (isAdmin()) {
            $user = sanitize($request->input('user'));
            $amount = sanitize($request->input('amount'));
            $type = sanitize($request->input('type'));
            $date = sanitize($request->input('date'));
            $note = sanitize($request->input('note'));

            $data = new employee_expenses();
            $data->user = $user;
            $data->note = $note;
            $data->amount = $amount;
            $data->type = $type;
            $data->created_at = date('Y-m-d H:i:s', strtotime($date));
            $data->updated_at = date('Y-m-d H:i:s', strtotime($date));

            if ($data->save()) {
                return response(json_encode(['error' => 0, 'message' => 'Expense added successfully.']));
            }

            return response(json_encode(['error' => 1, 'message' => 'Error while adding expense.']));
        }
    }

    public function printExpense(Request $request)
    {
        if (isAdmin()) {
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
        if (isAdmin()) {
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
        if (isAdmin()) {
            $id = sanitize($request->input('id'));
            return json_encode(['error' => 0, 'balance' => employee_expenses::where('user', $id)->where('type', 'Loan')->where(function ($q) {
                $q->where('status', '!=', 'paid')->orWhereNull('status');
            })->sum('amount')]);
        }
    }
}
