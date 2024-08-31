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
    <form action="{{ route('admin.nonInventory.update', $nonInventory->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group col-4">
            <label for="name">Name:</label>
            <input type="text" name="name" class="form-control" id="name" value="{{ $nonInventory->name }}" required>
        </div>
        <div class="form-group col-4">
            <label for="details">Details:</label>
            <textarea name="details" class="form-control" id="details">{{ $nonInventory->details }}</textarea>
        </div>
        <div class="form-group col-4">
            <label for="brand_name">Brand Name:</label>
            <input type="text" name="brand_name" class="form-control" id="brand_name" value="{{ $nonInventory->brand_name }}">
        </div>
        <div class="form-group col-4">
            <label for="origin">Origin:</label>
            <input type="text" name="origin" class="form-control" id="origin" value="{{ $nonInventory->origin }}">
        </div>
        <div class="form-group col-4">
            <label for="purchase_price">Purchase Price:</label>
            <input type="number" name="purchase_price" class="form-control" id="purchase_price" value="{{ $nonInventory->purchase_price }}" required>
        </div>
        <div class="form-group col-4">
            <label for="sell_price">Sell Price:</label>
            <input type="number" name="sell_price" class="form-control" id="sell_price" value="{{ $nonInventory->sell_price }}" required>
        </div>
        <div class="form-group col-4">
            <label for="wholesale_price">Wholesale Price:</label>
            <input type="number" name="wholesale_price" class="form-control" id="wholesale_price" value="{{ $nonInventory->wholesale_price }}">
        </div>
        <div class="form-group col-4">
            <label for="quantity">Quantity:</label>
            <input type="number" name="quantity" class="form-control" id="quantity" value="{{ $nonInventory->quantity }}" required>
        </div>
        <div class="form-group col-4">
            <label for="status">Status:</label>
            <input type="text" name="status" class="form-control" id="status" value="{{ $nonInventory->status }}">
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a class="btn btn-secondary" href="{{ route('admin.nonInventory.index') }}">Back</a>
    </form>
</div>
@endsection
