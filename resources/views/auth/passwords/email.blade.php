

<x-layouts.focus-center class="md:!max-w-6xl mx-auto overflow-x-hidden relative" :backButton="false">

    <x-effect.gradient class="!-start-1/3 -top-50 !w-2/4"/>
    <x-effect.gradient class="!-end-1/3 -top-72 !w-2/4"/>

    <div class="mx-auto md:max-w-6xl text-center mt-8">
        <div class="mx-4">
   
            <x-heading.h1 class="!text-3xl md:!text-4xl !font-semibold">
                {{ __('Reset Your Password') }}
            </x-heading.h1>
            <p class="mt-4">
                {{__('You will receive an email with a link to reset your password.')}} <a class="text-primary-500 font-bold" href="{{ route('login') }}">{{__('Back to login')}}</a>
            </p>

            <div class="card text-center mx-auto md:max-w-xl mt-8 bg-base-100 shadow-xl p-4 md:p-8">
                @if (session('status'))
                <div role="alert" class="alert my-4 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <x-input.field label="{{ __('Email Address') }}" type="email" name="email"
                               value="{{ old('email') }}" required autofocus="true" class="my-2"
                               autocomplete="email" max-width="w-full"/>

                @error('email')
                    <span class="text-xs text-red-500" role="alert">
                        {{ $message }}
                    </span>
                @enderror

                @if (config('app.recaptcha_enabled'))
                    <div class="my-4">
                        {!! htmlFormSnippet() !!} <!-- reCAPTCHA widget -->
                    </div>

                    @error('g-recaptcha-response')
                        <span class="text-xs text-red-500" role="alert">
                            {{ $message }}
                        </span>
                    @enderror
                @endif

                <x-button-link.primary class="inline-block !w-full my-2" elementType="button" type="submit">
                    {{ __('Send Password Reset Link') }}
                </x-button-link.primary>

            </form>
            </div>

        </div>
    </div>

    @if (config('app.recaptcha_enabled'))
        @push('tail')
            {!! htmlScriptTagJsApi() !!} <!-- Include reCAPTCHA script -->
        @endpush
    @endif

</x-layouts.focus-center>

