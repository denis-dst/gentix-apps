<x-guest-layout>
<div class="mb-8">
    <h2 class="text-3xl font-bold font-outfit text-white mb-2">Create Account</h2>
    <p class="text-slate-400 font-light">Join the GenTix network as an Event Provider.</p>
</div>

<form method="POST" action="{{ route('register') }}" class="space-y-6">
    @csrf

    <!-- Name -->
    <div>
        <x-input-label for="name" :value="__('Full Name')" />
        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="John Doe" />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <!-- Organization Name -->
    <div>
        <x-input-label for="organization_name" :value="__('Organization Name')" />
        <x-text-input id="organization_name" class="block mt-1 w-full" type="text" name="organization_name" :value="old('organization_name')" required placeholder="e.g. Dreamworld Events" />
        <x-input-error :messages="$errors->get('organization_name')" class="mt-2" />
    </div>

    <!-- Email Address -->
    <div>
        <x-input-label for="email" :value="__('Work Email')" />
        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="john@organization.com" />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <!-- Password -->
    <div>
        <x-input-label for="password" :value="__('Password')" />
        <x-text-input id="password" class="block mt-1 w-full"
                        type="password"
                        name="password"
                        required autocomplete="new-password" placeholder="••••••••" />
        <x-input-error :messages="$errors->get('password')" class="mt-2" />
    </div>

    <!-- Confirm Password -->
    <div>
        <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
        <x-text-input id="password_confirmation" class="block mt-1 w-full"
                        type="password"
                        name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
    </div>

    <div class="pt-4">
        <x-primary-button>
            {{ __('Register as Partner') }}
        </x-primary-button>
    </div>

    <div class="text-center mt-6">
        <p class="text-sm text-slate-400">
            Already registered? 
            <a class="font-bold text-gentix-400 hover:text-gentix-300 transition underline decoration-2 underline-offset-4" href="{{ route('login') }}">
                {{ __('Log in here') }}
            </a>
        </p>
    </div>
</form>
</x-guest-layout>
