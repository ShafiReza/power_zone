@extends('admin.layout.layout')

@section('content')
<div class="content-wrapper container-fluid">
    <h2>Edit Monthly Bill</h2>

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

    <form action="{{ route('monthlyBill.update', $bill->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Customer Name (Pre-selected and Disabled) -->
        {{-- <div class="form-group col-4">
            <label for="customer_id">Customer Name:</label>
            <select class="form-control" id="customer_id" name="customer_id" disabled>
                <option value="">Select Customer</option>
                @foreach ($customers as $customer)
                    <option value="{{ $customer->id }}" data-address="{{ $customer->address }}"
                        {{ $bill->customer_id == $customer->id ? 'selected' : '' }}>
                        {{ $customer->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Customer Address (Automatically Filled) -->
        <div class="form-group col-4">
            <label for="customer_address">Customer Address:</label>
            <input type="text" class="form-control" id="customer_address" name="customer_address"
                   value="{{ $bill->customer_address }}" readonly>
        </div> --}}

        <!-- Other Fields -->
        <div class="form-group col-4">
            <label for="amount">Bill Amount:</label>
            <input type="number" class="form-control" id="amount" name="amount" value="{{ $bill->amount }}" required>
        </div>
        <div class="form-group col-4">
            <label for="description">Description:</label>
            <textarea class="form-control" id="description" name="description" rows="3">{{ $bill->description }}</textarea>
        </div>
        <div class="form-group col-4">
            <label for="service">Service:</label>
            <select class="form-control" id="service" name="service" required>
                <option value="lift" {{ $bill->service == 'lift' ? 'selected' : '' }}>Lift</option>
                <option value="generator" {{ $bill->service == 'generator' ? 'selected' : '' }}>Generator</option>
                <option value="lift and generator" {{ $bill->service == 'lift and generator' ? 'selected' : '' }}>Lift and Generator</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a class="btn btn-secondary" href="{{ route('admin.monthlyBill.index') }}">Back</a>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Automatically fill the customer address when the page loads, based on the selected customer
        var customerSelect = document.getElementById('customer_id');
        var addressInput = document.getElementById('customer_address');
        var selectedOption = customerSelect.options[customerSelect.selectedIndex];
        var address = selectedOption.getAttribute('data-address');
        addressInput.value = address;

        // Ensure the customer dropdown remains disabled during editing
        customerSelect.disabled = true;
    });
</script>
@endsection
