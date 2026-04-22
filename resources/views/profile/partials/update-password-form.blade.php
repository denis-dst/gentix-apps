<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6"
        x-data="{ 
            password: '', 
            strength: 0, 
            suggested: '',
            checkStrength() {
                let s = 0;
                if (this.password.length > 7) s += 1;
                if (this.password.match(/[A-Z]/)) s += 1;
                if (this.password.match(/[0-9]/)) s += 1;
                if (this.password.match(/[^A-Za-z0-9]/)) s += 1;
                this.strength = s;
            },
            generatePassword() {
                const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
                let pass = '';
                // Ensure at least one of each required character type
                pass += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.charAt(Math.floor(Math.random() * 26));
                pass += 'abcdefghijklmnopqrstuvwxyz'.charAt(Math.floor(Math.random() * 26));
                pass += '0123456789'.charAt(Math.floor(Math.random() * 10));
                pass += '!@#$%^&*'.charAt(Math.floor(Math.random() * 8));
                
                for (let i = 0; i < 8; i++) {
                    pass += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                // Shuffle
                this.suggested = pass.split('').sort(() => 0.5 - Math.random()).join('');
            },
            useSuggestion() {
                this.password = this.suggested;
                document.getElementById('update_password_password').value = this.suggested;
                document.getElementById('update_password_password_confirmation').value = this.suggested;
                this.checkStrength();
                this.suggested = ''; // hide suggestion after use
            }
        }">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" :value="__('Current Password')" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('New Password')" />
            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" x-model="password" @input="checkStrength" />
            
            <!-- Password Strength Indicator -->
            <div class="mt-2 h-1.5 w-full bg-gray-200 rounded-full overflow-hidden flex">
                <div class="h-full transition-all duration-300" :class="{ 'w-1/4 bg-red-500': strength === 1, 'w-2/4 bg-yellow-500': strength === 2, 'w-3/4 bg-blue-500': strength === 3, 'w-full bg-green-500': strength >= 4, 'w-0': strength === 0 }"></div>
            </div>
            <p class="text-xs mt-1 font-medium">
                <span x-show="strength === 0" class="text-gray-500">Enter a strong password</span>
                <span x-show="strength === 1" class="text-red-500">Weak</span>
                <span x-show="strength === 2" class="text-yellow-500">Fair</span>
                <span x-show="strength === 3" class="text-blue-500">Good</span>
                <span x-show="strength >= 4" class="text-green-500">Strong</span>
            </p>

            <!-- Password Suggestion -->
            <button type="button" class="mt-2 text-sm text-indigo-600 hover:text-indigo-800 font-bold flex items-center gap-1" @click="generatePassword()">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                Suggest a secure password
            </button>
            <div x-show="suggested !== ''" class="mt-3 p-3 bg-indigo-50 border border-indigo-100 rounded-lg flex justify-between items-center shadow-inner" style="display: none;">
                <span class="font-mono text-indigo-800 font-bold tracking-wider" x-text="suggested"></span>
                <button type="button" @click="useSuggestion()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-md text-xs font-bold transition shadow-sm">Use this</button>
            </div>
            
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
