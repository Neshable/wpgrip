<x-layouts.focus-center :backButton="true">

    <div class="flex min-h-[80vh] items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">

            {{-- heading --}}
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-white tracking-tight">Coming Soon</h1>
                <p class="mt-3 text-neutral-400 text-sm">
                    WPGrip is launching soon. Join the waitlist to be the first to know.
                </p>
            </div>

            {{-- glass card --}}
            <div class="rounded-2xl border border-white/10 bg-white/5 backdrop-blur-sm shadow-2xl p-8">

                @if (session('success'))
                    <div class="mb-6 rounded-lg border border-green-500/20 bg-green-500/10 p-4 text-center">
                        <svg class="mx-auto mb-2 h-8 w-8 text-green-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <p class="text-sm font-medium text-green-300">{{ session('success') }}</p>
                    </div>
                @else
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        {{-- Email --}}
                        <div class="mb-4">
                            <label class="block text-xs font-medium text-neutral-400 mb-1.5" for="email">Email address</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}"
                                   required autofocus autocomplete="email"
                                   placeholder="you@example.com"
                                   class="w-full rounded-lg bg-white/5 border border-white/10 text-neutral-100 placeholder-neutral-600 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/60 focus:border-blue-500/40 transition" />
                            @error('email')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                                class="w-full py-2.5 px-4 rounded-lg bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold transition-colors shadow-lg shadow-blue-900/30">
                            Join Waitlist
                        </button>
                    </form>
                @endif

                <p class="mt-5 text-center text-xs text-neutral-500">
                    Already have an account?
                    <a href="{{ route('login') }}" class="text-blue-400 hover:text-blue-300 font-semibold transition-colors">Sign in</a>
                </p>

            </div>

        </div>
    </div>

</x-layouts.focus-center>
