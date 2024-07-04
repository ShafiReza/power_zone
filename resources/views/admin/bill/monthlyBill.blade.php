
@extends('admin.layout.layout')

@section('content')

<div class="content-wrapper container-fluid">
    <h2>Monthly Bill List</h2>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <form action="{{ route('admin.bill.monthlyBill') }}" method="GET">
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
        {{-- <button type="submit" class="btn btn-primary">Filter</button> --}}

    </form>

    {{-- <a class="btn btn-success mb-3" href="{{ route('admin.bills.create') }}">Create Bill</a> --}}

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Customer Name</th>
                <th>Amount</th>
                <th>Billing Month</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($bills as $bill)
                <tr>
                    <td>{{ $bill->id }}</td>
                    <td>{{ $bill->regularCustomer->name }}</td>
                    <td>{{ $bill->amount }}</td>
                    <td>{{ $bill->billing_month->format('F Y') }}</td>
                    <td>
                        <form action="{{ route('bill.updateStatus', $bill->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-link {{ $bill->status == 'pending' ? 'text-warning' : ($bill->status == 'paid' ? 'text-success' : 'text-danger') }}">
                                {{ ucfirst($bill->status) }}
                            </button>
                        </form>
                    </td>
                    <td>
                        <form action="{{ route('bill.destroy', $bill->id) }}" method="POST" style="display:inline;" class="delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-danger delete-button"><i class="fas fa-trash-alt"></i></button>
                        </form>
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
</script>


@endsection
