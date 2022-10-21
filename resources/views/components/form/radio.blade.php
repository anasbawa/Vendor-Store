@props([
    'name', 'options', 'label', 'checked' => false
])

@if ($label)
<label for="">{{ $label }}</label>
@endif

@foreach ($options as $value => $text )
<div class="form-check">
    <input type="radio" name="{{ $name }}" value="{{ $value }}"
    @checked(old($name, $checked) == $value)
    {{ $attributes->class([
        'form-check-input',
        'in-invalid' => $errors->has($name)
    ]) }}
    >

    <label class="form-check-label" for="exampleRadios1">
      {{ $text }}
    </label>
</div>
@endforeach
