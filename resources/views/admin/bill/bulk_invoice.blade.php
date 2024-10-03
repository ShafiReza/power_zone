@extends('admin.layout.layout')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6"></div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <!-- Loop through each selected bill and display its invoice -->
                        @foreach ($bills as $bill)
                            <div class="invoice p-3 mb-3">
                                <!-- Invoice header with images -->
                                <div class="header-with-images">
                                    <div class="text-container">
                                        <h1 style="color: rgb(85, 199, 85); font-size: 80px;">Power Zone</h1>
                                        <sub style="margin-left:500px;color: rgb(85, 199, 85); font-size: 20px;">
                                            <i>The Source of Power</i>
                                        </sub>
                                    </div>
                                    <div class="images-container">
                                        <img src="{{ asset('admin/images/pic2.PNG') }}" alt="Image 1">
                                        <img src="{{ asset('admin/images/pic1.PNG') }}" alt="Image 2">
                                    </div>
                                </div>
                                <hr>

                                <!-- Invoice details -->
                                <div class="row">
                                    <h4 class="mx-auto">Invoice</h4>
                                    <div class="col-12">
                                        <h4>
                                            <small class="float-right">Date:
                                                {{ \Carbon\Carbon::parse($bill->bill_date)->format('d-m-Y') }}<br>
                                            </small>
                                            Invoice No: {{ $bill->id }}
                                        </h4>
                                    </div>
                                </div>

                                <!-- Info row -->
                                <div class="row invoice-info">
                                    <div class="col-sm-4 invoice-col">
                                        <address>
                                            <strong>Power Zone</strong><br>
                                            Bosila Housing,<br>
                                            Mohammadpur, Dhaka-1207, Bangladesh<br>
                                            Phone: 01722-533538, 01918-750912<br>
                                        </address>
                                    </div>
                                    <div class="col-sm-4 invoice-col" style="margin-left: 300px;">
                                        <address>
                                            <strong>{{ $bill->customer->name }}</strong><br>
                                            {{$bill->customer->address}}<br>
                                        </address>
                                    </div>
                                </div>

                                <!-- Product Table -->
                                <div class="row">
                                    <div class="col-12 table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Sl No</th>
                                                    <th>Product Name</th>
                                                    <th>Description</th>
                                                    <th>Brand Name</th>
                                                    <th>Origin</th>
                                                    <th>Quantity</th>
                                                    <th>Unit Price</th>
                                                    <th>Discount</th>
                                                    <th>Total Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($bill->products as $product)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $product->product_name }}</td>
                                                        <td>{{ $product->description ?? 'None' }}</td>
                                                        <td>{{ $product->brand_name }}</td>
                                                        <td>{{ $product->origin }}</td>
                                                        <td>{{ $product->quantity }}</td>
                                                        <td>{{ $product->unit_price }}</td>
                                                        <td>
                                                            @if ($product->discount_type == 'Percentage')
                                                                {{ intval($product->discount) }}%
                                                            @else
                                                                {{ number_format($product->discount) }}
                                                            @endif
                                                        </td>
                                                        <td>{{ $product->total_amount }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Totals -->
                                <div class="row">
                                    @if ($bill->previousBills->count() > 0)
                                    <div class="col-6">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Bill ID</th>
                                                    <th>Bill Date</th>
                                                    <th>Paid Amount</th>
                                                    <th>Due Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($bill->previousBills as $previousBill)
                                                    <tr>
                                                        <td>{{ $previousBill->id }}</td>
                                                        <td>{{ $previousBill->receive_date }}</td>
                                                        <td>{{ $previousBill->receivable_amount }}</td>
                                                        <td>{{ $previousBill->due_amount }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                                    <div class="col-6 ml-auto">
                                        <div class="table-responsive">
                                            @php
                                                $totalAmountSum = $bill->products->sum('total_amount');
                                            @endphp
                                            <table class="table">
                                                <tr>
                                                    <th>Total Amount</th>
                                                    <td>{{ number_format($totalAmountSum, 2) }}</td>
                                                </tr>
                                                @foreach ($bill->billItems2 as $item)
                                                <tr>
                                                    <th>Discount</th>
                                                    <td>
                                                        @if ($item->discount_type == 'Percentage')
                                                            {{ intval($item->discount) }}%
                                                        @else
                                                            {{ number_format($item->discount) }}
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>VAT</th>
                                                    <td>
                                                        @if ($item->discount_type == 'Percentage')
                                                            {{ intval($item->vat) }}%
                                                        @else
                                                            {{ number_format($item->vat) }}
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Final Amount</th>
                                                    <td>{{ $item->final_amount }}</td>
                                                </tr>
                                            @endforeach
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Signature -->
                                <div class="row signature-row">
                                    <div class="col-7 ml-5">
                                        <p>__________________________</p>
                                        <p><strong>Client Signature</strong></p>
                                    </div>
                                    <div class="col-4 text-right ml-1">
                                        <img src="{{ asset('admin/images/img001.jpg') }}" alt="Authorized Signature"
                                            style="max-width: 100px; margin-left: 150px;">
                                        <p>__________________________</p>
                                        <p><strong>Authorized Signature</strong></p>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <!-- Print button -->
                        <div class="row no-print">
                            <div class="col-12">
                                <a href="#" onclick="printPage()" class="btn btn-default">
                                    <i class="fas fa-print"></i> Print
                                </a>
                            </div>
                        </div>

                        <script>
                            function printPage() {
                                setTimeout(function() {
                                    window.print();
                                }, 500);
                            }
                        </script>

                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
<style>
    .invoice {
        position: relative;
        padding: 2rem;
        background: none;
        /* No background color or gradient */
        border: none;
        height: 100vw;
        box-shadow: none;
        overflow: hidden;
        z-index: 1;
        /* Ensure content is above the background */
    }

    .invoice::before {
        content: "";
        position: absolute;
        top: 130;
        left: 0;
        width: 100%;
        height: 90vw;
        background: none;
        border: none;
        box-shadow: none;
        background-image: url("{{ asset('admin/images/Capture.jpg') }}");
        background-repeat: no-repeat;
        background-position: center center;
        background-size: cover;
        opacity: 0.2;
        /* Adjust the opacity of the background image */
        z-index: -1;
        /* Ensure the background is behind the content */
    }

    .header-with-images {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .text-container {
        flex-grow: 1;
    }

    .images-container {
        display: flex;
        gap: 10px;
    }

    .images-container img {
        width: 100px;
        height: auto;
    }

    @media print {
        .invoice {
            height: 135vw;
            padding: 1rem;
        }

        .invoice::before {
            opacity: 0.2;
            /* Adjust the opacity for print if needed */
            -webkit-print-color-adjust: exact;
            /* Ensures background image and color are printed */
            print-color-adjust: exact;
            /* For modern browsers */
        }

        .no-print {
            display: none;
            /* Hide print buttons during printing */
        }
    }
     .signature-row {
        margin-top: 80px;
    }

    .signature-row .col-7 {
        display: flex;
        flex-direction: column;
        align-items: justify;
        justify-content: justify;
        height: 100px;
        margin-left: 60px;
        /* Add margin-left to move client signature to the right */
    }

    .signature-row .col-4 {
        display: flex;
        flex-direction: column;
        align-items: justify;
        justify-content: justify;
        height: 100px;
        margin-left: 150px;
        /* Adjust margin-left for more space */
    }

    .signature-row .text-right {
        text-align: right;
    }
</style>
