@extends('admin.layout.layout')
@section('content')
<div class="content-wrapper container-fluid col-11 table-responsive">
    <h2>Product List</h2>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif
    <form action="{{ route('admin.product.index') }}" method="GET">
        <div class="form-row">
            <div class="form-group col-2">
                <label for="name">Product Name:</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ request('name') }}">
            </div>
            <div class="form-group col-2">
                <label for="category">Category Name:</label>
                <input type="text" class="form-control" id="category" name="category" value="{{ request('category') }}">
            </div>
            <div class="form-group col-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </div>
    </form>

    <a class="btn btn-success mb-3" href="{{ route('admin.product.create') }}">Create Product</a>



    <table class="table table-hover">
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
                        <a class="btn btn-success " href="{{ route('admin.product.sales', ['id' => $product->id]) }}">Sales List</a>
                    <button type="button" class="btn btn-info add-product-btn" data-product-id="{{ $product->id }}">Add Product</button>
                        <a class="btn btn-warning " href="{{ route('admin.product.stockList', ['id' => $product->id]) }}">Stock List</a>
                    </td>

                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="addProductForm" method="POST" action="{{ route('admin.product.addProduct') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductModalLabel">Add Product</h5>

                </div>
                <div class="modal-body">
                    <input type="hidden" id="modal_product_id" name="product_id">
                    <div class="form-group">
                        <label for="entry_date">Entry Date</label>
                        <input type="date" class="form-control" id="entry_date" name="entry_date" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="quantity">Quantity</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" required>
                    </div>
                </div>
                <div class="modal-footer">

                    <button type="submit" class="btn btn-primary">Add Product</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
          // Handle Add Product button click
          document.querySelectorAll('.add-product-btn').forEach(button => {
            button.addEventListener('click', function () {
                const productId = this.getAttribute('data-product-id');
                document.getElementById('modal_product_id').value = productId;
                const addProductModal = new bootstrap.Modal(document.getElementById('addProductModal'));
                addProductModal.show();
            });
        });
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
