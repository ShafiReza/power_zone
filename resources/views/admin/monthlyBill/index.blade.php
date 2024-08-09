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
                        @if($bill->status == 'paid' && $bill->due_amount == 0)
                            <button class="btn btn-sm btn-secondary">
                                Paid
                            </button>
                        @elseif($bill->status == 'Partial' && $bill->due_amount > 0)
                            <button class="btn btn-sm btn-info mark-paid-button" data-id="{{ $bill->id }}" data-final-amount="{{ $bill->amount }}" data-due-amount="{{ $bill->due_amount }}">
                                Partial
                            </button>
                        @elseif($bill->status == 'pending')
                            <button class="btn btn-sm btn-success mark-paid-button" data-id="{{ $bill->id }}" data-final-amount="{{ $bill->amount }}" data-due-amount="{{ $bill->due_amount }}">
                                Mark as Paid
                            </button>
                        @elseif($bill->status == 'due')
                            <button class="btn btn-sm btn-danger">
                                Due
                            </button>
                        @elseif($bill->status != 'paid' && $bill->bill_month == \Carbon\Carbon::now()->format('F Y'))
                            <button class="btn btn-sm btn-danger">
                                Due
                            </button>
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
                        <a href="{{ route('admin.monthlyBill.invoice', ['clientId' => $bill->id, 'month' => $bill->bill_month]) }}"
                           onclick="printInvoice(event, '{{ route('admin.monthlyBill.showInvoicePrint', $bill->id) }}')"
                           class="btn btn-default"><i class="fas fa-print"></i> Print</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="modal fade" id="markPaidModal" tabindex="-1" role="dialog" aria-labelledby="markPaidModalLabel" aria-hidden="true">
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
                            <input type="text" name="amount" id="billAmount" class="form-control" readonly>
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
        fetch('{{ route('monthlyBill.Paid') }}', {
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
