<x-layouts.focus-center :backButton="false">

    <div class="flex min-h-[80vh] items-center justify-center px-4 py-12">
        <div class="w-full max-w-md text-center">

            {{-- success icon --}}
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-500/10 border border-emerald-500/20 mb-6">
                <svg class="w-7 h-7 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>

            <h1 class="text-3xl font-bold text-white tracking-tight mb-3">You're all set!</h1>
            <p class="text-neutral-400 text-sm mb-8">
                Your account is ready to go. Dive in and start exploring WPGrip's powerful features.
            </p>

            <div class="rounded-2xl border border-white/10 bg-white/5 backdrop-blur-sm shadow-2xl p-8">
                <p class="text-neutral-300 text-sm mb-6">We're thrilled to have you with us. Everything is set up and waiting for you.</p>

                <a href="{{ route('home') }}"
                   class="inline-block w-full py-2.5 px-4 rounded-lg bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold text-center transition-colors shadow-lg shadow-blue-900/30">
                    Continue
                </a>
            </div>

            <p class="text-xs text-neutral-600 mt-6">Need help getting started? Visit our support page or check out our quick-start guide.</p>

        </div>
    </div>

</x-layouts.focus-center>
