@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@40,300,1,200" />

@endsection


<x-app-layout>

    <!-- Session Status -->
    <x-auth-session-status class="mb-5" :status="session('status')" />

    <div class="flex items-center justify-center mt-5 ">
        <div class="w-full max-w-md rounded-lg shadow-md">
            <div class="rounded-3 border-bottom  border-end border-white">

                <div class="bg-blue-500 text-white text-xl font-bold p-4 rounded-t-lg  rounded-top">
                    {{ __('Login') }}
                </div>
                <div class="bg-white p-6 rounded-b-lg border border-white">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-4">
                            <div class="flex items-center">
                                <span class="dli-mail"></span>
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
                        </div>

                        <!-- Password -->
                        <div class="mb-4">
                            <div class="flex items-center">
                                <span class="material-symbols-outlined">
                                    lock
                                </span>
                                <x-my-text-input
                                    class="ring-0 focus:outline-none focus:ring-0 focus:border-transparent" id="password"
                                    type="password" name="password" autocomplete="current-password" placeholder="Password" />
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-2 pl-9 ms-4" />
                        </div>

                        <!-- Log in Button -->
                        <div class="w-full flex items-center justify-end">
                            @if (session('error'))
                            <div class="alert-danger pull-left fs-6 w-75">
                                {{ session('error') }}
                            </div>
                            @endif

                            <button class="py-2 px-4 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400">
                                {{ __('Log in') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>