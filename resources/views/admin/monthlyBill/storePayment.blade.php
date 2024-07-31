@extends('admin.layout.layout')

@section('content')
<div class="content-wrapper container-fluid">
    <h2>Payment History</h2>
    @if ($bill)
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
                @forelse($payments as $payment)
                    <tr>
                        <td>{{ $payment->description }}</td>
                        <td>{{ $payment->bill->amount }}</td>
                        <td>{{ $payment->receiveable_amount }}</td>
                        <td>{{ $payment->due_amount }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">No payment history available.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @else
        <p>The requested bill does not exist.</p>
    @endif
</div>
@endsection
