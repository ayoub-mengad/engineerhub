@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border border-gray-300 focus:border-[#0A66C2] focus:ring-1 focus:ring-[#0A66C2] rounded-lg shadow-sm text-[15px] leading-6']) }}>
