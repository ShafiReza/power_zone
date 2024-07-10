@extends('admin.layout.layout')

@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Invoice</h1>
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
                            <!-- title row -->
                            <div class="row">
                                <h4 class="mx-auto">Invoice</h4>
                                <div class="col-12">
                                    <h4>
                                        <small class="float-right">Date: {{ \Carbon\Carbon::parse($bill->start_date)->format('d-m-Y') }}</small>
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
                                                <th>Service</th>
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
                                                <td>{{ ucfirst($bill->service) }}</td>
                                                <td>{{ \Carbon\Carbon::parse($bill->bill_month)->format('F Y') }}</td>
                                                <td>{{ $bill->amount }}</td>
                                            </tr>
                                            @if($previousDue > 0)
                                                @php
                                                    $serialNumber++;
                                                @endphp
                                                <tr>
                                                    <td>{{ $serialNumber }}</td>
                                                    <td>Previous Due</td>
                                                    <td>{{ \Carbon\Carbon::parse($bill->bill_month)->subMonth()->format('F Y') }}</td>
                                                    <td>{{ $previousDue }}</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.col -->
                            </div>

                            <!-- /.row -->
                        </div>
                        <!-- /.invoice -->
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
@endsection
