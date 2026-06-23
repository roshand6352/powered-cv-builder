@props([
    'content' => null,
])

@php
    $html = filled($content) ? \App\Support\Html\RichTextSanitizer::sanitize($content) : '';
@endphp

@if ($html !== '')
    <div {{ $attributes->merge(['class' => trim(($attributes->get('class') ?? '').' cv-rich-text')]) }}>{!! $html !!}</div>
@endif
