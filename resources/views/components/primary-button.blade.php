<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-[#0A66C2] border border-transparent rounded-lg font-semibold text-sm text-white tracking-normal hover:bg-[#004182] focus:bg-[#004182] active:bg-[#004182] focus:outline-none focus:ring-2 focus:ring-[#0A66C2] focus:ring-offset-2 transition-colors duration-150']) }}>
    {{ $slot }}
</button>
