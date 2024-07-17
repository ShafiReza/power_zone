@extends('admin.layout.layout')

@section('content')
    <div class="content-wrapper container-fluid">
        <h2>Bill Details</h2>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Discount</th>
                    <th>Unit Price</th>
                    <th>Bill Date</th>
                    <th>Final Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($billItems as $item)
                    <tr>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>
                            @if($item->discount_type == 'Percentage')
                                {{ intval($item->discount) }}%
                            @else
                                {{ number_format($item->discount) }}
                            @endif
                        </td>
                        <td>{{ $item->unit_price }}</td>
                        <td>{{ $bill->bill_date }}</td>
                        <td>{{ $item->total_amount }}</td>
                    </tr>
                @endforeach
                @foreach ($billItem2 as $item)
                    <tr>
                        <td colspan="3">Additional Charges/Discounts</td>
                        <td>VAT: {{ $item->vat }}</td>
                        <td>Discount: {{ $item->discount }}</td>
                        <td>Final Amount: {{ $item->final_amount }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
