@extends('admin.layout.layout')

@section('content')
    <div class="content-wrapper container">
        <h2>Edit Billing</h2>
        <form id="billing-form">
            <div class="form-row">
                <div class="form-group col-4">
                    <label for="customerName">Customer Name</label>
                    <select id="customerName" class="form-control">
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ $bill->customer_id == $customer->id? 'elected' : '' }}>{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-4">
                    <label for="billType">Bill Type</label>
                    <select id="billType" class="form-control">
                        <option value="Day" {{ $bill->bill_type == 'Day'? 'elected' : '' }}>Day</option>
                        <option value="Month" {{ $bill->bill_type == 'Month'? 'elected' : '' }}>Month</option>
                    </select>
                </div>

                <div class="form-group col-4">
                    <label for="billDate">Date</label>
                    <input type="date" class="form-control" id="billDate" value="{{ $bill->bill_date }}">
                </div>
            </div>
            <div class="form-group col-4">
                <label for="services">Services</label>
                <select id="services" class="form-control">
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Description</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Discount</th>
                        <th>Total Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="bill-items">
                    @foreach($bill->billItems as $billItem)
                        <tr>
                            <td><input type="text" class="form-control" name="product_name[]" value="{{ $billItem->product_name }}"></td>
                            <td><textarea class="form-control" name="description[]">{{ $billItem->description }}</textarea></td>
                            <td><input type="number" class="form-control" name="quantity[]" value="{{ $billItem->quantity }}" onchange="calculateTotal(this)"></td>
                            <td><input type="number" class="form-control" name="unit_price[]" value="{{ $billItem->unit_price }}" onchange="calculateTotal(this)"></td>
                            <td><input type="number" class="form-control" name="discount[]" value="{{ $billItem->discount }}" onchange="calculateTotal(this)"></td>
                            <td><input type="number" class="form-control" name="total_amount[]" value="{{ $billItem->total_amount }}" readonly></td>
                            <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">Delete</button></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <button type="button" class="btn btn-primary mb-5" onclick="addRow()">Add Row</button>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Discount</th>
                        <th>VAT</th>
                        <th>Final Amount</th>
                    </tr>
                </thead>
                <tbody id="bill-items2">
                    @foreach($bill->billDiscounts as $billDiscount)
                        <tr>
                            <td><input type="number" class="form-control" name="discount_amount[]" value="{{ $billDiscount->discount_amount }}"></td>
                            <td><input type="number" class="form-control" name="vat[]" value="{{ $billDiscount->vat }}"></td>
                            <td><input type="number" class="form-control" name="final_amount[]" value="{{ $bill->final_amount }}" readonly></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <button type="button" class="btn btn-primary" onclick="addRow2()">Add Row</button>
            <button type="submit" class="btn btn-success">Update</button>
        </form>
    </div>
    <script>
        function calculateTotal(element) {
            const row = element.closest('tr');
            const quantity = parseFloat(row.querySelector('input[name="quantity[]"]').value) || 0;
            const unitPrice = parseFloat(row.querySelector('input[name="unitPrice[]"]').value) || 0;
            const discount = parseFloat(row.querySelector('input[name="discount[]"]').value) || 0;
            const totalAmount = (quantity * unitPrice) - discount;
            row.querySelector('input[name="totalAmount[]"]').value = totalAmount.toFixed(2);
        }

        function addRow() {
            const row = `
        <tr>
            <td><input type="text" class="form-control" name="product_name[]"></td>
            <td><textarea class="form-control" name="description[]"></textarea></td>
            <td><input type="number" class="form-control" name="quantity[]" value="1" onchange="calculateTotal(this)"></td>
            <td><input type="number" class="form-control" name="unitPrice[]" onchange="calculateTotal(this)"></td>
            <td><input type="number" class="form-control" name="discount[]" onchange="calculateTotal(this)"></td>
            <td><input type="number" class="form-control" name="total_amount[]" readonly></td>
            <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">Delete</button></td>
        </tr>`;
            document.getElementById('bill-items').insertAdjacentHTML('beforeend', row);
        }

        function addRow2() {
            const row = `
        <tr>
            <td><input type="number" class="form-control" name="discount_amount[]"></td>
            <td><input type="number" class="form-control" name="vat[]"></td>
            <td><input type="number" class="form-control" name="final_amount[]" readonly></td>
        </tr>`;
            document.getElementById('bill-items2').insertAdjacentHTML('beforeend', row);
        }

        function removeRow(button) {
            button.closest('tr').remove();
        }
    </script>
@endsection
