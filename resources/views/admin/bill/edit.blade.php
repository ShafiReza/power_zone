@extends('admin.layout.layout')

@section('content')
    <div class="content-wrapper container-fluid col-11 table-responsive">
        <h2>Edit Bill</h2>
        <form id="billing-form" method="POST" action="{{ route('bill.update', $bill->id) }}">
            @csrf
            <div class="form-row">
                <div class="form-group col-3">
                    <label for="customerType">Customer Type</label>
                    <select id="customerType" name="customerType" class="form-control" onchange="getCustomers(this.value)">
                        <option value="">Select Customer Type</option>
                        <option value="regularCustomer" {{ $bill->customerType == 'regularCustomer' ? 'selected' : '' }}>Regular Customer</option>
                        <option value="irregularCustomer" {{ $bill->customerType == 'irregularCustomer' ? 'selected' : '' }}>Irregular Customer</option>
                    </select>
                </div>
                <div class="form-group col-3">
                    <label for="customerName">Customer Name</label>
                    <select id="customerName" name="customerName" class="form-control">
                        <option value="">Select Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ $bill->customerName == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-3">
                    <label for="billType">Bill Type</label>
                    <select id="billType" name="billType" class="form-control">
                        <option value="Day" {{ $bill->billType == 'Day' ? 'selected' : '' }}>Day</option>
                        <option value="Month" {{ $bill->billType == 'Month' ? 'selected' : '' }}>Month</option>
                    </select>
                </div>
                <div class="form-group col-3">
                    <label for="billDate">Date</label>
                    <input type="date" name="billDate" class="form-control" id="billDate" value="{{ $bill->billDate }}">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-3">
                    <label for="products">Products</label>
                    <select id="products" class="form-control" onchange="addProductRow(this.value)">
                        <option value="">Select Product</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
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
                    @foreach ($bill->billItems as $item)
                        <tr>
                            <td><input type="text" class="form-control" name="product_name[]" value="{{ $item->product->name }}" readonly></td>
                            <td><textarea class="form-control" name="description[]">{{ $item->description }}</textarea></td>
                            <td>
                                <input type="number" class="form-control quantity" name="quantity[]" value="{{ $item->quantity }}" min="0" oninput="calculateTotal(this)">
                                <input type="hidden" class="availableQuantity" value="{{ $item->product->quantity }}">
                            </td>
                            <td><input type="number" class="form-control unitPrice" name="unitPrice[]" value="{{ $item->unit_price }}" oninput="calculateTotal(this)"></td>
                            <td><input type="number" class="form-control discount" name="discount[]" value="{{ $item->discount }}" oninput="calculateTotal(this)"></td>
                            <td>
                                <select class="form-control discountType" name="discountType[]" onchange="calculateTotal(this)">
                                    <option value="Percentage" {{ $item->discount_type == 'Percentage' ? 'selected' : '' }}>Percentage(%)</option>
                                    <option value="Flat" {{ $item->discount_type == 'Flat' ? 'selected' : '' }}>Flat</option>
                                </select>
                            </td>
                            <td><input type="number" class="form-control total-amount" name="total_amount[]" value="{{ $item->total_amount }}" readonly></td>
                            <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">Delete</button></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Discount Type</th>
                        <th>Discount</th>
                        <th>VAT</th>
                        <th>Receivable Amount</th>
                        <th>Due Amount</th>
                        <th>Final Amount</th>
                    </tr>
                </thead>
                <tbody id="bill-items2">
                    <tr>
                        <td>
                            <select class="form-control discountType" name="bill_items2[0][discount_type]"
                                onchange="calculateFinalAmount()">
                                <option value="Flat" {{ $bill->discount_type == 'Flat' ? 'selected' : '' }}>Flat</option>
                                <option value="Percentage" {{ $bill->discount_type == 'Percentage' ? 'selected' : '' }}>Percentage</option>
                            </select>
                        </td>
                        <td><input type="text" class="form-control discount" name="bill_items2[0][discount]"
                                value="{{ $bill->discount }}" oninput="calculateFinalAmount()"></td>
                        <td><input type="text" class="form-control vat" name="bill_items2[0][vat]" value="{{ $bill->vat }}"
                                oninput="calculateFinalAmount()"></td>
                        <td><input type="text" class="form-control receivable_amount"
                                name="bill_items2[0][receivable_amount]" value="{{ $bill->receivable_amount }}" oninput="calculateFinalAmount()">
                        </td>
                        <td><input type="text" class="form-control due_amount" name="bill_items2[0][due_amount]"
                                value="{{ $bill->due_amount }}" oninput="calculateFinalAmount()"></td>
                        <td><input type="text" class="form-control final_amount" name="bill_items2[0][final_amount]"
                                value="{{ $bill->final_amount }}" readonly></td>
                    </tr>
                </tbody>
            </table>

            <div class="form-row">
                <div class="form-group col-12 text-right">
                    <button type="submit" class="btn btn-primary">Update Bill</button>
                </div>
            </div>
        </form>
    </div>
@endsection

<script>
<script>
        function getCustomers(customerType) {
            if (customerType) {
                $.ajax({
                    type: "GET",
                    url: "{{ route('get_customers') }}",
                    data: {
                        customerType: customerType
                    },
                    success: function(data) {
                        $('#customerName').empty().append("<option value=''>Select Customer</option>");
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

        function addProductRow(productId) {
            if (!productId) return;
            $.ajax({
                type: "GET",
                url: "{{ route('get-product') }}",
                data: {
                    productId: productId
                },
                success: function(data) {
                    const availableQuantity = data.quantity;
                    const initialQuantity = availableQuantity > 0 ? 1 : 0;
                    const row = `
                <tr>
                    <td><input type="text" class="form-control" name="product_name[]" value="${data.name}" readonly></td>
                    <td><textarea class="form-control" name="description[]"></textarea></td>
                    <td>
                        <input type="number" class="form-control quantity" name="quantity[]" value="${initialQuantity}" min="0" oninput="calculateTotal(this)">
                        <input type="hidden" class="availableQuantity" value="${availableQuantity}">
                    </td>
                    <td><input type="number" class="form-control unitPrice" name="unitPrice[]" value="${data.sell_price}" oninput="calculateTotal(this)"></td>
                    <td><input type="number" class="form-control discount" name="discount[]" value="0.00" oninput="calculateTotal(this)"></td>
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
            const availableQuantity = parseFloat(row.find('.availableQuantity').val()) || 0;

            if (quantity > availableQuantity) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'There is not enough quantity in your product.',
                }).then(() => {
                    row.find('.quantity').val(availableQuantity); // Reset to maximum available quantity
                });
                return; // Stop further calculations
            }

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

            // Calculate the total amount from the bill items
            $('#bill-items .total-amount').each(function() {
                finalAmount += parseFloat($(this).val()) || 0;
            });

            // Process the additional fields (discount, VAT)
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

            // Calculate the due amount
            const receivableAmount = parseFloat($('.receivable_amount').val()) || 0;
            const dueAmount = finalAmount - receivableAmount;

            // Update the final amount and due amount fields
            $('.final-amount').val(finalAmount.toFixed(2));
            $('.due_amount').val(dueAmount.toFixed(2));
        }

        function removeRow(button) {
            $(button).closest('tr').remove();
            calculateFinalAmount();
        }
    </script>
</script>

