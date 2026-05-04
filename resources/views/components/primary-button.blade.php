<button {{ $attributes->merge(['type' => 'submit', 'class' => 'w-auto justify-center inline-flex items-center px-6 py-2.5 bg-orange-600 border border-transparent rounded-lg font-bold text-sm text-white uppercase tracking-widest hover:bg-orange-700 focus:bg-orange-700 active:bg-orange-800 focus:outline-none focus:ring-2 focus:ring-orange-600 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md']) }}>
    {{ $slot }}
</button>
