@extends('admin.layout.layout')
@section('content')
<div class="content-wrapper container-fluid col-11 table-responsive">
    <h2>Supplier List</h2>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif
    <form action="{{ route('admin.supplier.index') }}" method="GET">
        <div class="form-row">
            <div class="form-group col-2">
                <label for="name">Supplier Name:</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ request('name') }}">
            </div>
            <div class="form-group col-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </div>
    </form>


    <a class="btn btn-success mb-3" href="{{ route('admin.supplier.create') }}">Create Supplier</a>

    <table class="table table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Mobile</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($suppliers as $supplier)
                <tr class="{{ $supplier->status == 'active' ? '' : 'table-danger' }}">
                    <td>{{ $supplier->id }}</td>
                    <td>{{ $supplier->name }}</td>
                    <td>{{ $supplier->mobile }}</td>
                    <td>
                        <form action="{{ route('admin.supplier.toggleStatus', $supplier->id) }}" method="POST">

                             @csrf
                             <button type="submit" class="btn btn-link {{ $supplier->status == 'active' ? 'text-success' : 'text-danger' }}">
                                 {{ ucfirst($supplier->status) }}
                             </button>
                         </form>
                     </td>
                    <td>
                        <a class="btn btn-primary" href="{{ route('admin.supplier.edit', $supplier->id) }}"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.supplier.destroy', $supplier->id) }}" method="POST" style="display:inline;" class="delete-form">
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
