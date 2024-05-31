<button {{ $attributes->merge(['type' => 'submit', 'class' => 'py-2 px-3 inline-flex justify-center items-center gap-2 rounded-sm border border-transparent font-semibold bg-primary text-white hover:bg-primary focus:outline-none focus:ring-0 focus:ring-primary focus:ring-offset-0 transition-all text-sm dark:focus:ring-offset-white/10']) }}>
    {{ $slot }}
</button>
