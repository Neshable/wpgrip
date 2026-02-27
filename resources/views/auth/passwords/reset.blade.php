<x-layouts.focus-center :backButton="false">

    <div class="flex min-h-[80vh] items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">

            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-white tracking-tight">Choose a new password</h1>
                <p class="mt-3 text-neutral-400 text-sm">
                    <a href="{{ route('login') }}" class="text-blue-400 hover:text-blue-300 transition-colors">Back to login</a>
                </p>
            </div>

            <div class="rounded-2xl border border-white/10 bg-white/5 backdrop-blur-sm shadow-2xl p-8">
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="mb-4">
                        <label class="block text-xs font-medium text-neutral-400 mb-1.5" for="email">Email address</label>
                        <input id="email" type="email" name="email" value="{{ $email ?? old('email') }}"
                               required autofocus autocomplete="email"
                               class="w-full rounded-lg bg-white/5 border border-white/10 text-neutral-100 placeholder-neutral-600 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/60 focus:border-blue-500/40 transition" />
                        @error('email')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-medium text-neutral-400 mb-1.5" for="password">New password</label>
                        <input id="password" type="password" name="password" required autocomplete="new-password"
                               class="w-full rounded-lg bg-white/5 border border-white/10 text-neutral-100 placeholder-neutral-600 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/60 focus:border-blue-500/40 transition" />
                        @error('password')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-xs font-medium text-neutral-400 mb-1.5" for="password_confirmation">Confirm new password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                               class="w-full rounded-lg bg-white/5 border border-white/10 text-neutral-100 placeholder-neutral-600 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/60 focus:border-blue-500/40 transition" />
                    </div>

                    <button type="submit"
                            class="w-full py-2.5 px-4 rounded-lg bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold transition-colors shadow-lg shadow-blue-900/30">
                        Reset password
                    </button>
                </form>
            </div>

        </div>
    </div>

</x-layouts.focus-center>
