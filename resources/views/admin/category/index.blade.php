@extends('admin.layout.layout')
@section('content')
<div class="content-wrapper container-fluid col-11 table-responsive">
    <h2>Category List</h2>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <a class="btn btn-success mb-3" href="{{ route('admin.category.create') }}">Create Category</a>

    <table class="table table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Details</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($categories as $category)
                <tr  class="{{ $category->status == 'active' ? '' : 'table-danger' }}">
                    <td>{{ $category->id }}</td>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->details }}</td>
                    <td>
                        <form action="{{ route('admin.category.toggleStatus', $category->id) }}" method="POST">

                             @csrf
                             <button type="submit" class="btn btn-link {{ $category->status == 'active' ? 'text-success' : 'text-danger' }}">
                                 {{ ucfirst($category->status) }}
                             </button>
                         </form>
                     </td>
                    <td>
                        <a class="btn btn-primary" href="{{ route('admin.category.edit', $category->id) }}"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.category.destroy', $category->id) }}" method="POST" style="display:inline;" class="delete-form">
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
