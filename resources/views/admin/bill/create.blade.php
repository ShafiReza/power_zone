@extends('admin.layout.layout')
@section('content')
<style>
    .container {
        margin-top: 20px;
    }
</style>
<div class="content-wrapper container">

     <h2>Billing Page</h2>
     <form id="billing-form">
         <div class="form-row">
             <div class="form-group col-4">
                 <label for="customerName">Customer Name</label>
                 <input type="text" class="form-control" id="customerName" placeholder="Customer Name">
             </div>
             <div class="form-group col-4">
                 <label for="billType">Bill Type</label>
                 <select id="billType" class="form-control">
                     <option value="Day">Day</option>
                     <option value="Month">Month</option>
                 </select>
             </div>

             <div class="form-group col-4">
                 <label for="billDate">Date</label>
                 <input type="date" class="form-control" id="billDate" value="{{ date('Y-m-d') }}">
             </div>
         </div>
         <div class="form-group col-4">
             <label for="services">Services</label>
             <select id="services" class="form-control">
                 <option>Select Service</option>
             </select>
         </div>
         <table class="table table-bordered">
             <thead>
                 <tr>
                     <th>Product Name</th>
                     <th>Description</th>
                     <th>Quantity</th>
                     <th>Unit Price</th>
                     <th>Discount</th>
                     <th>Total Amount</th>
                     <th>Action</th>
                 </tr>
             </thead>
             <tbody id="bill-items">
                 <tr>
                     <td><input type="text" class="form-control" name="productName[]"></td>
                     <td><input type="text" class="form-control" name="description[]"></td>
                     <td><input type="number" class="form-control" name="quantity[]" onchange="calculateTotal(this)"></td>
                     <td><input type="number" class="form-control" name="unitPrice[]" onchange="calculateTotal(this)"></td>
                     <td><input type="number" class="form-control" name="discount[]" onchange="calculateTotal(this)"></td>
                     <td><input type="number" class="form-control" name="totalAmount[]" readonly></td>
                     <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">Delete</button></td>
                 </tr>
             </tbody>
         </table>
         <button type="button" class="btn btn-primary mb-5" onclick="addRow()">Add Row</button>
         <button type="button" class="btn btn-success mb-5" onclick="saveBill()">Save</button>
         <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Discount</th>
                    <div class="form-group col-4">
                        <label for="billType">Discount Type</label>
                        <select id="billType" class="form-control">
                            <option value="Day">Flat</option>
                            <option value="Month">Percentage</option>
                        </select>
                    </div>
                   <th>Final Amount</th>
                </tr>
            </thead>
            <tbody id="bill-items2">
                <tr>
                    <td><input type="number" class="form-control" name="discount[]" onchange="calculateTotal(this)"></td>
                    <td><input type="number" class="form-control" name="totalAmount[]" readonly></td>
                    <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">Delete</button></td>
                </tr>
            </tbody>
        </table>
        <button type="button" class="btn btn-primary" onclick="addRow2()">Add Row</button>
        <button type="button" class="btn btn-success">Submit</button>
     </form>
 </div>
</div>
<script>
    function calculateTotal(element) {
        const row = element.closest('tr');
        const quantity = parseFloat(row.querySelector('input[name="quantity[]"]').value) || 0;
        const unitPrice = parseFloat(row.querySelector('input[name="unitPrice[]"]').value) || 0;
        const discount = parseFloat(row.querySelector('input[name="discount[]"]').value) || 0;
        const totalAmount = (quantity * unitPrice) - discount;
        row.querySelector('input[name="totalAmount[]"]').value = totalAmount.toFixed(2);
    }

    function addRow() {
        const row = `
            <tr>
                <td><input type="text" class="form-control" name="productName[]"></td>
                <td><input type="text" class="form-control" name="description[]"></td>
                <td><input type="number" class="form-control" name="quantity[]" onchange="calculateTotal(this)"></td>
                <td><input type="number" class="form-control" name="unitPrice[]" onchange="calculateTotal(this)"></td>
                <td><input type="number" class="form-control" name="discount[]" onchange="calculateTotal(this)"></td>
                <td><input type="number" class="form-control" name="totalAmount[]" readonly></td>
                <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">Delete</button></td>
            </tr>`;
        document.getElementById('bill-items').insertAdjacentHTML('beforeend', row);
    }
    function addRow2() {
        const row = `
            <tr>
                 <tr>

                    <td><input type="number" class="form-control" name="discount[]" onchange="calculateTotal(this)"></td>
                    <td><input type="number" class="form-control" name="totalAmount[]" readonly></td>
                    <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">Delete</button></td>
                </tr>
            </tr>`;
        document.getElementById('bill-items2').insertAdjacentHTML('beforeend', row);
    }

    function removeRow(button) {
        button.closest('tr').remove();
    }


</script>

@endsection




