@extends('admin.layout.layout')

@section('content')
<div class="content-wrapper container-fluid">
    <h2>Payment History</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Receive Date</th>
                <th>Bill Amount</th>
                <th>Receivable Amount</th>
                <th>Due Amount</th>
                <th>Description</th>
                <th>Date</th>
                {{-- <th>Action</th> --}}
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $payment)
            <tr>
                <td>{{ $payment->id }}</td>
                <td>{{ $payment->receive_date }}</td>
                @php
                    $amount=$payment->receivable_amount + $payment->due_amount ;
                @endphp
                <td>{{ $amount }}</td>
                <td>{{ $payment->receivable_amount }}</td>
                <td>{{ $payment->due_amount }}</td>
                <td>{{ $payment->description }}</td>
                <td>{{ $payment->created_at }}</td>
                {{-- <td>
                    <button class="btn btn-danger delete-button" data-id="{{ $payment->id }}">Delete</button>
                    <form action="{{ route('payment.delete', $payment->id) }}" method="POST" class="delete-form" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </td> --}}
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
{{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Attach click event listeners to delete buttons
    const deleteButtons = document.querySelectorAll('.delete-button');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const form = this.closest('tr').querySelector('.delete-form');
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to delete this payment record!",
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
</script> --}}
@endsection
