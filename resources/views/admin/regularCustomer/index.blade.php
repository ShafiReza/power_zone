@extends('admin.layout.layout')
@section('content')
<div class="content-wrapper container-fluid col-11 table-responsive">
    <h2>Regular Customer List</h2>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif
    <form action="{{ route('admin.regularCustomer.index') }}" method="GET">
        <div class="form-row">
            <div class="form-group col-2">
                <label for="name">Client Name:</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ request('name') }}">
            </div>
            <div class="form-group col-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </div>
    </form>

    <a class="btn btn-success mb-3" href="{{ route('admin.regularCustomer.create') }}">Create Customer</a>

    <table class="table table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Area</th>
                <th>City</th>
                <th>Note</th>
                <th>Initial Bill Amount</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($regularCustomers as $customer)
                <tr class="{{ $customer->status == 'Active' ? '' : 'table-danger' }}">
                    <td>{{ $customer->id }}</td>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->phone }}</td>
                    <td>{{ $customer->address }}</td>
                    <td>{{ $customer->area }}</td>
                    <td>{{ $customer->city }}</td>
                    <td>{{ $customer->note }}</td>
                    <td>{{ $customer->initial_bill_amount }}</td>

                    <td>
                        <form action="{{ route('admin.regularCustomer.toggleStatus', $customer->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-link {{ $customer->status == 'Active' ? 'text-success' : 'text-danger' }}">
                                {{ ucfirst($customer->status) }}
                            </button>
                        </form>
                    </td>
                    <td>
                        <a class="btn btn-primary" href="{{ route('admin.regularCustomer.edit', $customer->id) }}"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.regularCustomer.destroy', $customer->id) }}" method="POST" style="display:inline;" class="delete-form">
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
        // Attach click event listeners to delete buttons
        const deleteButtons = document.querySelectorAll('.delete-button');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function () {
                const form = this.closest('.delete-form');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to delete this customer!",
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
