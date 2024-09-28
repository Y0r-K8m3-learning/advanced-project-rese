<x-app-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- 全体を中央に配置するためのラッパー -->
    <div class="flex justify-center items-center min-h-screen">
        <div class="w-full max-w-md bg-white p-6 rounded-lg shadow-lg">

            <!-- 登録の見出し -->
            <div class="text-left mb-4 bg-cyan-500 text-black py-2 rounded-lg">
                <h2 class="text-2xl font-bold">{{ __('登録') }}</h2>
            </div>


            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Name -->
                <div class="mb-4">
                    <x-input-label for="name" :value="__('Name')" />
                    <x-text-input id="name" class="block mt-1 w-full border border-gray-300 rounded-lg p-2" type="text" name="name" :value="old('name')" autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Email Address -->
                <div class="mb-4">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full border border-gray-300 rounded-lg p-2" type="email" name="email" :value="old('email')" autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input id="password" class="block mt-1 w-full border border-gray-300 rounded-lg p-2" type="password" name="password" autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>



                <div class="flex items-center justify-end mt-4">
                    <x-primary-button class="ms-4">
                        {{ __('Register') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>