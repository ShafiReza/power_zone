@extends('admin.layout.layout')

@section('content')
<div class="content-wrapper container-fluid">
    <h2>Bill Details</h2>

    {{-- <div class="bill-info">
        <p><strong>Bill Date:</strong> {{ $bill->bill_date }}</p>
        <p><strong>Final Amount:</strong> {{ $bill->final_amount }}</p>
    </div> --}}

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Category</th>
                <th>Quantity</th>
                <th>Discount</th>
                <th>Bill Date</th>
                <th>Unit Price</th>
                <th>Total Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td>{{ $item->description }}</td> <!-- Assuming description is where category is stored -->
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $item->discount }} ({{ $item->discount_type }})</td>
                    <td>{{ $item->bill_date }}</td>
                    <td>{{ $item->unit_price }}</td>
                    <td>{{ $item->total_amount }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
