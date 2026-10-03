<form method="POST" action="{{ route('payments.store') }}" class="row g-2 align-items-end">@csrf
<input type="hidden" name="invoice_id" value="{{ $invoice->id ?? request('invoice') }}">
<div class="col-md-4"><label class="form-label small">Amount (£) <span class="text-danger">*</span></label><input type="number" step="0.01" name="amount" value="{{ old('amount') }}" class="form-control" required></div>
<div class="col-md-4"><label class="form-label small">Method</label><select name="method" class="form-select"><option>Bank Transfer</option><option>Cash</option><option>Cheque</option><option>Card</option></select></div>
<div class="col-md-4"><button class="btn btn-success">Record Payment</button></div>
</form>
