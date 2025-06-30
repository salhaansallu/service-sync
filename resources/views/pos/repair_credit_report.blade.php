<!DOCTYPE html>
<html>
<head>
    <title>Repair & Credit Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        h2 { margin-top: 40px; }
    </style>
</head>
<body>

    <h1>Repair & Credit Report {{ count($repairs) > 0? 'of '.$repairs[0]->partner_name : '' }}</h1>
    <p>Generated on: {{ \Carbon\Carbon::now()->format('Y-m-d H:i:s') }}</p>

    <h2>Repairs</h2>
    <table>
        <thead>
            <tr>
                <th>Bill No</th>
                <th>Model</th>
                <th>Customer</th>
                <th>Total</th>
                <th>Status</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
        @foreach($repairs as $repair)
            <tr>
                <td>{{ $repair->bill_no }}</td>
                <td>{{ $repair->model_no }}</td>
                <td>{{ $repair->customer }}</td>
                <td>{{ currency($repair->total, '') }}</td>
                <td>{{ $repair->status }}</td>
                <td>{{ $repair->created_at }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <h2>Credits</h2>
    <table>
        <thead>
            <tr>
                <th>Customer ID</th>
                <th>Amount</th>
                <th>Order ID</th>
                <th>Status</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
        @foreach($credits as $credit)
            <tr>
                <td>{{ $credit->customer_id }}</td>
                <td>{{ $credit->ammount }}</td>
                <td>{{ $credit->order_id }}</td>
                <td>{{ $credit->status }}</td>
                <td>{{ $credit->created_at }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

</body>
</html>
