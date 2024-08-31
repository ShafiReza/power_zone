@extends('admin.layout.layout')
@section('content')
    <div class="content-wrapper container-fluid">
        <h2>Create Non-Inventory Item</h2>

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

        <form action="{{ route('admin.nonInventory.store') }}" method="POST">
            @csrf
            <div class="form-group col-4">
                <label for="name">Name:</label>
                <input type="text" class="form-control" name="name" id="name">
            </div>
            <div class="form-group col-4">
                <label for="details">Details:</label>
                <textarea name="details" class="form-control" id="details"></textarea>
            </div>
            <div class="form-group col-4">
                <label for="brand_name">Brand Name:</label>
                <input type="text" class="form-control" name="brand_name" id="brand_name">
            </div>
            <div class="form-group col-4">
                <label for="origin">Origin:</label>
                <input type="text" class="form-control" name="origin" id="origin">
            </div>
            <div class="form-group col-4">
                <label for="purchase_price">Purchase Price:</label>
                <input type="number" class="form-control" name="purchase_price" id="purchase_price">
            </div>
            <div class="form-group col-4">
                <label for="sell_price">Sell Price:</label>
                <input type="number" class="form-control" name="sell_price" id="sell_price" >
            </div>
            <div class="form-group col-4">
                <label for="wholesale_price">Wholesale Price:</label>
                <input type="number" class="form-control" name="wholesale_price" id="wholesale_price">
            </div>
            <div class="form-group col-4">
                <label for="quantity">Quantity:</label>
                <input type="number" class="form-control" name="quantity" id="quantity">
            </div>
            <div class="form-group col-4">
                <label for="status">Status:</label>
                <input type="text" class="form-control" name="status" id="status" value="active">
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>


@endsection
