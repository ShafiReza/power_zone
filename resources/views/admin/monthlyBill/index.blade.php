@extends('admin.layout.layout')

@section('content')
<div class="content-wrapper container-fluid">
    <h2>Monthly Bill List</h2>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <form action="{{ route('admin.monthlyBill.index') }}" method="GET">
        <div class="form-row">
            <div class="form-group col-2">
                <label for="month">Month:</label>
                <input type="month" class="form-control" id="month" name="month">
            </div>
            <div class="form-group col-2">
                <label for="customer_name">Customer Name:</label>
                <input type="text" class="form-control" id="customer_name" name="customer_name">
            </div>
            <div class="form-group col-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </div>
    </form>
    <a class="btn btn-success mb-3" href="{{ route('admin.monthlyBill.create') }}">Create Monthly Bill</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Customer Name</th>
                <th>Customer Address</th>
                <th>Amount</th>
                <th>Service</th>
                <th>Bill Month</th>
                <th>Start Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($bills as $bill)
                <tr>
                    <td>{{ $bill->id }}</td>
                    <td>{{ $bill->regularCustomer ? $bill->regularCustomer->name : 'N/A' }}</td>
                    <td>{{ $bill->customer_address }}</td>
                    <td>{{ $bill->amount }}</td>
                    <td>{{ ucfirst($bill->service) }}</td>
                    <td>{{ \Carbon\Carbon::parse($bill->bill_month)->format('F Y') }}</td>
                    <td>{{ $bill->start_date }}</td>
                    <td>
                        @if($bill->status == 'pending')
                            @if(\Carbon\Carbon::parse($bill->start_date)->diffInMonths(\Carbon\Carbon::now()) < 1)
                                <form action="{{ route('monthlyBill.toggleStatus', $bill->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">Mark as Paid</button>
                                </form>
                            @elseif(\Carbon\Carbon::parse($bill->start_date)->diffInMonths(\Carbon\Carbon::now()) >= 1)
                                <form action="{{ route('monthlyBill.toggleStatus', $bill->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-warning">Mark as Due</button>
                                </form>
                            @else
                                {{ ucfirst($bill->status) }}
                            @endif
                        @else
                            <form action="{{ route('monthlyBill.toggleStatus', $bill->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-secondary">{{ ucfirst($bill->status) }}</button>
                            </form>
                        @endif
                    </td>
                    <td>
                        <form action="{{ route('monthlyBill.destroy', $bill->id) }}" method="POST" class="delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-danger delete-button">Delete</button>
                        </form>
                    </td>
                    <td>
                        <a href="{{ route('admin.monthlyBill.showInvoice', $bill->id) }}" onclick="printInvoice(event, '{{ route('admin.monthlyBill.showInvoicePrint', $bill->id) }}')" class="btn btn-default"><i class="fas fa-print"></i> Print</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteButtons = document.querySelectorAll('.delete-button');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function () {
                const form = this.closest('.delete-form');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to delete this bill!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });

    function printInvoice(event, printUrl) {
    event.preventDefault();
    var newWindow = window.open(printUrl, '_blank');
    newWindow.onload = function() {
        newWindow.print();
        newWindow.onfocus = function() {
            newWindow.close();
        };
    };
}
</script>
@endsection
