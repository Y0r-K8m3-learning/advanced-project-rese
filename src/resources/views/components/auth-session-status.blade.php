@props(['status'])

@if ($status)
<div {{ $attributes->merge(['class' => 'font-medium text-sm text-green-600 mt-3']) }}>
    {{ $status }}
</div>
@endif