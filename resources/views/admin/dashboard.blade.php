@extends('admin.layout.layout')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Dashboard</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">

                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- Small boxes (Stat box) -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-info">
                            <div class="inner d-flex justify-content-between align-items-center">
                                <div>
                                    <h3>{{ $totalRegularCustomers }}</h3>
                                    <p>Total Regular Customers</p>
                                </div>
                                <div class="mb-5">
                                    <h6>{{ \Carbon\Carbon::now()->format('F Y') }}</h6>
                                </div>
                            </div>
                            <div class="icon">

                                <i class="ion ion-bag mt-4"></i>
                            </div>

                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-success">
                            <div class="inner d-flex justify-content-between align-items-center">
                                <div>
                                    <h3>{{ $totalIrregularCustomers }}</h3>

                                    <p>Total Irregular Customers</p>
                                </div>
                                <div class="mb-5">
                                    <h6>{{ \Carbon\Carbon::now()->format('F Y') }}</h6>
                                </div>

                            </div>

                            <div class="icon">
                                <i class="ion ion-stats-bars mt-4"></i>
                            </div>

                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <a href="{{ route('admin.monthlyBill.index', ['status' => 'due']) }}" class="small-box bg-danger">
                            <div class="inner d-flex justify-content-between align-items-center">
                                <div>
                                    <h3>{{ $totalDueAmount }}</h3>
                                    <p>Total Monthly Bill Due</p>
                                </div>
                                <div class="mb-5">
                                    <h6>{{ \Carbon\Carbon::now()->format('F Y') }}</h6>
                                </div>

                            </div>

                            <div class="icon">

                                <i class="ion ion-person-add mt-4"></i>
                            </div>

                        </a>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <a href="{{ route('admin.monthlyBill.index', ['status' => 'paid']) }}" class="small-box bg-warning">
                            <div class="inner d-flex justify-content-between align-items-center">
                                <div>
                                    <h3>{{ $totalPaidAmount }}</h3>
                                    <p>Total Monthly Bill Paid</p>
                                </div>
                                <div class="mb-5">
                                    <h6>{{ \Carbon\Carbon::now()->format('F Y') }}</h6>
                                </div>

                            </div>
                            <div class="icon">
                                <i class="ion ion-pie-graph mt-4"></i>
                            </div>
                        </a>
                    </div>

                    <!-- ./col -->
                </div>
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <a href="{{ route('admin.bill.index') }}" class="small-box bg-info">
                            <div class="inner d-flex justify-content-between align-items-center">
                                <div>
                                    <h3>{{ $totalPaid }}</h3>
                                    <p>Total Extra Bill Paid</p>
                                </div>
                                <div class="mb-5">
                                    <h6>{{ \Carbon\Carbon::now()->format('F Y') }}</h6>
                                </div>
                            </div>
                            <div class="icon">
                                <i class="ion ion-bag mt-4"></i>
                            </div>
                        </a>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-7">
                        <!-- small box -->
                        <a href="{{ route('admin.bill.index') }}" class="small-box bg-success">
                            <div class="inner d-flex justify-content-between align-items-center">
                                <div>
                                    <h3>{{ $totalDue }}</h3>
                                    <p>Total Extra Bill Due</p>
                                </div>
                                <div class="mb-5">
                                    <h6>{{ \Carbon\Carbon::now()->format('F Y') }}</h6>
                                </div>
                            </div>
                            <div class="icon">
                                <i class="ion ion-stats-bars mt-4"></i>
                            </div>
                        </a>
                    </div>
                    <!-- ./col -->

                    <!-- ./col -->

                    <!-- ./col -->
                </div>


            </div>
        </section>

    </div>
@endsection
