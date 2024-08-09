@extends('admin.layout.layout')

@section('content')
    <div class="content-wrapper container-fluid">
        <h2>Billing List</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer Name</th>
                    <th>Bill Date</th>
                    <th>Bill Type</th>
                    <th>Final Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bills as $bill)
                    <tr>
                        <td>{{ $bill->id }}</td>
                        <td>{{ $bill->customer_name }}</td>
                        <td>{{ $bill->bill_date }}</td>
                        <td>{{ $bill->bill_type }}</td>
                        <td>{{ $bill->final_amount }}</td>
                        <td>
                            <button
                                class="btn btn-sm {{ $bill->due_amount == 0 ? 'btn-success' : 'btn-warning' }} mark-paid-button"
                                data-id="{{ $bill->id }}" data-final-amount="{{ $bill->final_amount }}"
                                data-due-amount="{{ $bill->due_amount }}">
                                {{ $bill->due_amount == 0 ? 'Paid' : 'Partial' }}
                            </button>
                        </td>
                        <td>
                            <a href="{{ route('admin.bill.challan', $bill->id) }}" class="btn btn-info btn-sm">Challan</a>
                            <a href="{{ route('admin.bill.invoice', $bill->id) }}" class="btn btn-success btn-sm">Invoice</a>
                            <form action="{{ route('bill.destroy', $bill->id) }}" method="POST" style="display:inline;"
                                class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-danger delete-button"><i
                                        class="fas fa-trash-alt"></i></button>
                            </form>
                            <a href="{{ route('admin.bill.paymentHistory', ['bill' => $bill->id]) }}"
                                class="btn btn-info btn-sm">Payment History</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal for Mark as Paid -->
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
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="bill_amount">Bill Amount</label>
                            <input type="text" name="bill_amount" id="billAmount" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label for="receivable_amount">Receivable Amount</label>
                            <input type="text" name="receivable_amount" id="receivableAmount" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="due_amount">Due Amount</label>
                            <input type="text" name="due_amount" id="dueAmount" class="form-control" readonly>
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                    if (status === 'partial') {
                        document.getElementById('billAmount').value = dueAmount;
                    } else if (status === 'paid') {
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
        const formData = new FormData(this);
        fetch('{{ route('bill.markPaid') }}', {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => {
            // Debugging the response
            console.log(response);
            return response.json();
        })
        .then(data => {
            console.log(data); // Debugging the parsed JSON data
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
    </script>
@endsection
