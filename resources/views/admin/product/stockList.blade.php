@extends('admin.layout.layout')
@section('content')
<div class="content-wrapper container-fluid">
    <h2>Stock List for {{ $product->name }}</h2>

    <a class="btn btn-secondary mb-3" href="{{ route('admin.product.index') }}">Back to Product List</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Entry Date</th>
                <th>Description</th>
                <th>Quantity</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($stockEntries as $entry)
                <tr>
                    <td>{{ $entry->entry_date }}</td>
                    <td>{{ $entry->description }}</td>
                    <td>{{ $entry->quantity }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
