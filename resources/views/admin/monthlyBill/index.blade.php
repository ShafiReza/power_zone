@extends('admin.layout.layout')

@section('content')
    <div class="content-wrapper container-fluid">
        <h2>Monthly Bill List</h2>

        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif

        <form action="{{ route('admin.monthlyBill.index') }}" method="GET">
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
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer Name</th>
                    <th>Customer Address</th>
                    <th>Amount</th>
                    <th>Description</th>
                    <th>Service</th>
                    <th>Bill Month</th>
                    <th>Start Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>

                @php
                    $totalAmount = 0;
                @endphp
                @foreach ($bills as $bill)
                    <tr>
                        <td>{{ $bill->id }}</td>
                        <td>{{ $bill->regularCustomer ? $bill->regularCustomer->name : 'N/A' }}</td>
                        <td>{{ $bill->customer_address }}</td>
                        <td>{{ $bill->amount }}</td>
                        <td>{{ $bill->description }}</td>
                        <td>{{ ucfirst($bill->service) }}</td>
                        <td>{{ \Carbon\Carbon::parse($bill->bill_month)->format('F Y') }}</td>
                        <td>{{ $bill->start_date }}</td>
                        <td>
                            @if ($bill->status == 'pending')
                                @if (\Carbon\Carbon::parse($bill->start_date)->diffInMonths(\Carbon\Carbon::now()) < 1)
                                    <form action="{{ route('monthlyBill.toggleStatus', $bill->id) }}" method="POST">
                                        @csrf
                                        <button type="button" class="btn btn-sm btn-success" data-toggle="modal"
                                            data-target="#paymentModal">Mark as Paid</button>
                                    </form>
                                @elseif(\Carbon\Carbon::parse($bill->start_date)->diffInMonths(\Carbon\Carbon::now()) >= 1)
                                    <form action="{{ route('monthlyBill.toggleStatus', $bill->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-warning">Mark as Due</button>
                                    </form>
                                @else
                                    {{ ucfirst($bill->status) }}
                                @endif
                            @else
                                <form action="{{ route('monthlyBill.toggleStatus', $bill->id) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="btn btn-sm btn-secondary">{{ ucfirst($bill->status) }}</button>
                                </form>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('monthlyBill.destroy', $bill->id) }}" method="POST"
                                class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-danger delete-button">Delete</button>
                            </form>
                        </td>
                        <td><a class="btn btn-primary mb-3"
                                href="{{ route('admin.monthlyBill.showBill', ['id' => $bill->id]) }}">Payment History</a>
                        </td>


                        <td>
                            <a href="{{ route('admin.monthlyBill.showInvoice', $bill->id) }}"
                                onclick="printInvoice(event, '{{ route('admin.monthlyBill.showInvoicePrint', $bill->id) }}')"
                                class="btn btn-default"><i class="fas fa-print"></i> Print</a>
                        </td>
                    </tr>
                    @php
                        $totalAmount = $bill->amount;
                    @endphp
                @endforeach
            </tbody>
        </table>
        <!-- Modal -->
        <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="paymentModalLabel">Payment Details</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="paymentForm" method="POST" action="{{ route('monthlyBill.storePayment') }}">
                            @csrf
                            @if ($bill)
                                <input type="hidden" name="bill_id" id="modal_bill_id" value="{{ $bill->id }}">
                            @endif
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="bill_amount">Bill Amount</label>
                                <input type="number" class="form-control" id="bill_amount" name="bill_amount"
                                    value="{{ $totalAmount }}" readonly>

                            </div>
                            <div class="form-group">
                                <label for="receiveable_amount">Receivable Amount</label>
                                <input type="number" class="form-control" id="receiveable_amount" name="receiveable_amount"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="due_amount">Due Amount</label>
                                <input type="number" class="form-control" id="due_amount" name="due_amount" readonly>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="submitPaymentForm">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.delete-button');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const form = this.closest('.delete-form');
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You want to delete this bill!",
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

            $('#submitPaymentForm').on('click', function(event) {
                event.preventDefault(); // Prevent default form submission
                var form = $('#paymentForm');
                var formData = form.serialize();

                // Debugging: log the form data
                console.log("Form Data:", formData);

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.status === 'Payment added successfully.') {
                            $('#paymentModal').modal('hide');
                            Swal.fire('Success', response.status, 'success').then(
                                () => {
                                    location
                                        .reload(); // Reload the page to update the status
                                });
                        } else {
                            Swal.fire('Error', response.error, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire('Error', 'An error occurred while processing the payment',
                            'error');
                        console.log(xhr.responseText); // Log the error response
                    }
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
    <script>
        document.getElementById('receiveable_amount').addEventListener('input', function() {
            var billAmount = parseFloat(document.getElementById('bill_amount').value);
            var receiveableAmount = parseFloat(this.value);
            var dueAmount = billAmount - receiveableAmount;
            document.getElementById('due_amount').value = dueAmount.toFixed(2);
        });
    </script>
@endsection
