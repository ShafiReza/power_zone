@extends('admin.layout.layout')
@section('content')
<div class="content-wrapper container-fluid col-11 table-responsive">
    <h2>Non-Inventory List</h2>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif
    <form method="GET" action="{{ route('admin.nonInventory.index') }}" class="mb-3">
        <div class="input-group col-2">
            <input type="text" name="name" class="form-control" placeholder="Filter by Name" value="{{ request('name') }}">
            <div class="input-group-append">
                <button class="btn btn-primary" type="submit">Filter</button>
            </div>
        </div>
    </form>

    <a class="btn btn-success mb-3" href="{{ route('admin.nonInventory.create') }}">Add New Non-Inventory Item</a>
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Name</th>
                <th>Details</th>
                <th>Brand Name</th>
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
            @foreach($nonInventories as $item)
                <tr class="{{ $item->status == 'active' ? '' : 'table-danger' }}">
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->details }}</td>
                    <td>{{ $item->brand_name }}</td>
                    <td>{{ $item->origin }}</td>
                    <td>{{ $item->purchase_price }}</td>
                    <td>{{ $item->sell_price }}</td>
                    <td>{{ $item->wholesale_price }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $item->total_amount }}</td>
                   
                    <td>
                        <form action="{{ route('admin.nonInventory.toggleStatus', $item->id) }}" method="POST">

                             @csrf
                             <button type="submit" class="btn btn-link {{ $item->status == 'active' ? 'text-success' : 'text-danger' }}">
                                 {{ ucfirst($item->status) }}
                             </button>
                         </form>
                     </td>
                     <td>
                        <a class="btn btn-primary" href="{{ route('admin.nonInventory.edit', $item->id) }}"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.nonInventory.destroy', $item->id) }}" method="POST" style="display:inline;" class="delete-form">
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
@endsection
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
                    text: "You want to delete this Product!",
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
