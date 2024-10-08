<x-app-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="flex items-center justify-center min-h-screen">
        <div class="w-full max-w-md bg-white border border-gray-300 rounded-lg p-6 shadow-md">
            <div class="text-center text-xl font-bold mb-4">
                {{ __('Login') }}
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Address -->
                <div class="flex items-center mb-4">
                    <span class="flex items-center pl-3">
                        <svg class="h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20 4H4c-1.11 0-2 .89-2 2v12c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4V8l8 5 8-5v10zm-8-7L4 6h16l-8 5z" />
                        </svg>
                    </span>
                    <x-text-input id="email" class="block w-full border-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:ring-0 pl-3" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="Email" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="flex items-center mb-6">
                    <span class="flex items-center pl-3">
                        <svg class="h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 17a1 1 0 001-1v-3h2v3a1 1 0 002 0v-3h1a1 1 0 100-2h-6a1 1 0 100 2h1v3a1 1 0 001 1zM4 8h2v9H4V8zm4 3h2v6H8v-6zm6 6h-2v-6h2v6zm4-7h-2V8h2v2zM12 3c-1.1 0-2 .9-2 2v3h4V5c0-1.1-.9-2-2-2zm-2-2h4c2.21 0 4 1.79 4 4v3h-2V5h-8v3H6V5c0-2.21 1.79-4 4-4z" />
                        </svg>
                    </span>
                    <x-text-input id="password" class="block w-full border-0 border-b-2 border-gray-300 focus:border-indigo-500 focus:ring-0 pl-3" type="password" name="password" required autocomplete="current-password" placeholder="Password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Log in Button -->
                <div class="flex items-center justify-center">
                    <x-primary-button class="w-full py-2">
                        {{ __('Log in') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>