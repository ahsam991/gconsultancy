@extends('public.layout')
@section('title', ($page->meta_title ?? $page->title) . ' | Global Consultancy')
@section('meta_description', $page->meta_description ?? '')
@section('content')
<section class="gc-section"><div class="container" style="max-width:46rem">
<span class="gc-eyebrow">{{ setting('company_short', 'G Consultancy') }}</span>
<h1>{{ $page->title }}</h1>
<div class="mt-3">{!! $page->content !!}</div>
</div></section>
@endsection
