<x-layouts.focus>
    <x-slot name="left">
        <div class="flex flex-col py-2 md:p-10 gap-4 justify-center h-full items-center">
            <x-heading.h1 class="!text-3xl md:!text-4xl !font-semibold">
                Welcome back
            </x-heading.h1>

            <div class="card w-full md:max-w-xl bg-base-100 shadow-xl p-4 md:p-8">
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <p class="text-xs mt-2 text-end">{{__('No account?')}} <a class="text-primary-500 font-bold" href="{{ route('register') }}">{{__('Register')}}</a></p>

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
    </x-slot>


    <x-slot name="right">
        
        <div class="py-4 md:px-12 md:pt-36 h-full">

            <p class="mt-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2 gap-y-10 gap-x-10 mt-8 lg:mt-12 xl:mt-16">

                    <div class="flex flex-col space-y-4 text-white/75">
                        <div class="flex space-x-4">

                                <span class="w-11 h-11 flex items-center justify-center bg-gray-800 border-t border-gray-600 shadow-xl ring-gray-900 ring-1 rounded-xl">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 01-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.631 8.41m5.96 5.96a14.926 14.926 0 01-5.841 2.58m-.119-8.54a6 6 0 00-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 00-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 01-2.448-2.448 14.9 14.9 0 01.06-.312m-2.24 2.39a4.493 4.493 0 00-1.757 4.306 4.493 4.493 0 004.306-1.758M16.5 9a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path>
                                    </svg>
                                </span>

                            <h3 class="text-lg font-bold self-center text-white font-headline">Fast
                                Provisioning</h3>
                        </div>

                        <p>Quickly provision servers and deploy sites with DigitalOcean, UpCloud, Vultr,
                            Linode,
                            Scaleway, AWS EC2, or Custom VPS.</p>
                    </div>

                    <div class="flex flex-col space-y-4 text-white/75">
                        <div class="flex space-x-4">

                                <span class="w-11 h-11 flex items-center justify-center bg-gray-800 border-t border-gray-600 shadow-xl ring-gray-900 ring-1 rounded-xl text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8.25V18a2.25 2.25 0 002.25 2.25h13.5A2.25 2.25 0 0021 18V8.25m-18 0V6a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 6v2.25m-18 0h18M5.25 6h.008v.008H5.25V6zM7.5 6h.008v.008H7.5V6zm2.25 0h.008v.008H9.75V6z"></path>
                                    </svg>
                                </span>

                            <h3 class="text-lg font-bold self-center text-white font-headline">Accessible
                                Interface</h3>

                        </div>

                        <p>A UI that is easy on the eyes. Clean, easy to use, fantastic. Built by
                            developers,
                            for developers.</p>
                    </div>

                    <div class="flex flex-col space-y-4 text-white/75">
                        <div class="flex space-x-4">
                                <span class="w-11 h-11 flex items-center justify-center bg-gray-800 border-t border-gray-600 shadow-xl ring-gray-900 ring-1 rounded-xl text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"></path>
                                    </svg>
                                </span>
                            <h3 class="text-lg font-bold self-center text-white font-headline">SSH Keys</h3>
                        </div>

                        <p>Each server gets its own unique encrypted key-pair generated.</p>
                    </div>

                    <div class="hidden xl:flex flex-col space-y-4 text-white/75">
                        <div class="flex space-x-4">
                                <span class="w-11 h-11 flex items-center justify-center bg-gray-800 border-t border-gray-600 shadow-xl ring-gray-900 ring-1 rounded-xl text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 011.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.56.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.893.149c-.425.07-.765.383-.93.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 01-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.397.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 01-.12-1.45l.527-.737c.25-.35.273-.806.108-1.204-.165-.397-.505-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.107-1.204l-.527-.738a1.125 1.125 0 01.12-1.45l.773-.773a1.125 1.125 0 011.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </span>

                            <h3 class="text-lg font-bold self-center text-white font-headline">Easy
                                queues</h3>
                        </div>

                        <p>Forget manually creating supervisor config files, Ploi takes care of it!</p>
                    </div>
                </div>
            </p>
        </ce=>
    </x-slot>

</x-layouts.focus>
