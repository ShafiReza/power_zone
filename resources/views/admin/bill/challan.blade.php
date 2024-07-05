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
                            <!-- title row -->
                            <div class="row">
                                <h4 class="mx-auto">Challan</h4>
                                <div class="col-12">
                                    <h4>
                                        <small
                                            class="float-right">Date:{{ \Carbon\Carbon::parse($bill->bill_date)->format('d-m-Y') }}</small>
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

                                <!-- /.col -->
                            </div>
                            <!-- /.row -->

                            <!-- Table row -->
                            <div class="row">
                                <div class="col-12 table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Product Name</th>
                                                <th>Description</th>
                                                <th>Quantity</th>


                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($products as $product)
                                                <tr>
                                                    <td>{{ $product->product_name }}</td>
                                                    <td>{{ $product->description }}</td>
                                                    <td>{{ $product->quantity }}</td>


                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.col -->
                            </div>
                            <!-- /.row -->

                            <div class="row">
                                <!-- accepted payments column -->

                                <!-- /.col -->
                                <div class="col-6">


                                    <div class="table-responsive">
                                        {{-- <table class="table">
                                            @foreach ($billItems2 as $item)
                                                <tr>
                                                    <th style="width:50%">Discount Type</th>
                                                    <td>{{ $item->discount_type }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Discount</th>
                                                    <td>{{ $item->discount }}</td>
                                                </tr>
                                                <tr>
                                                    <th>VAT</th>
                                                    <td>{{ $item->vat }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Final Amount</th>
                                                    <td></td>
                                                </tr>
                                            @endforeach
                                        </table> --}}
                                    </div>
                                </div>
                                <!-- /.col -->
                            </div>
                            <!-- /.row -->

                            <!-- this row will not appear when printing -->
                            <div class="row no-print">
                                <div class="col-12">
                                    <a href="#" onclick="printPage()" class="btn btn-default"><i class="fas fa-print"></i> Print</a>
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
