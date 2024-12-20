@extends('admin.layout.layout')

@section('content')
    <div class="content-wrapper container-fluid col-11 table-responsive">
        <h2>Monthly Bill List</h2>

        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif

        <form action="{{ route('admin.monthlyBill.index') }}" method="GET">
            @csrf
            <div class="form-row">
                <div class="form-group col-2">
                    <label for="month">Month:</label>
                    <input type="month" class="form-control" id="month" name="month">
                </div>
                <div class="form-group col-2">
                    <label for="customer_name">Customer Name:</label>
                    <input type="text" class="form-control" id="customer_name" name="customer_name">
                </div>
                <div class="form-group col-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </div>
        </form>

        <a class="btn btn-success mb-3" href="{{ route('admin.monthlyBill.create') }}">Create Monthly Bill</a>
        <div class="d-flex justify-content-between mb-3">
            @if ($bills->isNotEmpty())
                <!-- Left Side: Mark as Paid -->
                <form id="bulkMarkPaidForm" method="POST" action="{{ route('monthlyBill.bulkPaid') }}">
                    @csrf
                    <input type="hidden" name="selected_bills" id="selected-bills-pay">
                    <button type="button" id="bulk-pay-button" class="btn btn-primary">Mark Selected as Paid</button>
                </form>

                <!-- Right Side: Print Invoices -->
                <form id="bulk-print-form" method="POST" action="{{ route('admin.monthlyBill.bulkInvoice', ['clientId' => $clientId, 'month' => $month]) }}">
                    @csrf
                    <input type="hidden" name="selected_bills" id="selected-bills-print">
                    <button type="button" id="bulk-print-invoice" class="btn btn-success">Print Selected Invoices</button>
                </form>
            @else
                <!-- Display message below the Create Monthly Bill button -->
                <div class="w-100 text-center mt-3">
                    <p class="alert alert-info">Please create monthly bill first.</p>
                </div>
            @endif
        </div>

        <table class="table table-hover">
            <thead>
                <tr>
                    <th><input type="checkbox" id="select-all"></th>
                    <th>ID</th>
                    <th>Customer Name</th>
                    <th>Customer Address</th>
                    <th>Amount</th>
                    <th>Description</th>
                    <th>Service</th>
                    <th>Bill Month</th>
                    <th>Start Date</th>
                    <th>Status</th>
                    <th colspan="4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bills as $bill)
                    <tr>
                        <td><input type="checkbox" name="bill_ids[]" class="bill-checkbox" value="{{ $bill->id }}" data-amount="{{ $bill->amount }}"></td>
                        <td>{{ $bill->id }}</td>
                        <td>{{ $bill->regularCustomer ? $bill->regularCustomer->name : 'N/A' }}</td>
                        <td>{{ $bill->customer_address }}</td>
                        <td>{{ $bill->amount }}</td>
                        <td>{{ $bill->description }}</td>
                        <td>{{ ucfirst($bill->service) }}</td>
                        <td>{{ \Carbon\Carbon::parse($bill->bill_month)->format('F Y') }}</td>
                        <td>{{ $bill->start_date }}</td>
                        <td>
                            @if ($bill->status == 'paid' && $bill->due_amount == 0)
                                <button class="btn btn-sm btn-secondary">Paid</button>
                            @elseif($bill->status == 'pending')
                                <button class="btn btn-sm btn-success mark-paid-button" data-id="{{ $bill->id }}" data-final-amount="{{ $bill->amount }}" data-due-amount="{{ $bill->due_amount }}">Mark as Paid</button>
                            @elseif($bill->status == 'due')
                                <button class="btn btn-sm btn-danger mark-paid-button" data-id="{{ $bill->id }}" data-final-amount="{{ $bill->amount }}" data-due-amount="{{ $bill->due_amount }}">Due</button>
                            @elseif($bill->status != 'paid' && $bill->bill_month == \Carbon\Carbon::now()->format('F Y'))
                                <button class="btn btn-sm btn-danger">Due</button>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('monthlyBill.destroy', $bill->id) }}" method="POST" class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-danger delete-button">Delete</button>
                            </form>
                        </td>
                        <td>
                            <a href="{{ route('admin.monthlyBill.edit', $bill->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        </td>
                        <td>
                            <a class="btn btn-primary mb-3" href="{{ route('admin.monthlyBill.showBill', ['id' => $bill->id]) }}">Payment History</a>
                        </td>
                        <td>
                            <a href="{{ route('admin.monthlyBill.invoice', ['clientId' => $bill->id, 'month' => $bill->bill_month]) }}" onclick="printInvoice(event, '{{ route('admin.monthlyBill.showInvoicePrint', $bill->id) }}')" class="btn btn-default"><i class="fas fa-print"></i> Print</a>
                        </td>
                    </tr>
                @empty
                    
                @endforelse
            </tbody>
        </table>


        <!-- Bulk Mark as Paid Modal -->
        <div class="modal fade" id="bulkMarkPaidModal" tabindex="-1" role="dialog"
            aria-labelledby="bulkMarkPaidModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form id="bulkMarkPaidForm" method="POST" action="{{ route('monthlyBill.bulkPaid') }}">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="bulkMarkPaidModalLabel">Mark Selected Bills as Paid</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="selected_bills" id="selectedBillsBulkModal">
                            <div class="form-group">
                                <label for="bill_date">Receive Date</label>
                                <input type="date" name="receive_date" id="receive_date" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="total_amount">Total Amount</label>
                                <input type="text" id="totalAmountBulk" class="form-control" readonly>
                            </div>
                            <div class="form-group">
                                <label for="receivable_amount">Receivable Amount</label>
                                <input type="text" name="receivable_amount" id="receivableAmountBulkModal"
                                    class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="due_amount">Due Amount</label>
                                <input type="text" id="dueAmountBulkModal" class="form-control" readonly>
                            </div>
                            <div class="form-group">
                                <label for="descriptionBulkModal">Description</label>
                                <textarea id="descriptionBulkModal" class="form-control" name="description" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <!-- Ensure this button submits the form -->
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="markPaidModal" tabindex="-1" role="dialog" aria-labelledby="markPaidModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form id="markPaidForm">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="markPaidModalLabel">Mark as Paid</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="bill_id" id="billId">
                            <div class="form-group">
                                <label for="bill_date">Receive Date</label>
                                <input type="date" name="receive_date" id="receive_date" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="bill_amount">Bill Amount</label>
                                <input type="text" name="amount" id="billAmount" class="form-control" readonly>
                            </div>
                            <div class="form-group">
                                <label for="receivable_amount">Receivable Amount</label>
                                <input type="text" name="receivable_amount" id="receivableAmount"
                                    class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="due_amount">Due Amount</label>
                                <input type="text" name="due_amount" id="dueAmount" class="form-control" readonly>
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea name="description" id="description" class="form-control" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Select All checkbox functionality
            const selectAllCheckbox = document.getElementById('select-all');
            const billCheckboxes = document.querySelectorAll('.bill-checkbox');

            selectAllCheckbox.addEventListener('change', function() {
                billCheckboxes.forEach(checkbox => {
                    checkbox.checked = selectAllCheckbox.checked;
                });
                calculateTotalAndDueAmounts(); // Update total and due amounts when selecting all
            });

            // Handle Bulk Mark as Paid
            document.getElementById('bulk-pay-button').addEventListener('click', function() {
                const selectedBills = [];
                let totalAmount = 0;
                let paidCount = 0;

                billCheckboxes.forEach(checkbox => {
                    if (checkbox.checked) {
                        selectedBills.push(checkbox.value);
                        totalAmount += parseFloat(checkbox.dataset.amount);

                        // Count how many selected bills are already marked as paid
                        if (checkbox.closest('tr').querySelector('.btn-secondary')) {
                            paidCount++;
                        }
                    }
                });

                if (selectedBills.length === 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'You have not selected any record.',
                    });
                } else if (paidCount === 1) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning',
                        text: 'One of the selected bills is already marked as paid.',
                    });
                } else if (paidCount > 1) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning',
                        text: 'Multiple selected bills are already marked as paid.',
                    });
                } else {
                    // Set values in the modal for bulk mark as paid
                    document.getElementById('selectedBillsBulkModal').value = selectedBills.join(',');
                    document.getElementById('totalAmountBulk').value = totalAmount.toFixed(
                        2); // Show Total Amount
                    document.getElementById('dueAmountBulkModal').value = totalAmount.toFixed(
                        2); // Initially, Due = Total

                    // Clear any previous receivable amounts
                    document.getElementById('receivableAmountBulkModal').value = '';

                    // Show the bulk mark paid modal
                    $('#bulkMarkPaidModal').modal('show');
                }
            });

            // Handle input in the receivable amount field to calculate the due amount
            document.getElementById('receivableAmountBulkModal').addEventListener('input', function() {
                const totalAmount = parseFloat(document.getElementById('totalAmountBulk').value);
                const receivableAmount = parseFloat(this.value || 0); // Ensure it's a valid number
                const dueAmount = totalAmount - receivableAmount;

                document.getElementById('dueAmountBulkModal').value = dueAmount.toFixed(2); // Update Due Amount

                // Check if due amount is zero to enable/disable form submission
                // if (dueAmount > 0) {
                //     Swal.fire({
                //         icon: 'warning',
                //         title: 'Warning',
                //         text: 'Receivable amount must equal the total amount to proceed.',
                //     });
                // }
            });

            // Add event listener for bulk modal submit
            // Add event listener for bulk modal submit
            document.getElementById('bulkMarkPaidForm').addEventListener('submit', function(e) {
                e.preventDefault(); // Prevent default form submission

                const receivableAmount = parseFloat(document.getElementById('receivableAmountBulkModal')
                    .value);
                const totalAmount = parseFloat(document.getElementById('totalAmountBulk').value);

                if (receivableAmount !== totalAmount) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Receivable amount must be equal to the total bill amount.',
                    });
                    return;
                }

               // Prepare form data for submission
                const formData = new FormData(this);

                // Make the fetch call to submit the form data
                fetch('{{ route('monthlyBill.bulkPaid') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json',

                        }
                    }).then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message ||
                                    'An error occurred while processing the bills.',
                            });
                        }
                    }).catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An unexpected error occurred. Please try again.',
                        });
                    });

            });
            console.log('{{ route('monthlyBill.bulkPaid') }}');


            // Helper function to calculate total and due amounts
            function calculateTotalAndDueAmounts() {
                let totalAmount = 0;
                billCheckboxes.forEach(checkbox => {
                    if (checkbox.checked) {
                        totalAmount += parseFloat(checkbox.dataset.amount);
                    }
                });

                document.getElementById('totalAmountBulk').value = totalAmount.toFixed(2);
                document.getElementById('dueAmountBulkModal').value = totalAmount.toFixed(
                    2); // Initially due amount = total
            }

            // Update total and due amounts on checkbox changes
            billCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', calculateTotalAndDueAmounts);
            });

            // Handle Bulk Print Invoice Button
            document.getElementById('bulk-print-invoice').addEventListener('click', function() {
                const selectedBills = [];

                // Collect selected bill IDs for printing
                billCheckboxes.forEach(checkbox => {
                    if (checkbox.checked) {
                        selectedBills.push(checkbox.value); // Use value (bill ID) for bulk print
                    }
                });

                if (selectedBills.length === 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'You have not selected any record to print.',
                    });
                } else {
                    // Set the selected bill IDs in the hidden input and submit the form
                    document.getElementById('selected-bills-print').value = selectedBills.join(',');
                    document.getElementById('bulk-print-form').submit();
                }
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Attach click event listeners to delete buttons
            const deleteButtons = document.querySelectorAll('.delete-button');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const form = this.closest('.delete-form');
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You want to delete this customer!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            // Attach click event listeners to mark as paid buttons
            const markPaidButtons = document.querySelectorAll('.mark-paid-button');
            markPaidButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const billId = this.getAttribute('data-id');
                    const finalAmount = parseFloat(this.getAttribute('data-final-amount'));
                    const dueAmount = parseFloat(this.getAttribute('data-due-amount'));
                    const status = this.textContent.trim().toLowerCase(); // Get the status text

                    document.getElementById('billId').value = billId;

                    // Set bill amount to due amount for subsequent payments
                    // if (status === 'partial') {
                    //     document.getElementById('billAmount').value = dueAmount;
                    // } else
                    if (status === 'paid') {
                        document.getElementById('billAmount').value = finalAmount;
                    } else {
                        document.getElementById('billAmount').value = finalAmount;
                    }

                    document.getElementById('receivableAmount').value = '';
                    document.getElementById('dueAmount').value = dueAmount;

                    $('#markPaidModal').modal('show');
                });
            });

            // Calculate due amount on receivable amount change
            document.getElementById('receivableAmount').addEventListener('input', function() {
                const billAmount = parseFloat(document.getElementById('billAmount').value);
                const receivableAmount = parseFloat(this.value);
                const dueAmount = billAmount - receivableAmount;
                document.getElementById('dueAmount').value = dueAmount;
            });

            // Handle form submission for marking as paid
            document.getElementById('markPaidForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const billAmount = parseFloat(document.getElementById('billAmount').value);
                const receivableAmount = parseFloat(document.getElementById('receivableAmount').value);

                // Check if receivable amount is less than the bill amount
                if (receivableAmount < billAmount || receivableAmount > billAmount) {
                    const errorMessage = 'Receivable amount is less than the Bill Amount';
                    // Display the error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage,
                    });
                    return; // Prevent form submission
                }

                // If validation passes, proceed with form submission
                const formData = new FormData(this);
                fetch('{{ route('monthlyBill.Paid') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('An error occurred');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred');
                    });
            });
        });

        function printInvoice(event, printUrl) {
            event.preventDefault();
            var newWindow = window.open(printUrl, '_blank');
            newWindow.onload = function() {
                newWindow.print();
                newWindow.onfocus = function() {
                    newWindow.close();
                };
            };
        }
    </script>
@endsection
