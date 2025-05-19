@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'block w-full max-w-xs border-0 border-bottom border-dark pl-4']) !!}>