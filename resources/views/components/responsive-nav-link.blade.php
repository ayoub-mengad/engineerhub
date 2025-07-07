@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-white text-start text-base font-medium text-white bg-[#004182] focus:outline-none focus:text-white focus:bg-[#004182] focus:border-white transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-white hover:text-[#EEF3F8] hover:bg-[#004182] hover:border-[#EEF3F8] focus:outline-none focus:text-[#EEF3F8] focus:bg-[#004182] focus:border-[#EEF3F8] transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
