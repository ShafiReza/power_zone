@extends('admin.layout.layout')
@section('content')
<div class="content-wrapper container-fluid">
    <h2>Product List</h2>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <a class="btn btn-success mb-3" href="{{ route('admin.product.create') }}">Create Product</a>
    <a class="btn btn-success mb-3" href="{{ route('admin.product.sales') }}">Sales List</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Details</th>
                <th>Brand Name</th>
                <th>Category</th>
                <th>Origin</th>
                <th>Purchase Price</th>
                <th>Sell Price</th>
                <th>Wholesale Price</th>
                <th>Quantity</th>
                <th>Total Amount</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
                <tr class="{{ $product->status == 'active' ? '' : 'table-danger' }}">
                    <td>{{ $product->id }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->details }}</td>
                    <td>{{ $product->brand_name }}</td>
                    <td>{{ $product->category->name }}</td>
                    <td>{{ $product->origin }}</td>
                    <td>{{ $product->purchase_price }}</td>
                    <td>{{ $product->sell_price }}</td>
                    <td>{{ $product->wholesale_price }}</td>
                    <td>{{ $product->quantity }}</td>
                    <td>{{ $product->total_amount }}</td>
                    <td>
                        <form action="{{ route('admin.product.toggleStatus', $product->id) }}" method="POST">

                             @csrf
                             <button type="submit" class="btn btn-link {{ $product->status == 'active' ? 'text-success' : 'text-danger' }}">
                                 {{ ucfirst($product->status) }}
                             </button>
                         </form>
                     </td>
                    <td>
                        <a class="btn btn-primary" href="{{ route('admin.product.edit', $product->id) }}"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.product.destroy', $product->id) }}" method="POST" style="display:inline;" class="delete-form">
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
