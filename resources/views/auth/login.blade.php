

<x-layouts.focus-center class="md:!max-w-6xl mx-auto overflow-x-hidden relative" :backButton="false">

    <x-effect.gradient class="!-start-1/3 -top-50 !w-2/4"/>
    <x-effect.gradient class="!-end-1/3 -top-72 !w-2/4"/>

    <div class="mx-auto md:max-w-6xl text-center mt-8">
        <div class="mx-4">
   
            <x-heading.h1 class="!text-3xl md:!text-4xl !font-semibold">
                {{ __('Welcome back') }}
            </x-heading.h1>
            <p class="mt-4">
                {{__('New to WPGrip?')}} <a class="text-primary-500 font-bold" href="{{ route('register') }}">{{__('Create an account')}}</a>
            </p>

            <div class="card text-center mx-auto md:max-w-xl mt-8 bg-base-100 shadow-xl p-4 md:p-8">
                <form method="POST" action="{{ route('login') }}">
                    @csrf
        
        
                    <x-input.field label="{{ __('Email Address') }}" type="email" name="email"
                                   value="{{ old('email') }}" required autofocus="true" class="my-2"
                                   autocomplete="email" max-width="w-full"/>
                    @error('email')
                    <span class="text-xs text-red-500" role="alert">
                            {{ $message }}
                        </span>
                    @enderror
        
                    <x-input.field label="{{ __('Password') }}" type="password" name="password" required class="my-2"  max-width="w-full"/>
        
                    @error('password')
                        <span class="text-xs text-red-500" role="alert">
                            {{ $message }}
                        </span>
                    @enderror
        
                    @if (config('app.recaptcha_enabled'))
                        <div class="my-4">
                            {!! htmlFormSnippet() !!}
                        </div>
        
                        @error('g-recaptcha-response')
                            <span class="text-xs text-red-500" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
        
                        @push('tail')
                            {!! htmlScriptTagJsApi() !!}
                        @endpush
        
                    @endif
        
                    <div class="my-3 flex flex-wrap gap-2 justify-between text-sm">
                        <div class="flex gap-2">
                            <input class="checkbox checkbox-sm" type="checkbox" name="remember"
                                   id="remember" {{ old('remember') ? 'checked' : '' }}>
        
                            <label class="text-sm" for="remember">
                                {{ __('Remember Me') }}
                            </label>
                        </div>
                        <div>
                            @if (Route::has('password.request'))
                                <a class="text-primary-500 text-xs" href="{{ route('password.request') }}">
                                    {{ __('Forgot Your Password?') }}
                                </a>
                            @endif
                        </div>
                    </div>
        
                    <x-button-link.primary class="inline-block !w-full my-2" elementType="button" type="submit">
                        {{ __('Login') }}
                    </x-button-link.primary>
        
        
        
                    <x-auth.social-login>
                        <x-slot name="before">
                            <div class="flex flex-col w-full">
                                <div class="divider">{{ __('or') }}</div>
                            </div>
                        </x-slot>
                    </x-auth.social-login>
        
                </form>
            </div>

        </div>
    </div>

</x-layouts.focus-center>
