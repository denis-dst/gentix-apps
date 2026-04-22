<button {{ $attributes->merge(['type' => 'submit', 'class' => 'w-auto justify-center inline-flex items-center px-6 py-2.5 bg-purple-600 border border-transparent rounded-lg font-bold text-sm text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-purple-600 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md']) }}>
    {{ $slot }}
</button>
