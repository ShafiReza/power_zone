@extends('admin.layout.layout')

@section('content')
    <div class="content-wrapper container-fluid col-11 table-responsive">
        <h2>Billing List</h2>
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif
        <form method="GET" action="{{ route('admin.bill.index') }}" class="mb-3">
            <div class="input-group col-3">
                <input type="text" name="client_name" class="form-control" placeholder="Search by client name" value="{{ request()->client_name }}">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit">Search</button>
                </div>
            </div>
        </form>
        <table class="table table-hover">
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
                        $isPaid = $dueAmount == 0 ? 1 : 0;
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
                                <button class="btn btn-sm btn-warning mark-paid-button" data-id="{{ $bill->id }}"
                                    data-final-amount="{{ $bill->final_amount }}" data-due-amount="{{ $dueAmount }}"
                                    data-status="partial"
                                    @if ($bill->billItems2->first()) data-prev-due-amount="{{ $bill->billItems2->first()->due_amount ?? 0 }}"
                                @elseif($bill->payments)
                                data-prev-due-amount="{{ $bill->payments->last()->due_amount ?? 0 }}"
                                @else
                                    data-prev-due-amount="0" @endif>
                                    Partial
                                </button>
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
                <form action="{{ route('bill.markPaid') }}" method="POST">
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
                            <label for="paid_amount">Payable Amount</label>
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
                    const prevDueAmount = parseFloat(this.getAttribute('data-prev-due-amount'));
                    const newPaymentAmount = parseFloat(document.getElementById('receivableAmount')
                        .value);
                    const newDueAmount = prevDueAmount - newPaymentAmount;
                    const status = this.textContent.trim().toLowerCase();

                    document.getElementById('billId').value = billId;
                    document.getElementById('finalAmount').value = finalAmount;
                    document.getElementById('paidAmount').value =
                    prevDueAmount; // Set paid amount to previous due amount
                    document.getElementById('receivableAmount').value = '';
                    document.getElementById('dueAmount').value = newDueAmount;

                    $('#markPaidModal').modal('show');
                });
            });
            document.getElementById('receivableAmount').addEventListener('input', function() {
                const paidAmount = parseFloat(document.getElementById('paidAmount').value);
                const receivableAmount = parseFloat(this.value);
                const dueAmount = paidAmount - receivableAmount;
                document.getElementById('dueAmount').value = dueAmount;
            });

           
            document.getElementById('markPaidForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const paidAmount = parseFloat(document.getElementById('paidAmount').value);
                const receivableAmount = parseFloat(document.getElementById('receivableAmount').value);
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
