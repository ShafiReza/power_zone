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
                    <th>Receivable Amount</th>
                    <th>Due Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bills as $bill)
                    @php
                        $firstBillItem = $bill->billItems2->first();
                        $dueAmount = $firstBillItem->due_amount ?? 0;
                        $receivableAmount = $firstBillItem->receivable_amount ?? 0;
                        $isPaid = $dueAmount == 0 && $receivableAmount == $bill->final_amount;
                    @endphp
                    <tr>
                        <td>{{ $bill->id }}</td>
                        <td>{{ $bill->customer_name }}</td>
                        <td>{{ $bill->bill_date }}</td>
                        <td>{{ $bill->bill_type }}</td>
                        <td>{{ $bill->final_amount }}</td>
                        <td>{{ $receivableAmount }}</td>
                        <td>{{ $dueAmount }}</td>
                        <td>
                            @if ($isPaid)
                                <button class="btn btn-sm btn-success">Paid</button>
                            @else
                                @if ($dueAmount == 0)
                                    <button class="btn btn-sm btn-success mark-paid-button" data-id="{{ $bill->id }}"
                                        data-final-amount="{{ $bill->final_amount }}" data-due-amount="{{ $dueAmount }}"
                                        data-status="mark_as_paid">Mark as Paid</button>
                                @else
                                    <button class="btn btn-sm btn-warning mark-paid-button" data-id="{{ $bill->id }}"
                                        data-final-amount="{{ $bill->final_amount }}"
                                        data-due-amount="{{ $dueAmount }}" data-status="partial">Partial</button>
                                @endif
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.bill.challan', $bill->id) }}" class="btn btn-info btn-sm">Challan</a>
                            <a href="{{ route('admin.bill.invoice', $bill->id) }}"
                                class="btn btn-success btn-sm">Invoice</a>
                            <form action="{{ route('bill.destroy', $bill->id) }}" method="POST" style="display:inline;"
                                class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-danger delete-button">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
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
                <form id="markPaidForm" method="POST" action="{{ route('bill.markPaid') }}">
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
                            <input type="date" name="receive_date" id="receive_date" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="bill_amount">Bill Amount</label>
                            <input type="text" name="bill_amount" id="finalAmount" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label for="paid_amount">Paid Amount</label>
                            <input type="text" name="paid_amount" id="paidAmount" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label for="receivable_amount">Receivable Amount</label>
                            <input type="text" name="receivable_amount" id="receivableAmount" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="due_amount">Due Amount</label>
                            <input type="text" name="due_amount" id="dueAmount" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control"></textarea>
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
            // Delete button functionality
            document.querySelectorAll('.delete-button').forEach(button => {
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

            // Mark as Paid button functionality
            const markPaidButtons = document.querySelectorAll('.mark-paid-button');
            markPaidButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const billId = this.getAttribute('data-id');
                    const finalAmount = parseFloat(this.getAttribute('data-final-amount'));
                    const dueAmount = parseFloat(this.getAttribute('data-due-amount'));

                    document.getElementById('billId').value = billId;
                    document.getElementById('finalAmount').value = finalAmount;
                    document.getElementById('paidAmount').value = dueAmount;
                    document.getElementById('receivableAmount').value = '';
                    document.getElementById('dueAmount').value = dueAmount;

                    $('#markPaidModal').modal('show');
                });
            });

            // Calculate due amount on receivable amount change
            document.getElementById('receivableAmount').addEventListener('input', function() {
                const previousDueAmount = parseFloat(document.getElementById('dueAmount').value);
                const receivableAmount = parseFloat(this.value) || 0;
                const newDueAmount = previousDueAmount - receivableAmount;
                document.getElementById('dueAmount').value = newDueAmount;
            });

            // Handle form submission for marking as paid
            document.getElementById('markPaidForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                fetch('{{ route('bill.markPaid') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            if (data.due_amount === 0) {
                                document.querySelector(`.mark-paid-button[data-id="${data.bill_id}"]`)
                                    .outerHTML = '<button class="btn btn-sm btn-success">Paid</button>';
                                $('#markPaidModal').modal('hide'); // Hide modal when fully paid
                            } else {
                                document.querySelector(`.mark-paid-button[data-id="${data.bill_id}"]`)
                                    .outerHTML =
                                    '<button class="btn btn-sm btn-warning">Partial</button>';
                            }
                            location.reload(); // Reload the page to reflect the changes
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
