<x-layouts.focus-center :backButton="false">

    <div class="flex min-h-[80vh] items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">

            {{-- heading --}}
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-white tracking-tight">Welcome back</h1>
                <p class="mt-3 text-neutral-400 text-sm">
                    New to WPGrip?
                    <a href="{{ route('register') }}" class="text-blue-400 hover:text-blue-300 font-semibold transition-colors">Create an account</a>
                </p>
            </div>

            {{-- glass card --}}
            <div class="rounded-2xl border border-white/10 bg-white/5 backdrop-blur-sm shadow-2xl p-8">
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    {{-- Email --}}
                    <div class="mb-4">
                        <label class="block text-xs font-medium text-neutral-400 mb-1.5" for="email">Email address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}"
                               required autofocus autocomplete="email"
                               class="w-full rounded-lg bg-white/5 border border-white/10 text-neutral-100 placeholder-neutral-600 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/60 focus:border-blue-500/40 transition" />
                        @error('email')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="mb-4">
                        <label class="block text-xs font-medium text-neutral-400 mb-1.5" for="password">Password</label>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                               class="w-full rounded-lg bg-white/5 border border-white/10 text-neutral-100 placeholder-neutral-600 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/60 focus:border-blue-500/40 transition" />
                        @error('password')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    @if(config('app.recaptcha_enabled'))
                        <div class="my-4">{!! htmlFormSnippet() !!}</div>
                        @error('g-recaptcha-response')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                        @push('tail'){!! htmlScriptTagJsApi() !!}@endpush
                    @endif

                    {{-- Remember + Forgot --}}
                    <div class="flex items-center justify-between mb-6 text-sm">
                        <label class="flex items-center gap-2 text-neutral-400 cursor-pointer select-none">
                            <input type="checkbox" name="remember" class="rounded border-white/20 bg-white/5 text-blue-500 focus:ring-blue-500/40" {{ old('remember') ? 'checked' : '' }}>
                            Remember me
                        </label>
                        @if(Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-xs text-blue-400 hover:text-blue-300 transition-colors">Forgot password?</a>
                        @endif
                    </div>

                    <button type="submit"
                            class="w-full py-2.5 px-4 rounded-lg bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold transition-colors shadow-lg shadow-blue-900/30">
                        Sign in
                    </button>

                    <x-auth.social-login>
                        <x-slot name="before">
                            <div class="relative my-6">
                                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-white/10"></div></div>
                                <div class="relative flex justify-center text-xs"><span class="bg-transparent px-3 text-neutral-500">or continue with</span></div>
                            </div>
                        </x-slot>
                    </x-auth.social-login>

                </form>
            </div>

        </div>
    </div>

</x-layouts.focus-center>
