@extends('admin.layout.layout')
@section('content')
<div class="content-wrapper container-fluid">
    <h2>Edit Irregular Customer</h2>

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

    <form action="{{ route('admin.irregularCustomer.update', $customer->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group col-4">
            <label for="name">Name:</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $customer->name }}" required>
        </div>
        <div class="form-group col-4">
            <label for="phone">Phone:</label>
            <input type="text" class="form-control" id="phone" name="phone" value="{{ $customer->phone }}" required>
        </div>
        <div class="form-group col-4">
            <label for="address">Address:</label>
            <input type="text" class="form-control" id="address" name="address" value="{{ $customer->address }}" required>
        </div>
        <div class="form-group col-4">
            <label for="area">Area:</label>
            <input type="text" class="form-control" id="area" name="area" value="{{ $customer->area }}" required>
        </div>
        <div class="form-group col-4">
            <label for="city">City:</label>
            <input type="text" class="form-control" id="city" name="city" value="{{ $customer->city }}" required>
        </div>
        <div class="form-group col-4">
            <label for="note">Note:</label>
            <textarea class="form-control" id="note" name="note" rows="4" cols="4">{{ $customer->note }}</textarea>
        </div>
        <div class="form-group col-4">
            <label for="status">Status:</label>
            <select class="form-control" id="status" name="status" required>
                <option value="active" {{ $customer->status == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ $customer->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a class="btn btn-secondary" href="{{ route('admin.irregularCustomer.index') }}">Back</a>
    </form>
</div>
@endsection
