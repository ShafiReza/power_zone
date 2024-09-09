@extends('admin.layout.layout')

@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <!-- Main content -->
                        <div class="invoice p-3 mb-3">
                            <div class="header-with-images">
                                <div class="text-container">
                                    <h1 style="color: rgb(85, 199, 85); font-size: 80px;">Power Zone</h1>
                                    <sub style="margin-left:500px;color: rgb(85, 199, 85); font-size: 20px;"><i>The Source
                                            of Power</i></sub>
                                </div>
                                <div class="images-container">
                                    <img src="{{ asset('admin/images/pic2.PNG') }}" alt="Image 1">
                                    <img src="{{ asset('admin/images/pic1.PNG') }}" alt="Image 2">
                                </div>
                            </div>
                            <hr>
                            <!-- title row -->
                            <div class="row">
                                <h4 class="mx-auto">Invoice</h4>
                                <div class="col-12">
                                    <h4>
                                        <small class="float-right">Date:
                                            {{ \Carbon\Carbon::parse($bill->bill_date)->format('d-m-Y') }}<br>
                                            <!-- Adding the Invoice Number below the Date -->
                                        </small>
                                        Invoice No: {{ $bill->id }}
                                    </h4>
                                </div>
                            </div>
                            <!-- info row -->
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
                                        <strong>{{ $customer->name }}</strong><br>
                                        {{ $customer->address }}<br>
                                    </address>
                                </div>
                            </div>
                            <!-- Table row -->
                            <div class="row">
                                <div class="col-12 table-responsive">
                                    <table class="table table-striped">
                                        @php
                                            $hasDescription = $products->contains(
                                                fn($product) => !empty($product->description),
                                            );
                                        @endphp
                                        <thead>
                                            <tr>
                                                <th>Sl No</th>
                                                <th>Product Name</th>
                                                @if ($hasDescription)
                                                    <th>Description</th>
                                                @endif
                                                <th>Brand Name</th>
                                                <th>Origin</th>
                                                <th>Quantity</th>
                                                <th>Unit Price</th>
                                                <th>Discount</th>
                                                <th>Total Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($products as $product)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $product->product_name }}</td>

                                                    @if ($hasDescription)
                                                        <td>
                                                            {{ !empty($product->description) ? $product->description : 'None' }}
                                                        </td>
                                                    @endif

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

                            <div class="row">
                                @if ($previousBills->count() > 0)
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
                                                @foreach ($previousBills as $previousBill)
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
                                        <table class="table">
                                            @foreach ($billItems2 as $item)
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

                            <div class="row no-print">
                                <div class="col-12">
                                    <a href="#" onclick="printPage()" class="btn btn-default"><i
                                            class="fas fa-print"></i> Print</a>
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
</style>
