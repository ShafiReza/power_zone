@extends('admin.layout.layout')

@section('content')
<div class="content-wrapper container-fluid">
    <h2>Payment History</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Description</th>
                <th>Bill Amount</th>
                <th>Receivable Amount</th>
                <th>Due Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bill as $bills)
                <tr>
                    <td>{{ $bills->description }}</td>
                    <td>{{ $bills->bill_amount }}</td>
                    <td>{{ $bills->receiveable_amount }}</td>
                    <td>{{ $bills->due_amount }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No payment history available.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
