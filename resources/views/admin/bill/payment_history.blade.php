@extends('admin.layout.layout')

@section('content')
<div class="content-wrapper container-fluid">
    <h2>Payment History</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Description</th>
                <th>Receive Date</th>
                <th>Bill Amount</th>
                <th>Paid Amount</th> <!-- This is the field we are fixing -->
                <th>Receivable Amount</th>
                <th>Due Amount</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $payment)
            <tr>
                <td>{{ $payment->id }}</td>
                <td>{{ $payment->description }}</td>
                <td>{{ $payment->receive_date }}</td>
                <td>{{ $finalAmount }}</td>
                <td>{{ $payment->paid_amount }}</td> <!-- Ensure this is correctly referenced -->
                <td>{{ $payment->receivable_amount }}</td>
                <td>{{ $payment->due_amount }}</td>
                <td>{{ $payment->created_at }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
