@extends('admin.layout.layout')

@section('content')
    <div class="content-wrapper container">
        <h2>Billing Page</h2>
        <form id="billing-form" method="POST" action="{{ route('bill.store') }}">
            @csrf
            <div class="form-row">
            <div class="form-group col-4">
                <label for="customerType">Customer Type</label>
                <select id="customerType" name="customerType" class="form-control" onchange="getCustomers(this.value)">
                    <option value="">Select Customer Type</option>
                    <option value="regularCustomer">Regular Customer</option>
                    <option value="irregularCustomer">Irregular Customer</option>
                </select>
            </div>
            <div class="form-group col-4">
                <label for="customerName">Customer Name</label>
                <select id="customerName" name="customerName" class="form-control">
                    <option value="">Select Customer</option>
                </select>
            </div>
        </div>
            <div class="form-row">
                <div class="form-group col-4">
                    <label for="billType">Bill Type</label>
                    <select id="billType" name="billType" class="form-control">
                        <option value="Day">Day</option>
                        <option value="Month">Month</option>
                    </select>
                </div>
                <div class="form-group col-4">
                    <label for="billDate">Date</label>
                    <input type="date" name="billDate" class="form-control" id="billDate" value="{{ date('Y-m-d') }}">
                </div>
            </div>
            <div class="form-row">
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
            <button type="button" class="btn btn-primary mb-5" onclick="addProductRow()">Add Row</button>
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
                    <tr>
                        <td><select class="form-control discountType" name="bill_items2[0][discount_type]" onchange="calculateFinalAmount()">
                                <option value="Flat">Flat</option>
                                <option value="Percentage">Percentage</option>
                            </select></td>
                        <td><input type="number" class="form-control discount" name="bill_items2[0][discount]" onchange="calculateFinalAmount()"></td>
                        <td><input type="number" class="form-control vat" name="bill_items2[0][vat]" onchange="calculateFinalAmount()"></td>
                        <td><input type="text" class="form-control final-amount" name="bill_items2[0][final_amount]" value="0.00" readonly></td>
                        {{-- <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">Delete</button></td> --}}
                    </tr>
                </tbody>
            </table>
            {{-- <button type="button" class="btn btn-primary" onclick="addRow2()">Add Row</button> --}}
            <button type="submit" class="btn btn-success">Submit</button>
        </form>
    </div>

    <script>

        function getCustomers(customerType) {
            if (customerType) {
                $.ajax({
                    type: "GET",
                    url: "{{ route('get-customers') }}",
                    data: {
                        customerType: customerType
                    },
                    success: function(data) {
                        $('#customerName').empty().append('<option value="">Select Customer</option>');
                        $.each(data, function(index, customer) {
                            $('#customerName').append('<option value="' + customer.id + '">' + customer
                                .name + '</option>');
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            } else {
                $('#customerName').empty().append('<option value="">Select Customer</option>');
            }
        }

        // Call getCustomers function based on the selected customer type
        $(document).ready(function() {
            $('#customerType').on('change', function() {
                var selectedType = $(this).val();
                getCustomers(selectedType);
            });
        });


        function addProductRow(productId) {
            if (!productId) return;
            $.ajax({
                type: "GET",
                url: "{{ route('get-product') }}",
                data: {
                    productId: productId
                },
                success: function(data) {
                    const row = `
                        <tr>
                            <td><input type="text" class="form-control" name="product_name[]" value="${data.name}" readonly></td>
                            <td><textarea class="form-control" name="description[]"></textarea></td>
                            <td><input type="number" class="form-control quantity" name="quantity[]" value="1" onchange="calculateTotal(this)"></td>
                            <td><input type="number" class="form-control unitPrice" name="unitPrice[]" value="${data.sell_price}" onchange="calculateTotal(this)"></td>
                            <td><input type="number" class="form-control discount" name="discount[]" onchange="calculateTotal(this)"></td>
                            <td>
                                <select class="form-control discountType" name="discountType[]" onchange="calculateTotal(this)">
                                    <option value="Percentage">Percentage</option>
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

        // function addRow2() {
        //     const row = `
        //         <tr>
        //             <td><select class="form-control discountType" name="bill_items2[0][discount_type]" onchange="calculateFinalAmount()">
        //                     <option value="Flat">Flat</option>
        //                     <option value="Percentage">Percentage</option>
        //                 </select></td>
        //             <td><input type="number" class="form-control discount" name="bill_items2[0][discount]" onchange="calculateFinalAmount()"></td>
        //             <td><input type="number" class="form-control vat" name="bill_items2[0][vat]" onchange="calculateFinalAmount()"></td>
        //             <td><input type="text" class="form-control final-amount" name="bill_items2[0][final-amount]" value="0.00" readonly></td>
        //             <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">Delete</button></td>
        //         </tr>
        //     `;
        //     $('#bill-items2').append(row);
        // }

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
