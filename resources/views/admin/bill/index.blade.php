
@extends('admin.layout.layout')

@section('content')
    <div class="content-wrapper container">
        <h2>Billing List</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Customer Name</th>
                    <th>Bill Date</th>
                    <th>Bill Type</th>
                    <th>Final Amount</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bills as $bill)
                    <tr>
                        <td>{{ $bill->customer_name }}</td>
                        <td>{{ $bill->bill_date }}</td>
                        <td>{{ $bill->bill_type }}</td>
                        <td>{{ $bill->final_amount }}</td>
                        <td>
                            <a href="{{ route('bill.edit', $bill->id) }}" class="btn btn-primary btn-sm">Edit</a>
                            <a href="{{ route('bill.invoice', $bill->id) }}" class="btn btn-success btn-sm">Invoice</a>
                            <a href="{{ route('bill.destroy', $bill->id) }}" class="btn btn-danger btn-sm">Delete</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
