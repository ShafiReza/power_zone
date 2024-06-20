@extends('admin.layout.layout')

@section('content')
    <div class="content-wrapper container">
        <h2>Billing Page</h2>
        <form id="billing-form" method="POST" action="{{ route('bill.store') }}">
            @csrf
            <div class="form-group col-4">
                <label for="customerType">Customer Type</label>
                <select id="customerType" class="form-control" onchange="getCustomers(this.value)">
                    <option value="">Select Customer Type</option>
                    <option value="regularCustomer">Regular customer</option>
                    <option value="irregularCustomer">Irregular Customer</option>
                </select>
            </div>
            <div class="form-row">
                <div class="form-group col-4">
                    <label for="customerName">Customer Name</label>
                    <select id="customerName" class="form-control">
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-4">
                    <label for="billType">Bill Type</label>
                    <select id="billType" class="form-control">
                        <option value="Day">Day</option>
                        <option value="Month">Month</option>
                    </select>
                </div>

                <div class="form-group col-4">
                    <label for="billDate">Date</label>
                    <input type="date" class="form-control" id="billDate" value="{{ date('Y-m-d') }}">
                </div>
            </div>
            <div class="form-group col-4">
                <label for="products">Products</label>
                <select id="products" class="form-control" onchange="addProductRow(this.value)">
                    <option value="">Select Product</option>
                    @foreach ($products as $product)
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
                        <th>Discount Type</th>
                        <th>Total Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="bill-items">

                </tbody>
            </table>
            <button type="button" class="btn btn-primary mb-5" onclick="addRow()">Add Row</button>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Discount Type</th>
                        <th>Discount</th>
                        <th>VAT</th>
                        <th>Final Amount</th>
                    </tr>
                </thead>
                <tbody id="bill-items2">


                </tbody>
            </table>
            <button type="button" class="btn btn-primary" onclick="addRow2()">Add Row</button>
            <button type="submit" class="btn btn-success">Submit</button>
        </form>
    </div>
    <script>
        function getCustomers(customerType) {
            $.ajax({
                type: "GET",
                url: "{{ route('get-customers') }}",
                data: {
                    customerType: customerType
                },
                success: function(data) {
                    $('#customerName').empty();
                    $('#customerName').append(
                        '<option value="">Select Customer</option>'); // Add default option

                    // Populate customer dropdown based on customerType
                    $.each(data, function(index, customer) {
                        $('#customerName').append('<option value="' + customer.id + '">' + customer
                            .name + '</option>');
                    });
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    // Handle error if needed
                }
            });
        }



        function addProductRow(productId) {
            $.ajax({
                type: "GET",
                url: "{{ route('get-product') }}",
                data: {
                    productId: productId
                },
                success: function(data) {
                    const row = `
               <tr>
    <td><input type="text" class="form-control" name="product_name[]" value="${data.name}"></td>
    <td><textarea class="form-control" name="description[]"></textarea></td>
    <td><input type="number" class="form-control" name="quantity[]" value="1" onchange="calculateTotal(this)"></td>
    <td><input type="number" class="form-control" name="unitPrice[]" value="${data.price}" onchange="calculateTotal(this)"></td>

    <td><input type="number" class="form-control" name="discount[]" onchange="calculateTotal(this)"></td>
    <td>
        <select class="form-control" name="discountType[]" onchange="calculateTotal(this)">
            <option value="Percentage">Percentage</option>
            <option value="Flat">Flat</option>
        </select>
    </td>
    <td><input type="number" class="form-control" name="total_amount[]" readonly></td>
    <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">Delete</button></td>
</tr>
            `;
                    document.getElementById('bill-items').insertAdjacentHTML('beforeend', row);
                }
            });
        }

        function addRow2() {
            const row = `
        <tr>
            <td>
                <select class="form-control discountType" onchange="calculateFinalAmount()">
                    <option value="Flat">Flat</option>
                    <option value="Percentage">Percentage</option>
                </select>
            </td>
            <td><input type="number" class="form-control discount" onchange="calculateFinalAmount()"></td>
            <td><input type="number" class="form-control vat" onchange="calculateFinalAmount()"></td>
            <td><input type="number" class="form-control finalAmount" readonly></td>
            <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">Delete</button></td>
        </tr>
    `;

            document.getElementById('bill-items2').insertAdjacentHTML('beforeend', row);
        }


        function calculateTotal(element) {
            const row = element.closest('tr');
            const quantity = parseFloat(row.querySelector('input[name="quantity[]"]').value) || 0;
            const unitPrice = parseFloat(row.querySelector('input[name="unitPrice[]"]').value) || 0;
            const discount = parseFloat(row.querySelector('input[name="discount[]"]').value) || 0;
            const discountType = row.querySelector('select[name="discountType[]"]').value;

            let totalAmount = quantity * unitPrice;

            if (discountType === 'Percentage') {
                totalAmount -= totalAmount * (discount / 100);
            } else {
                totalAmount -= discount;
            }

            row.querySelector('input[name="total_amount[]"]').value = totalAmount.toFixed(2);
            calculateFinalAmount();
        }

        //     document.addEventListener('DOMContentLoaded', function() {
        // let initialFinalAmount = 0;

        // // Iterate through each row in the bill-items table to calculate initial total amount
        // document.querySelectorAll('#bill-items tr').forEach(row => {
        //     calculateTotal(row); // Calculate total amount for current row
        //     initialFinalAmount += parseFloat(row.querySelector('input[name="total_amount[]"]').value) || 0;
        // });

        // // Set the initial final amount
        // document.getElementById('finalAmount').value = initialFinalAmount.toFixed(2);
        // });


        function calculateFinalAmount() {
    let finalAmount = parseFloat(document.getElementById('finalAmount').value) || 0;

    // Iterate through each row in the bill-items2 table
    document.querySelectorAll('#bill-items2 tr').forEach(row => {
        const discountType = row.querySelector('.discountType').value;
        const discountAmount = parseFloat(row.querySelector('.discount').value) || 0;
        const vat = parseFloat(row.querySelector('.vat').value) || 0;

        // Calculate discount
        let discount = 0;
        if (discountType === 'Percentage') {
            discount = finalAmount * (discountAmount / 100);
        } else {
            discount = discountAmount;
        }

        // Calculate final amount
        finalAmount += vat;
        finalAmount -= discount;
    });

    // Update the final amount field
    document.getElementById('finalAmount').value = finalAmount.toFixed(2);
}

// Calculate initial final amount on page load
document.addEventListener('DOMContentLoaded', function() {
    let initialFinalAmount = 0;

    // Iterate through each row in the bill-items table to calculate initial total amount
    document.querySelectorAll('#bill-items tr').forEach(row => {
        calculateTotal(row); // Calculate total amount for current row
        initialFinalAmount += parseFloat(row.querySelector('input[name="total_amount[]"]').value) || 0;
    });

    // Set the initial final amount
    document.getElementById('finalAmount').value = initialFinalAmount.toFixed(2);

    // Call calculateFinalAmount to update the final amount field
    calculateFinalAmount();
});


        function removeRow(button) {
            button.closest('tr').remove();
        }
    </script>
@endsection
