@extends('layouts.app')
@section('title','Revenue Overview')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Revenue Overview</h4>
    @if(Route::has('commissions.index'))
    <a href="{{ route('commissions.index') }}" class="btn btn-sm btn-outline-secondary">Commissions</a>
    @endif
</div>

<div class="row">
    @php
    $cardDefs = [
        ['label' => 'Claimed', 'value' => $cards['claimed'] ?? 0, 'cls' => 'text-warning'],
        ['label' => 'Received', 'value' => $cards['received'] ?? 0, 'cls' => 'text-success'],
        ['label' => 'Outstanding', 'value' => $cards['outstanding'] ?? 0, 'cls' => 'text-info'],
        ['label' => 'Expected', 'value' => $cards['expected'] ?? 0, 'cls' => 'text-primary'],
        ['label' => 'Clawed back', 'value' => $cards['clawed'] ?? 0, 'cls' => 'text-danger'],
    ];
    @endphp
    @foreach($cardDefs as $c)
    <div class="col-md-2 col-6 mb-3"><div class="card shadow-sm h-100"><div class="card-body"><div class="small text-muted">{{ $c['label'] }}</div><div class="h5 mb-0 {{ $c['cls'] }}">£{{ number_format($c['value'], 2) }}</div></div></div></div>
    @endforeach
</div>

<div class="row">
    <div class="col-md-4 mb-3"><div class="card shadow-sm h-100"><div class="card-body"><h6>Revenue by university (top 10)</h6><canvas id="revUni" height="220"></canvas></div></div></div>
    <div class="col-md-4 mb-3"><div class="card shadow-sm h-100"><div class="card-body"><h6>Revenue by country (top 10)</h6><canvas id="revCountry" height="220"></canvas></div></div></div>
    <div class="col-md-4 mb-3"><div class="card shadow-sm h-100"><div class="card-body"><h6>Revenue by month</h6><canvas id="revMonth" height="220"></canvas></div></div></div>
</div>

@push('scripts')
<script>
(function(){
    const uni = @json($revenueByUniversity ?? ['labels'=>[],'data'=>[]]);
    const cty = @json($revenueByCountry ?? ['labels'=>[],'data'=>[]]);
    const mon = @json($revenueByMonth ?? ['labels'=>[],'data'=>[]]);
    function bar(id, payload, label){
        const el = document.getElementById(id);
        if(!el || !window.Chart) return;
        new Chart(el, {type:'bar', data:{labels:payload.labels, datasets:[{label:label, data:payload.data}]}, options:{responsive:true, plugins:{legend:{display:false}}}});
    }
    bar('revUni', uni, 'Revenue £');
    bar('revCountry', cty, 'Revenue £');
    bar('revMonth', mon, 'Revenue £');
})();
</script>
@endpush
@endsection
