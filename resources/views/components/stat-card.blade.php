@props(['color' => 'primary', 'value' => 0, 'label' => '', 'sub' => '', 'icon' => 'fa-chart-simple'])
<div class="gc-stat {{ $color === 'warning' ? 'brass' : '' }}">
    <div class="v">{{ $value }}</div>
    <div class="l">{{ $label }}</div>
    @if($sub)<div class="s">{{ $sub }}</div>@endif
</div>
