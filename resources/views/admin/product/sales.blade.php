@extends('admin.layout.layout')

@section('content')
    <div class="content-wrapper container-fluid">
       <h2>Sales List</h2>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>SL No.</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Discount</th>
                    <th>Unit Price</th>
                    <th>Bill Date</th>
                    <th>Final Amount</th>
                </tr>
            </thead>
            <tbody>
                @php $sl = 1; @endphp
                @foreach ($bills as $bill)
                    @foreach ($bill->billItems as $item)
                        <tr>
                            <td rowspan="2">{{ $sl++ }}</td>
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
                    @foreach ($bill->billItems2 as $item)
                        <tr>
                            <td></td>
                            <td colspan="2">Additional Charges/Discounts</td>
                            <td>VAT:
                                @if($item->discount_type == 'Percentage')
                                    {{ intval($item->vat) }}%
                                @else
                                    {{ number_format($item->vat) }}
                                @endif
                            </td>
                            <td>Discount:
                                @if($item->discount_type == 'Percentage')
                                    {{ intval($item->discount) }}%
                                @else
                                    {{ number_format($item->discount) }}
                                @endif
                            </td>
                            <td>Final Amount: {{ $item->final_amount }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
