<div class="toast-container position-fixed bottom-0 end-0 p-3">
@if(session('success'))<div class="toast align-items-center text-bg-success border-0" role="alert"><div class="d-flex"><div class="toast-body">{{ session('success') }}</div><button class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div></div>@endif
@if(session('error'))<div class="toast align-items-center text-bg-danger border-0" role="alert"><div class="d-flex"><div class="toast-body">{{ session('error') }}</div><button class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div></div>@endif
</div>
