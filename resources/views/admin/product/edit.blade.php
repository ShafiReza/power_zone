@extends('admin.layout.layout')
@section('content')
<div class="content-wrapper container-fluid">
    <h2>Edit Product</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.product.update', $product->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group col-4">
            <label for="name">Name:</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $product->name }}" required>
        </div>
        <div class="form-group col-4">
            <label for="details">Details:</label>
            <textarea class="form-control" id="details" name="details" rows="4">{{ $product->details }}</textarea>
        </div>
        <div class="form-group col-4">
            <label for="brand_name">Brand Name:</label>
            <input type="text" class="form-control" id="brand_name" name="brand_name" value="{{ $product->brand_name }}" required>
        </div>
        <div class="form-group col-4">
            <label for="category_id">Category:</label>
            <select class="form-control" id="category_id" name="category_id" required>
                @foreach ($categories as $category)
                    @if ($category->status == 'active')
                        <option value="{{ $category->id }}" {{ $category->id == $product->category_id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endif
                @endforeach
            </select>
        </div>
        <div class="form-group col-4">
            <label for="origin">Origin:</label>
            <input type="text" class="form-control" id="origin" name="origin" value="{{ $product->origin }}" required>
        </div>
        <div class="form-group col-4">
            <label for="purchase_price">Purchase Price:</label>
            <input type="number" step="0.01" class="form-control" id="purchase_price" name="purchase_price" value="{{ $product->purchase_price }}" required>
        </div>
        <div class="form-group col-4">
            <label for="sell_price">Sell Price:</label>
            <input type="number" step="0.01" class="form-control" id="sell_price" name="sell_price" value="{{ $product->sell_price }}" required>
        </div>
        <div class="form-group col-4">
            <label for="wholesale_price">Wholesale Price:</label>
            <input type="number" step="0.01" class="form-control" id="wholesale_price" name="wholesale_price" value="{{ $product->wholesale_price }}" required>
        </div>
        <div class="form-group col-4">
            <label for="quantity">Quantity:</label>
            <input type="number" class="form-control" id="quantity" name="quantity" value="{{ $product->quantity }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a class="btn btn-secondary" href="{{ route('admin.product.index') }}">Back</a>
    </form>
</div>
@endsection
