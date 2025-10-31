@props([
    'type' => 'text',
    'name',
    'placeholder' => '',
    'required' => false,
    'value' => ''
])

<input 
    type="{{ $type }}"
    name="{{ $name }}"
    placeholder="{{ $placeholder }}"
    value="{{ old($name, $value) }}"
    {{ $required ? 'required' : '' }}
    {{ $attributes }}
>