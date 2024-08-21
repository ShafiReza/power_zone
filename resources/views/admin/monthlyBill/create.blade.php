@extends('admin.layout.layout')

@section('content')
<div class="content-wrapper container-fluid">
    <h2>Create Monthly Bill</h2>

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

    <form action="{{ route('monthlyBill.store') }}" method="POST">
        @csrf
        <div class="form-group col-4">
            <label for="customer_id">Customer Name:</label>
            <select class="form-control" id="customer_id" name="customer_id" required>
                <option value="">Select Customer</option>
                @foreach ($customers as $customer)
                    <option value="{{ $customer->id }}" data-address="{{ $customer->address }}">{{ $customer->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-4">
            <label for="customer_address">Customer Address:</label>
            <input type="text" class="form-control" id="customer_address" name="customer_address" readonly>
        </div>
        <div class="form-group col-4">
            <label for="amount">Bill Amount:</label>
            <input type="number" class="form-control" id="amount" name="amount" required>
        </div>
        <div class="form-group col-4">
            <label for="description">Description:</label>
            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
        </div>
        <div class="form-group col-4">
            <label for="service">Service:</label>
            <select class="form-control" id="service" name="service" required>
                <option value="lift">Lift</option>
                <option value="generator">Generator</option>
                <option value="lift and generator">Lift and Generator</option>
            </select>
        </div>
        <div class="form-group col-4">
            <label for="bill_month">Bill Month (Name with Year):</label>
            <input type="date" class="form-control" id="bill_month" name="bill_month" required>
        </div>
        <div class="form-group col-4">
            <label for="start_date">Start Date:</label>
            <input type="date" class="form-control" id="start_date" name="start_date" required>
        </div>
        {{-- <div class="form-group col-4">
            <label for="status">Status:</label>
            <select class="form-control" id="status" name="status" required>
                <option value="pending">Pending</option>
                <option value="paid">Paid</option>
                <option value="due">Due</option>
            </select>
        </div> --}}
        <button type="submit" class="btn btn-primary">Submit</button>
        <a class="btn btn-secondary" href="{{ route('admin.monthlyBill.index') }}">Back</a>
    </form>
</div>

<script>
    document.getElementById('customer_id').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        var address = selectedOption.getAttribute('data-address');
        document.getElementById('customer_address').value = address;
    });
</script>
<script>
    document.getElementById('start_date').addEventListener('change', function() {
        const startDate = new Date(this.value);
        const nextGenerationDate = new Date(startDate.getFullYear(), startDate.getMonth() + 1, startDate.getDate());
        document.getElementById('next_generation_date').value = nextGenerationDate.toISOString().slice(0, 10);
    });
</script>
@endsection
