<form method="POST" action="{{ route('login') }}">
    @csrf
     <!----------------Name--------------------------->
     <div>
        <x-label for="name" :value="__('labels.name')" />

        <x-input id="name" class="block mt-1 w-full" type="text" name="name"  required autofocus />
    </div>

    <!-- Email Address -----------------
    <div>
        <x-label for="email" :value="__('Email')" />

        <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
    </div>
         ----------------------------------->
    <!-- Password -->
    <div class="mt-4">
        <x-label for="password" :value="__('labels.password')" />

        <x-input id="password" class="block mt-1 w-full"
                        type="password"
                        name="password"
                        required autocomplete="current-password" />
    </div>

    <!-- Remember Me -->
    <div class="block mt-4">
        <label for="remember_me" class="inline-flex items-center">
            <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="remember">
            <span class="ml-2 text-sm text-gray-600">{{ __('labels.remember') }}</span>
        </label>
    </div>

    
    <div class="mb-4">
        
      {{---
        @if (Route::has('password.request'))
            <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('password.request') }}">
                {{ __('Forgot your password?') }}
            </a>
        @endif---}}

        <x-button class="text-center" >
            
            {{ __('labels.login') }}
        </x-button>
    </div>
</form>