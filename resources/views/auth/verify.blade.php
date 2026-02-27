<x-layouts.focus-center :backButton="false">

    <div class="flex min-h-[80vh] items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">

            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-blue-500/10 border border-blue-500/20 mb-4">
                    <svg class="w-6 h-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <h1 class="text-3xl font-bold text-white tracking-tight">Verify your email</h1>
                <p class="mt-3 text-neutral-400 text-sm">This is necessary to continue your registration.</p>
            </div>

            <div class="rounded-2xl border border-white/10 bg-white/5 backdrop-blur-sm shadow-2xl p-8">

                @if(session('sent'))
                    <div class="flex items-center gap-2 rounded-lg border border-emerald-500/30 bg-emerald-500/10 text-emerald-300 text-sm px-4 py-3 mb-6">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        A fresh verification link has been sent to your email address.
                    </div>
                @endif

                <p class="text-neutral-300 text-sm mb-2">Please check your inbox for a verification link.</p>
                <p class="text-neutral-500 text-sm mb-6">Didn't receive it? Click below to resend.</p>

                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit"
                            class="w-full py-2.5 px-4 rounded-lg bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold transition-colors shadow-lg shadow-blue-900/30">
                        Resend verification email
                    </button>
                </form>

            </div>
        </div>
    </div>

</x-layouts.focus-center>
