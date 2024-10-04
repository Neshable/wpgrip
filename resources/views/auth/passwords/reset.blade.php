

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
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">

                    <x-input.field label="{{ __('Email Address') }}" type="email" name="email"
                                   value="{{ $email ?? old('email') }}" required autofocus="true" class="my-2"
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

                    <x-input.field label="{{ __('Confirm Password') }}" type="password" name="password_confirmation" required class="my-2"  max-width="w-full"/>

                    @error('password')
                    <span class="text-xs text-red-500" role="alert">
                            {{ $message }}
                        </span>
                    @enderror

                    <x-button-link.primary class="inline-block !w-full my-2" elementType="button" type="submit">
                        {{ __('Reset Password') }}
                    </x-button-link.primary>

                </form>
            </div>

        </div>
    </div>

</x-layouts.focus-center>


