<x-layouts.focus-center class="md:!max-w-6xl mx-auto overflow-x-hidden relative" :backButton="true">

    <x-effect.gradient class="!-start-1/3 -top-50 !w-2/4" />
    <x-effect.gradient class="!-end-1/3 -top-72 !w-2/4" />

    <div class="mx-auto md:max-w-6xl text-center mt-8">
        <div class="mx-4">

            <x-heading.h1 class="!text-3xl md:!text-4xl !font-semibold">
                Get started in minutes
            </x-heading.h1>
            <p class="mt-4">
                Try WPGrip with our free 5-day trial. Cancel anytime.
            </p>


            <div class="card text-center mx-auto md:max-w-xl mt-8 bg-base-100 shadow-xl p-4 md:p-8">

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <p class="text-xs mt-2 text-end">{{ __('Have an account?') }} <a class="text-primary-500 font-bold"
                            href="{{ route('login') }}">{{ __('Login') }}</a></p>

                    <x-input.field label="{{ __('Name') }}" type="text" name="name" value="{{ old('name') }}"
                        required autofocus="true" class="my-2" autocomplete="name" max-width="w-full" />

                    @error('name')
                        <span class="text-xs text-red-500" role="alert">
                            {{ $message }}
                        </span>
                    @enderror

                    <x-input.field label="{{ __('Email Address') }}" type="email" name="email"
                        value="{{ old('email') }}" required class="my-2" autocomplete="email" max-width="w-full" />
                    @error('email')
                        <span class="text-xs text-red-500" role="alert">
                            {{ $message }}
                        </span>
                    @enderror

                    <x-input.field label="{{ __('Password') }}" type="password" name="password" required class="my-2"
                        max-width="w-full" />

                    @error('password')
                        <span class="text-xs text-red-500" role="alert">
                            {{ $message }}
                        </span>
                    @enderror

                    <x-input.field label="{{ __('Confirm Password') }}" type="password" name="password_confirmation"
                        required class="my-2" max-width="w-full" />

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


                    <p class="text-left text-xs mt-4 mb-4">
                        By signing up, you agree to our Terms of Service and Privacy Policy. You also agree to receive
                        account-related emails from RunCloud, including tips and product updates.
                    </p>

                    <x-button-link.primary class="inline-block !w-full my-2" elementType="button" type="submit">
                        {{ __('Register now') }}
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
