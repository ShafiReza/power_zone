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
              <div class="inner">
                <h3>{{ $totalRegularCustomers }}</h3>

                <p>Total Regular Customers</p>
              </div>
              <div class="icon">
                <i class="ion ion-bag"></i>
              </div>

            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                <h3>{{ $totalIrregularCustomers }}</h3>

                <p>Total Irregular Customers</p>
              </div>
              <div class="icon">
                <i class="ion ion-stats-bars"></i>
              </div>

            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                <h3>{{ $totalDueAmount }}</h3>

                <p>Total Monthly Bill Due</p>
              </div>
              <div class="icon">
                <i class="ion ion-person-add"></i>
              </div>

            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
                <h3>{{ $totalPaidAmount }}</h3>

                <p>Total Monthly Bill Paid</p>
              </div>
              <div class="icon">
                <i class="ion ion-pie-graph"></i>
              </div>

            </div>
          </div>
          <!-- ./col -->
        </div>
        <div class="row">
            <div class="col-lg-3 col-6">
              <!-- small box -->
              <div class="small-box bg-info">
                <div class="inner">
                  <h3>{{ $totalPaid }}</h3>

                  <p>Total Extra Bill Paid</p>
                </div>
                <div class="icon">
                  <i class="ion ion-bag"></i>
                </div>

              </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
              <!-- small box -->
              <div class="small-box bg-success">
                <div class="inner">
                  <h3>{{ $totalDue }}</sup></h3>

                  <p>Total Extra Bill Due</p>
                </div>
                <div class="icon">
                  <i class="ion ion-stats-bars"></i>
                </div>

              </div>
            </div>
            <!-- ./col -->

            <!-- ./col -->

            <!-- ./col -->
          </div>


      </div>
    </section>

  </div>
  @endsection
