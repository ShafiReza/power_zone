@extends('admin.layout.layout')

@section('content')
    <div class="content-wrapper container-fluid col-11 table-responsive">
        <h2>Billing Page</h2>
        <form id="billing-form" method="POST" action="{{ route('bill.store') }}">
            @csrf
            <div class="form-row">
                <div class="form-group col-3">
                    <label for="customerType">Customer Type</label>
                    <select id="customerType" name="customerType" class="form-control" onchange="getCustomers(this.value)">
                        <option value="">Select Customer Type</option>
                        <option value="regularCustomer">Regular Customer</option>
                        <option value="irregularCustomer">Irregular Customer</option>
                    </select>
                </div>
                <div class="form-group col-3" style="position: relative;">
                    <label for="customerName">Customer Name</label>
                    <input type="hidden" id="customerId" name="customerId" value="">
                    <input type="text" id="customerSearch" placeholder="Search.." class="form-control"
                        onkeyup="filterCustomers()">

                    <!-- Plus Icon aligned to the right -->
                    <i class="fas fa-plus-circle" id="addCustomer"
                        style="position: absolute; left: 450px; top: 35px; cursor: pointer;"
                        onclick="openCustomerModal()"></i>

                    <div id="customerDropdown" class="dropdown-content" onchange="getCustomers(this.value)">
                        <!-- Customer list will be populated here -->
                    </div>
                </div>

            </div>
            <div class="form-row">
                <div class="form-group col-3">
                    <label for="billType">Bill Type</label>
                    <select id="billType" name="billType" class="form-control">
                        <option value="Day">Day</option>
                        <option value="Month">Month</option>
                    </select>
                </div>

                <div class="form-group col-3">
                    <label for="billDate">Date</label>
                    <input type="date" name="billDate" class="form-control" id="billDate" value="{{ date('Y-m-d') }}">
                </div>

            </div>
            <div class="form-row">
                <div class="form-group col-3">
                    <label for="productType">Category</label>
                    <select id="productType" name="productType" class="form-control"
                        onchange="getProductsByCategory(this.value)">
                        <option value="">Select Product Type</option>
                        <option value="inventory">Inventory</option>
                        <option value="noninventory">Noninventory</option>
                    </select>
                </div>

                <div class="form-group col-3">
                    <label for="productName">Product Name</label>
                    <div id="productDropdownWrapper">
                        <input type="text" id="productSearch" placeholder="Select products..." class="form-control"
                            readonly onclick="toggleDropdown()">
                        <select id="productDropdown" class="form-control" onchange="selectProduct(this.value)"
                            size="5" style="display: none;">
                            <!-- Product list will be populated here -->
                        </select>
                    </div>
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
                        <th>Brand Name</th>
                        <th>Origin</th>
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
                                <option value="Flat">Flat</option>
                                <option value="Percentage">Percentage</option>
                            </select>
                        </td>
                        <td><input type="text" class="form-control discount" name="bill_items2[0][discount]"
                                value="0.00" oninput="calculateFinalAmount()"></td>
                        <td><input type="text" class="form-control vat" name="bill_items2[0][vat]" value="0.00"
                                oninput="calculateFinalAmount()"></td>
                        <td><input type="text" class="form-control receivable_amount"
                                name="bill_items2[0][receivable_amount]" value="0.00" oninput="calculateFinalAmount()">
                        </td>
                        <td><input type="text" class="form-control due_amount" name="bill_items2[0][due_amount]"
                                value="0.00" oninput="calculateFinalAmount()"></td>
                        <td><input type="text" class="form-control final-amount" name="bill_items2[0][final_amount]"
                                value="0.00" readonly></td>
                    </tr>
                </tbody>
            </table>

            <button type="submit" class="btn btn-success">Submit</button>
        </form>
        <div class="modal fade" id="customerModal" tabindex="-1" role="dialog" aria-labelledby="customerModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content shadow-lg">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="customerModalLabel">Add New Customer</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="customerForm" action="" method="POST">
                            @csrf
                            <!-- Dynamic Form Content will be injected here -->
                            <div id="customerFormContent"></div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function getProductsByCategory(productType) {
            if (productType) {
                $.ajax({
                    type: "GET",
                    url: "{{ route('getProductsByCategory') }}", // Ensure this route is correct
                    data: {
                        productType: productType
                    },
                    success: function(data) {
                        const select = $('#productDropdown');
                        select.empty(); // Clear previous options

                        // Add the default "Select a product" option
                        //select.append('<option value="" disabled selected>Select a product</option>');

                        // Populate the dropdown with products
                        $.each(data, function(index, product) {
                            select.append(
                                '<option value="' + product.id + '">' +
                                product.name + ' (' + product.part_no + ')' +
                                '</option>'
                            );
                        });

                        // Show the dropdown
                        $('#productDropdown').show();
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            } else {
                $('#productDropdown').empty().append('<option value="" disabled selected>Select a product</option>').hide();
            }
        }


        function toggleDropdown() {
            const select = $('#productDropdown');
            if (select.is(':hidden')) {
                select.show();
                $('#productSearch').attr('readonly', false).focus();

                // Add search input only if it doesn't exist

                const searchInput = $('<input>', {
                    type: 'text',
                    id: 'dropdownSearch',
                    class: 'form-control',
                    placeholder: 'Search...',
                    keyup: filterProducts
                });

                // Prepend search input to dropdown

                select.before(searchInput);

            } else {
                select.hide();
                $('#dropdownSearch').remove(); // Remove search input when hiding dropdown
                $('#productSearch').attr('readonly', true);
            }
        }

        function filterProducts() {
            const input = $('#dropdownSearch').val().toUpperCase();
            const select = $('#productDropdown');
            const options = select.find('option');

            options.each(function() {
                if (this.value) {
                    const txtValue = $(this).text().toUpperCase();
                    $(this).toggle(txtValue.indexOf(input) > -1);
                }
            });
        }

        function selectProduct(productId) {
            if (productId) {
                // Find the selected option text
                const selectedOption = $('#productDropdown option:selected').text();

                // Set the selected product in the search input (this is optional if you want to show it in the input field)
                $('#productSearch').val(selectedOption);

                // Hide the dropdown after selection
                $('#productDropdown').hide();
                $('#dropdownSearch').remove(); // Remove search input when hiding dropdown

                // Add the product row (can add the same product again)
                addProductRow(productId);

                // Clear the selected value so that the same product can be selected again
                $('#productDropdown').val('');
            }
        }
        $('#productSearch').on('keyup', filterProducts);
        // Hide the dropdown if clicked outside
        $(document).click(function(e) {
            if (!$(e.target).closest('#productSearch, #productDropdown').length) {
                $('#productDropdown').hide();
            }
        });


        function getCustomers(customerType) {
            if (customerType) {
                $.ajax({
                    type: "GET",
                    url: "{{ route('get_customers') }}",
                    data: {
                        customerType: customerType
                    },
                    success: function(data) {
                        $('#customerDropdown').empty().show(); // Clear previous customers and show dropdown
                        $.each(data, function(index, customer) {
                            $('#customerDropdown').append('<a href="#" onclick="selectCustomer(\'' +
                                customer.id + '\', \'' + customer.name + '\')">' + customer.name +
                                '</a>');
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            } else {
                $('#customerDropdown').empty().hide();
            }
        }

        function filterCustomers() {
            const input = document.getElementById("customerSearch");
            const filter = input.value.toUpperCase();
            const div = document.getElementById("customerDropdown");
            const a = div.getElementsByTagName("a");
            for (let i = 0; i < a.length; i++) {
                const txtValue = a[i].textContent || a[i].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    a[i].style.display = "";
                } else {
                    a[i].style.display = "none";
                }
            }
        }

        function selectCustomer(customerId, customerName) {
            $('#customerSearch').val(customerName); // Set the selected customer in the search input
            $('#customerDropdown').hide(); // Hide the dropdown after selection
            // You can perform further actions here (e.g., save customerId or trigger an event)
            $('#customerId').val(customerId);
        }

        // Hide the dropdown if clicked outside
        $(document).click(function(e) {
            if (!$(e.target).closest('#customerSearch, #customerDropdown').length) {
                $('#customerDropdown').hide();
            }
        });

        function addProductRow(productId) {
            if (!productId) return;

            const productType = $('#productType').val(); // Get the selected product type

            $.ajax({
                type: "GET",
                url: "{{ route('get-product') }}", // Ensure this route is correctly defined in your Laravel app
                data: {
                    productId: productId,
                    productType: productType // Pass the product type to the server
                },
                success: function(data) {
                    console.log(data); // Log the response to see if it's correct

                    const availableQuantity = data.quantity;
                    const initialQuantity = availableQuantity > 0 ? 1 : 0;

                    // Handle missing fields with default values
                    const brandName = data.brandName || 'None'; // Default value
                    const origin = data.origin || 'None'; // Default value

                    // Dynamically insert the product ID into the hidden input field and append a row to the table
                    const row = `
                <tr>
                    <input type="hidden" name="product_id[]" value="${productId}">
                    <td><input type="text" class="form-control" name="product_name[]" value="${data.name}" readonly></td>
                    <td><textarea class="form-control" name="description[]"></textarea></td>
                    <td>
                        <input type="number" class="form-control quantity" name="quantity[]" value="${initialQuantity}" min="0" oninput="calculateTotal(this)">
                        <input type="hidden" class="availableQuantity" value="${availableQuantity}">
                    </td>
                    <td><input type="number" class="form-control unitPrice" name="unitPrice[]" value="${data.sell_price}" oninput="calculateTotal(this)"></td>
                    <td><input type="number" class="form-control discount" name="discount[]" value="0.00" oninput="calculateTotal(this)"></td>
                    <td>
                        <select class="form-control discountType" name="discountType[]" oninput="calculateTotal(this)">
                            <option value="Percentage">Percentage(%)</option>
                            <option value="Flat">Flat</option>
                        </select>
                        <input type="hidden" name="productType[]" value="${productType}">
                    </td>
                    <td><input type="text" class="form-control brand-name" name="brand_name[]" value="${brandName}" readonly></td>
                    <td><input type="text" class="form-control origin" name="origin[]" value="${origin}" readonly></td>
                    <td><input type="number" class="form-control total-amount" name="total_amount[]" readonly></td>
                    <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">Delete</button></td>
                </tr>
            `;

                    const newRow = $(row);
                    $('#bill-items').append(newRow);

                    // Calculate the total amount based on the initial quantity and unit price
                    const quantity = initialQuantity;
                    const unitPrice = data.sell_price;
                    const totalAmount = quantity * unitPrice;
                    newRow.find('.total-amount').val(totalAmount.toFixed(2));

                    // Recalculate the final amount if needed
                    calculateFinalAmount();
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
            const productType = $('#productType').val(); // Get the product type

            if (productType === 'inventory') { // Check if it's an inventory product
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
            }

            // Rest of the code remains the same
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
        // Function to open the modal and load the correct form based on customer type
        function openCustomerModal() {
            const customerType = document.getElementById("customerType").value;

            let modalTitle = "Add New Customer";
            let formContent = "";

            if (customerType === "regularCustomer") {
                document.getElementById("customerForm").action = "{{ route('admin.regularCustomer.store') }}";
                formContent = `
            <div class="form-group col-12">
                <label for="name">Name:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group col-12">
                <label for="phone">Phone:</label>
                <input type="text" class="form-control" id="phone" name="phone" required>
            </div>
            <div class="form-group col-12">
                <label for="address">Address:</label>
                <input type="text" class="form-control" id="address" name="address" required>
            </div>
            <div class="form-group col-12">
                <label for="area">Area:</label>
                <input type="text" class="form-control" id="area" name="area" required>
            </div>
            <div class="form-group col-12">
                <label for="city">City:</label>
                <input type="text" class="form-control" id="city" name="city" required>
            </div>
            <div class="form-group col-12">
                <label for="note">Note:</label>
                <textarea class="form-control" id="note" name="note" rows="4" cols="4"></textarea>
            </div>
            <div class="form-group col-12">
                <label for="initial_bill_amount">Initial Bill Amount:</label>
                <input type="number" step="0.01" class="form-control" id="initial_bill_amount" name="initial_bill_amount" required>
            </div>
            <div class="form-group col-12">
                <label for="status">Status:</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
            <div class="form-group col-12">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        `;
            } else if (customerType === "irregularCustomer") {
                document.getElementById("customerForm").action = "{{ route('admin.irregularCustomer.store') }}";
                formContent = `
            <div class="form-group col-12">
                <label for="name">Name:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group col-12">
                <label for="phone">Phone:</label>
                <input type="text" class="form-control" id="phone" name="phone" required>
            </div>
            <div class="form-group col-12">
                <label for="address">Address:</label>
                <input type="text" class="form-control" id="address" name="address" required>
            </div>
            <div class="form-group col-12">
                <label for="area">Area:</label>
                <input type="text" class="form-control" id="area" name="area" required>
            </div>
            <div class="form-group col-12">
                <label for="city">City:</label>
                <input type="text" class="form-control" id="city" name="city" required>
            </div>
            <div class="form-group col-12">
                <label for="note">Note:</label>
                <textarea class="form-control" id="note" name="note" rows="4" cols="4"></textarea>
            </div>
            <div class="form-group col-12">
                <label for="status">Status:</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div class="form-group col-12">
                <button type="submit" class="btn btn-primary">Submit</button>

            </div>
        `;
            } else {
                formContent = `<p>Please select a customer type first.</p>`;
            }

            document.getElementById("customerModalLabel").innerText = modalTitle;
            document.getElementById("customerFormContent").innerHTML = formContent;

            // Show modal
            $('#customerModal').modal('show');
        }
    </script>
    <style>
        #customerDropdown {
            display: none;
            position: absolute;
            background-color: white;
            border: 1px solid #ddd;
            max-height: 150px;
            width: 100%;
            z-index: 100;
            /* Ensures dropdown appears on top */
            overflow-y: auto;
        }

        #customerDropdown a {
            padding: 8px 16px;
            display: block;
            text-decoration: none;
            color: black;
        }

        #customerDropdown a:hover {
            background-color: #f1f1f1;
        }
    </style>
    <style>
        #productDropdown {
            display: none;
            position: absolute;
            background-color: white;
            border: 1px solid #ddd;
            max-height: 200px;
            width: 100%;
            z-index: 9999;
            /* Ensures dropdown appears on top */
            overflow-y: auto;
        }

        #productDropdown a {
            padding: 8px 16px;
            display: block;
            text-decoration: none;
            color: black;
        }

        #productDropdown a:hover {
            background-color: #f1f1f1;
        }
    </style>
    <style>
        #addCustomer {
            color: #007bff;
            font-size: 20px;
            position: absolute;
            right: 10px;
            /* Aligns the icon to the right of the input field */
            top: 35px;
            /* Adjust this value based on the input field's height */
        }

        #addCustomer:hover {
            color: #0056b3;
        }

        #addCustomer {
            position: absolute;
            left: 450px;
            top: 35px;
            cursor: pointer;
            font-size: 36px;
            /* Adjust the size */
            color: #4CAF50;
            /* Icon color */
            transition: transform 0.3s, color 0.3s;
            /* Smooth transitions */
        }

        #addCustomer:hover {
            transform: scale(1.1);
            /* Slightly increase size on hover */
            color: #FF5722;
            /* Change color on hover */
        }

        #addCustomer:active {
            transform: scale(0.95);
            /* Slightly decrease size when clicked */
        }

        /* Ensure the modal appears over the customer list */
        .modal-dialog-centered {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            /* Full height to center vertically */
        }

        #customerModal {
            z-index: 1050;
            /* Higher than #customerDropdown to ensure it appears above */
        }

        .modal-backdrop {
            z-index: 1040;
            /* Ensures backdrop is behind the modal but above the dropdown */
        }

        /* Beautify Modal Background and Box Shadow */
        .modal-content {
            border-radius: 10px;
            /* Rounded corners */
            border: none;
            /* Remove default border */
            background-color: #f9f9f9;
            /* Light background color */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            /* Soft shadow */
        }

        /* Modal Header Customization */
        .modal-header {
            background-color: #007bff;
            /* Primary color background */
            border-bottom: none;
            /* Remove bottom border */
        }

        .modal-header .modal-title {
            font-weight: bold;
            /* Bold title */
            font-size: 1.25rem;
            /* Slightly larger font size */
        }

        .modal-header .close {
            font-size: 1.5rem;
            /* Larger close icon */
            color: white;
            /* White close button */
            opacity: 0.8;
            /* Slight transparency */
        }

        .modal-header .close:hover {
            opacity: 1;
            /* No transparency on hover */
        }

        /* Spacing and Font Improvements */
        .modal-body {
            padding: 20px 30px;
            /* Increased padding for cleaner look */
        }

        #customerFormContent .form-group {
            margin-bottom: 1rem;
            /* Increase spacing between fields */
        }

        .form-control {
            border-radius: 5px;
            /* Rounded inputs */
            border: 1px solid #ced4da;
            /* Lighter border */
            box-shadow: none;
            /* Remove shadow */
            transition: border-color 0.3s ease;
            /* Smooth focus transition */
        }

        .form-control:focus {
            border-color: #007bff;
            /* Primary color border on focus */
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.25);
            /* Soft blue glow */
        }

        /* Modal Footer Customization */
        .modal-footer {
            border-top: none;
            /* Remove top border */
            padding: 15px 30px;
            /* Padding adjustments */
            justify-content: flex-end;
            /* Align buttons to the right */
        }

        /* Button Enhancements */
        .btn-primary {
            background-color: #007bff;
            /* Primary button color */
            border-color: #007bff;
            /* Consistent border */
            border-radius: 5px;
            /* Rounded button */
        }

        .btn-secondary {
            background-color: #6c757d;
            /* Secondary button color */
            border-color: #6c757d;
            border-radius: 5px;
        }

        /* Add animation to modal appearance */
        .modal.fade .modal-dialog {
            transform: scale(0.8);
            /* Scale down initial size */
            transition: all 0.3s ease;
            /* Smooth transition */
        }

        .modal.show .modal-dialog {
            transform: scale(1);
            /* Scale up to normal size */
        }
    </style>
@endsection
