<a {{ $attributes->merge(['class' => 'group relative inline-flex border-2 border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 rounded-md hover:border-primary-600']) }}>
    <span class="w-full inline-flex items-center justify-center self-stretch px-6 py-3 text-sm text-primary-600 text-center font-semibold tracking-wide uppercase bg-white ring-1 ring-primary-500 ring-offset-1 transform transition-all duration-200 ease-out group-hover:-translate-y-1 group-hover:-translate-x-1 group-hover:text-primary-700 group-hover:bg-primary-50 group-focus:-translate-y-1 group-focus:-translate-x-1 group-active:translate-x-0 group-active:translate-y-0 rounded-md">
        {{ $slot }}
    </span>
</a>
