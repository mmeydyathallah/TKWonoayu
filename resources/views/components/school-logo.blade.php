@props([
    'class' => 'h-10 w-10',
])

<img
    {{ $attributes->merge(['class' => $class . ' object-contain drop-shadow-[0_6px_14px_rgba(56,189,248,0.28)]']) }}
    src="{{ asset('images/logo-tk.png') }}"
    alt="Logo TK Wonoayu"
>
