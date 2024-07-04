@extends('admin.layout.layout')
@section('content')
<div class="content-wrapper container-fluid">

    <h2>Create Regular Customer</h2>

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

    <form action="{{ route('admin.regularCustomer.store') }}" method="POST">
        @csrf
        <div class="form-group col-4">
            <label for="name">Name:</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="form-group col-4">
            <label for="phone">Phone:</label>
            <input type="text" class="form-control" id="phone" name="phone" required>
        </div>
        <div class="form-group col-4">
            <label for="address">Address:</label>
            <input type="text" class="form-control" id="address" name="address" required>
        </div>
        <div class="form-group col-4">
            <label for="area">Area:</label>
            <input type="text" class="form-control" id="area" name="area" required>
        </div>
        <div class="form-group col-4">
            <label for="city">City:</label>
            <input type="text" class="form-control" id="city" name="city" required>
        </div>
        <div class="form-group col-4">
            <label for="note">Note:</label>
            <textarea class="form-control" id="note" name="note" rows="4" cols="4"></textarea>
        </div>

        <div class="form-group col-4">
            <label for="initial_bill_amount">Initial Bill Amount:</label>
            <input type="number" step="0.01" class="form-control" id="initial_bill_amount" name="initial_bill_amount" required>
        </div>
        <div class="form-group col-4">
            <label for="status">Status:</label>
            <select class="form-control" id="status" name="status" required>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
            </select>
        </div>
        <div class="form-group col-4">
        <button type="submit" class="btn btn-primary">Submit</button>
        <a class="btn btn-secondary" href="{{ route('admin.regularCustomer.index') }}">Back</a>
    </div>
    </form>
</div>
@endsection
