<x-layouts.focus-center :backButton="false">

    <div class="flex min-h-[80vh] items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">

            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-white tracking-tight">Reset your password</h1>
                <p class="mt-3 text-neutral-400 text-sm">
                    We'll send a reset link to your email.
                    <a href="{{ route('login') }}" class="text-blue-400 hover:text-blue-300 transition-colors">Back to login</a>
                </p>
            </div>

            <div class="rounded-2xl border border-white/10 bg-white/5 backdrop-blur-sm shadow-2xl p-8">

                @if(session('status'))
                    <div class="flex items-center gap-2 rounded-lg border border-emerald-500/30 bg-emerald-500/10 text-emerald-300 text-sm px-4 py-3 mb-6">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div class="mb-5">
                        <label class="block text-xs font-medium text-neutral-400 mb-1.5" for="email">Email address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}"
                               required autofocus autocomplete="email"
                               class="w-full rounded-lg bg-white/5 border border-white/10 text-neutral-100 placeholder-neutral-600 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/60 focus:border-blue-500/40 transition" />
                        @error('email')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>

                    @if(config('app.recaptcha_enabled'))
                        <div class="my-4">{!! htmlFormSnippet() !!}</div>
                        @error('g-recaptcha-response')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    @endif

                    <button type="submit"
                            class="w-full py-2.5 px-4 rounded-lg bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold transition-colors shadow-lg shadow-blue-900/30">
                        Send reset link
                    </button>
                </form>

            </div>
        </div>
    </div>

    @if(config('app.recaptcha_enabled'))
        @push('tail'){!! htmlScriptTagJsApi() !!}@endpush
    @endif

</x-layouts.focus-center>
