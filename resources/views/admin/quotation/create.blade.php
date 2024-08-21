@extends('admin.layout.layout')

@section('content')
    <div class="content-wrapper container-fluid col-11 table-responsive">
        <h2>Billing Page</h2>
        <form id="billing-form" method="POST" action="{{ route('quotation.store') }}">
            @csrf
            <div class="form-row">

                    <div class="form-group col-2">
                        <label for="customerName">Customer Name</label>
                        <select id="customerName" name="customerName" class="form-control" onchange="getCustomerDetails(this.value)">
                            <option value="">Select Customer</option>
                            @foreach ($irregularCustomers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>

            <div class="form-row col-4">
                <div class="form-group col-4">
                    <label for="billType">Bill Type</label>
                    <select id="billType" name="billType" class="form-control">
                        <option value="Day">Day</option>
                        <option value="Month">Month</option>
                    </select>
                </div>
                <div class="form-group col-4">
                    <label for="billDate">Date</label>
                    <input type="date" name="quotation_date" class="form-control" id="billDate" value="{{ date('Y-m-d') }}">
                </div>
                <div class="form-group col-4">
                    <label for="products">Products</label>
                    <select id="products" class="form-control" onchange="addProductRow(this.value)">
                        <option value="">Select Product</option>
                        <option value="">-</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>



            <input type="hidden" name="products[]" value="">
            <table class="table table-hover">
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

            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Discount Type</th>
                        <th>Discount</th>
                        <th>VAT</th>
                        <th>Final Amount</th>
                    </tr>
                </thead>
                <tbody id="bill-items2">
                    <tr>
                        <td><select class="form-control col-6 discountType" name="bill_items2[0][discount_type]" onchange="calculateFinalAmount()">
                                <option value="Flat">Flat</option>
                                <option value="Percentage">Percentage</option>
                            </select></td>
                        <td><input type="number" class="form-control col-6 discount" name="bill_items2[0][discount]" value="0.00" onchange="calculateFinalAmount()"></td>
                        <td><input type="number" class="form-control col-6 vat" name="bill_items2[0][vat]" value="0.00" onchange="calculateFinalAmount()"></td>
                        <td><input type="text" class="form-control col-6 final-amount" name="bill_items2[0][final_amount]" value="0.00" readonly></td>
                        {{-- <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">Delete</button></td> --}}
                    </tr>
                </tbody>
            </table>
            {{-- <button type="button" class="btn btn-primary" onclick="addRow2()">Add Row</button> --}}
            </div>
            <button type="submit" class="btn btn-success">Submit</button>
        </form>
    </div>

    <script>

        function getCustomers(customerType) {
            if (customerId) {
                $.ajax({
                    type: "GET",
                    url: "{{ route('get-customers') }}",
                    data: {
                        customerId: customerId
                    },
                    success: function(data) {
                        // do something with the customer details
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            }
        }



        function addProductRow(productId) {
            if (!productId) return;
            $.ajax({
                type: "GET",
                url: "{{ route('get-product') }}",
                data: {
                    productId: productId
                },
                success: function(data) {
                    const quantity = data.quantity > 0 ? 1 : 0;
                    const row = `
                        <tr>
                            <td><input type="text" class="form-control" name="product_name[]" value="${data.name}" readonly></td>
                            <td><textarea class="form-control" name="description[]"></textarea></td>
                            <td><input type="number" class="form-control quantity" name="quantity[]" value="${quantity}" onchange="calculateTotal(this)"></td>
                            <td><input type="number" class="form-control unitPrice" name="unitPrice[]" value="${data.sell_price}" onchange="calculateTotal(this)"></td>
                            <td><input type="number" class="form-control discount" name="discount[]" value="0.00" onchange="calculateTotal(this)"></td>
                            <td>
                                <select class="form-control discountType" name="discountType[]" onchange="calculateTotal(this)">
                                    <option value="Percentage">Percentage(%)</option>
                                    <option value="Flat">Flat</option>
                                </select>
                            </td>
                            <td><input type="number" class="form-control total-amount" name="total_amount[]" readonly></td>
                            <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">Delete</button></td>
                        </tr>
                    `;
                    $('#bill-items').append(row);
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }

        function calculateTotal(element) {
            const row = $(element).closest('tr');
            const quantity = parseFloat(row.find('.quantity').val()) || 0;
            const unitPrice = parseFloat(row.find('.unitPrice').val()) || 0;
            const discount = parseFloat(row.find('.discount').val()) || 0;
            const discountType = row.find('.discountType').val();

            let totalAmount = quantity * unitPrice;

            if (discountType === 'Percentage') {
                totalAmount -= totalAmount * (discount / 100);
            } else {
                totalAmount -= discount;
            }

            row.find('.total-amount').val(totalAmount.toFixed(2));

            calculateFinalAmount();
        }

        function calculateFinalAmount() {
            let finalAmount = 0;

            $('#bill-items .total-amount').each(function() {
                finalAmount += parseFloat($(this).val()) || 0;
            });

            $('#bill-items2 tr').each(function() {
                const discountType = $(this).find('.discountType').val();
                const discountAmount = parseFloat($(this).find('.discount').val()) || 0;
                const vat = parseFloat($(this).find('.vat').val()) || 0;

                let discount = 0;
                if (discountType === 'Percentage') {
                    discount = finalAmount * (discountAmount / 100);
                } else {
                    discount = discountAmount;
                }

                finalAmount += vat;
                finalAmount -= discount;
            });

            $('.final-amount').val(finalAmount.toFixed(2));
        }

        function removeRow(button) {
            $(button).closest('tr').remove();
            calculateFinalAmount();
        }
    </script>
@endsection
