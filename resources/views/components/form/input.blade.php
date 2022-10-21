@props([
    'type' => 'text', // default value
    'name',
    'value' => '',
    'label' => false
])

@if ($label)
<label for="">{{ $label }}</label>
@endif

<input
    type="{{ $type }}"
    name="{{ $name }}"
    value="{{ old($name, $value) }}"
    {{ $attributes->class([
        'form-control',
        'in-invalid' => $errors->has($name)
    ]) }}
>
@error($name)
<div class="invalid-feedback">
    {{ $message }}
</div>
@enderror

{{-- <x-form.validation-feedback :name="$name" /> --}}
