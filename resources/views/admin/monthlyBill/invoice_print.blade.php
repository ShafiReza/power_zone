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

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <!-- Main content -->
                        <div class="invoice p-3 mb-3">
                            <h1 style="color: rgb(85, 199, 85)">Power Zone</h1>
                            <sub style="margin-left: 300px; color: rgb(85, 199, 85)"><i>The Source of power</i></sub>
                            <hr>
                            <!-- title row -->
                            <div class="row">
                                <h4 class="mx-auto">Invoice</h4>
                                <div class="col-12">
                                    <h4>
                                        <small class="float-right">Date:
                                            {{ \Carbon\Carbon::parse($bill->start_date)->format('d-m-Y') }}</small>
                                            Invoice No: {{ $bill->id  }}
                                    </h4>
                                </div>
                                <!-- /.col -->
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
                                <!-- /.col -->
                                <div class="col-sm-4 invoice-col">
                                    <address>
                                        <strong>{{ $customer->name }}</strong><br>
                                        {{ $customer->address }}<br>
                                    </address>
                                </div>
                                <!-- /.col -->
                            </div>
                            <!-- /.row -->

                            <!-- Table row -->
                            <div class="row">
                                <div class="col-12 table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Sl. No.</th> <!-- Add Sl. No. column -->
                                                <th>Description</th>
                                                {{-- <th>Service</th> --}}
                                                <th>Bill Month</th>
                                                <th>Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $serialNumber = 1;
                                            @endphp
                                            <tr>
                                                <td>{{ $serialNumber }}</td>
                                                <td>{{ $bill->description }}</td>
                                                {{-- <td>{{ ucfirst($bill->service) }}</td> --}}
                                                <td>{{ \Carbon\Carbon::parse($bill->bill_month)->format('F Y') }}</td>
                                                <td>{{ $bill->amount }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                @if ($previousBills->count() > 0)
                                <div class="col-6 table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Sl. No.</th>
                                                <th>Status</th>
                                                <th>Due Month</th>
                                                <th>Due Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $sl = 0;
                                            @endphp
                                            @foreach ($previousBills as $previousBill)
                                                @php
                                                    $sl++;
                                                @endphp
                                                <tr>
                                                    <td>{{ $sl }}</td>
                                                    <td>Previous Due</td>
                                                    <td>{{ \Carbon\Carbon::parse($previousBill->bill_month)->format('F Y') }}</td>
                                                    <td>
                                                        @if ($previousBill->status == 'partial')
                                                            {{ $previousBill->due_amount }}
                                                        @else
                                                            {{ $previousBill->amount }}
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @endif
                                <!-- /.col -->
                            </div>

                            <!-- /.row -->
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
                        <!-- /.invoice -->
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
@endsection

<style>
    .invoice {
        position: relative;
        padding: 2rem;
        background: none; /* No background color or gradient */
        border: none;
        height: 100vw;
        box-shadow: none;
        overflow: hidden;
        z-index: 1; /* Ensure content is above the background */
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
        opacity: 0.2; /* Adjust the opacity of the background image */
        z-index: -1; /* Ensure the background is behind the content */
    }

    @media print {
        .invoice {
            height: 135vw;
            padding: 1rem;
        }

        .invoice::before {
            opacity: 0.2; /* Adjust the opacity for print if needed */
            -webkit-print-color-adjust: exact; /* Ensures background image and color are printed */
            print-color-adjust: exact; /* For modern browsers */
        }

        .no-print {
            display: none; /* Hide print buttons during printing */
        }
    }
</style>
