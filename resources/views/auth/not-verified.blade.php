<x-layouts.focus-center :backButton="false">

    <div class="flex min-h-[80vh] items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">

            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-amber-500/10 border border-amber-500/20 mb-4">
                    <svg class="w-6 h-6 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h1 class="text-3xl font-bold text-white tracking-tight">Verify your email address</h1>
                <p class="mt-3 text-neutral-400 text-sm">This is necessary to continue your registration.</p>
            </div>

            <div class="rounded-2xl border border-white/10 bg-white/5 backdrop-blur-sm shadow-2xl p-8">

                @if(session('sent'))
                    <div class="flex items-center gap-2 rounded-lg border border-emerald-500/30 bg-emerald-500/10 text-emerald-300 text-sm px-4 py-3 mb-6">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        A fresh verification link has been sent to your email address.
                    </div>
                @endif

                <p class="text-neutral-300 text-sm mb-2">Please check your email for a verification link.</p>
                <p class="text-neutral-500 text-sm mb-6">If you did not receive the email, you can resend it.</p>

                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit"
                            class="w-full py-2.5 px-4 rounded-lg bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold transition-colors shadow-lg shadow-blue-900/30">
                        Send another verification email
                    </button>
                </form>

            </div>
        </div>
    </div>

</x-layouts.focus-center>
