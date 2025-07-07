@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-[#202124]']) }}>
    {{ $value ?? $slot }}
</label>
