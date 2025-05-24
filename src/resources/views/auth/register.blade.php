@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@40,300,1,200" />

<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

@endsection

<x-app-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="flex items-center justify-center mt-5">
        <div class="w-full max-w-md rounded-lg shadow-md">

            <div class="bg-blue-500 text-white text-xl font-bold p-4 rounded-t-lg  rounded-top">
                {{ __('Registration') }}
            </div>

            <div class="bg-white p-6 rounded-b-lg border border-white">
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="flex items-center">
                        <span class="material-symbols-outlined">
                            person
                        </span>

                        <x-my-text-input id="name" class="ring-0 focus:outline-none focus:ring-0 focus:border-transparent" type="text" name="name" :value="old('name')" autofocus autocomplete="name"
                            placeholder="UserName" />

                        </span>
                        <x-input-error :messages="$errors->get('name')" class="mt-2 pl-9 ms-4" />
                    </div>

                    <div class="mb-4">
                        <div class="flex items-center">
                            <span class="dli-mail"></span>
                            <!-- メールアドレス -->
                            <x-my-text-input
                                class="ring-0 focus:outline-none focus:ring-0 focus:border-transparent"
                                id="email"
                                type="email"
                                name="email"
                                :value="old('email')"
                                autofocus
                                autocomplete="username"
                                placeholder="Email" />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2 pl-9 ms-4" />
                        <div class="mb-4">
                            <div class="flex items-center">
                                <span class="material-symbols-outlined">
                                    lock
                                </span>
                                <!-- パスワード -->
                                <x-my-text-input id="password" class="ring-0 focus:outline-none focus:ring-0 focus:border-transparent" type="password" name="password" autocomplete="new-password" placeholder="Password" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-2 pl-9 ms-4" />
                        </div>


                        <div class="w-full flex items-center justify-end mt-4">
                            <button class="py-2 px-4 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400">
                                {{ __('Register') }}
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>