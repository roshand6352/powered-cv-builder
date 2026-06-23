@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'form-status']) }}>
        {{ $status }}
    </div>
@endif
