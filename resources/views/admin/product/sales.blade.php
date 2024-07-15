@extends('admin.layout.layout')

@section('content')
    <div class="content-wrapper container-fluid">
        <h2>Bill Details</h2>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Discount</th>
                    <th>Unit Price</th>
                    <th>Total Amount</th>
                    <th>Bill Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($billItems as $item)
                    <tr>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->description }}</td> <!-- Assuming description is where category is stored -->
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->discount }} ({{ $item->discount_type }})</td>
                        <td>{{ $item->unit_price }}</td>
                        <td>{{ $item->total_amount }}</td>
                        <td>{{ $bill->bill_date }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
