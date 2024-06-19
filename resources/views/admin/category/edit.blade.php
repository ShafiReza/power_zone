@extends('admin.layout.layout')
@section('content')
<div class="content-wrapper container-fluid">
    <h2>Edit Category</h2>

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

    <form action="{{ route('admin.category.update', $category->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group col-4">
            <label for="name">Name:</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $category->name }}" required>
        </div>
        <div class="form-group col-4">
            <label for="details">Details:</label>
            <textarea class="form-control" id="details" name="details" rows="4">{{ $category->details }}</textarea>
        </div>
        <div class="form-group col-4">
            <label for="status">Status:</label>
            <select class="form-control" id="status" name="status" required>
                <option value="active" {{ $category->status == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ $category->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a class="btn btn-secondary" href="{{ route('admin.category.index') }}">Back</a>
    </form>
</div>
@endsection
