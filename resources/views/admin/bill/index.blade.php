
@extends('admin.layout.layout')

@section('content')
    <div class="content-wrapper container-fluid">
        <h2>Billing List</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer Name</th>
                    <th>Bill Date</th>
                    <th>Bill Type</th>
                    <th>Final Amount</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bills as $bill)
                    <tr>
                        <td>{{ $bill->id }}</td>
                        <td>{{ $bill->customer_name }}</td>
                        <td>{{ $bill->bill_date }}</td>
                        <td>{{ $bill->bill_type }}</td>
                        <td>{{ $bill->final_amount }}</td>
                        <td>
                            <a href="{{ route('admin.bill.quotation', $bill->id) }}" class="btn btn-primary btn-sm">Quotation</a>
                            <a href="{{ route('admin.bill.challan', $bill->id) }}" class="btn btn-info btn-sm">Challan</a>
                            <a href="{{ route('admin.bill.invoice', $bill->id) }}" class="btn btn-success btn-sm">Invoice</a>
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
