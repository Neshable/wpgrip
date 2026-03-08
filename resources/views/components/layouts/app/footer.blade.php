<footer class="border-t border-white/5 bg-[#111111] pt-16 pb-8">
    <div class="max-w-screen-xl mx-auto px-6">
        <div class="flex flex-col lg:flex-row justify-between gap-12">

            {{-- Brand + tagline --}}
            <div class="max-w-xs space-y-6">
                <a href="/">
                    <img src="{{ asset(config('app.logo.dark')) }}" class="h-8" alt="{{ config('app.name') }}" />
                </a>
                <p class="text-sm font-light text-neutral-400 leading-relaxed">
                    All your WordPress sites.<br>
                    One powerful control panel.
                </p>
                <p class="text-xs text-neutral-600">
                    &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                </p>
            </div>

            {{-- Footer links --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-10">
                <div class="space-y-5">
                    <h4 class="text-sm font-medium text-white">Product</h4>
                    <nav class="flex flex-col gap-4">
                        <a href="{{ route('features') }}" class="text-sm text-neutral-400 hover:text-white transition-colors">Features</a>
                        <a href="{{ route('pricing') }}" class="text-sm text-neutral-400 hover:text-white transition-colors">Pricing</a>
                        <a href="{{ route('why-ssh') }}" class="text-sm text-neutral-400 hover:text-white transition-colors">Why SSH</a>
                        <a href="{{ config('app.docs_url', '/docs') }}" class="text-sm text-neutral-400 hover:text-white transition-colors">Documentation</a>
                    </nav>
                </div>
                <div class="space-y-5">
                    <h4 class="text-sm font-medium text-white">Features</h4>
                    <nav class="flex flex-col gap-4">
                        <a href="{{ route('features.uptime-monitoring') }}" class="text-sm text-neutral-400 hover:text-white transition-colors">Uptime Monitoring</a>
                        <a href="{{ route('features.backups') }}" class="text-sm text-neutral-400 hover:text-white transition-colors">Database Backups</a>
                        <a href="{{ route('features.git-deployments') }}" class="text-sm text-neutral-400 hover:text-white transition-colors">Git Deployments</a>
                        <a href="{{ route('features.security') }}" class="text-sm text-neutral-400 hover:text-white transition-colors">Security Scanning</a>
                        <a href="{{ route('features.performance') }}" class="text-sm text-neutral-400 hover:text-white transition-colors">Performance Testing</a>
                    </nav>
                </div>
                <div class="space-y-5">
                    <h4 class="text-sm font-medium text-white">Use Cases</h4>
                    <nav class="flex flex-col gap-4">
                        <a href="{{ route('for-agencies') }}" class="text-sm text-neutral-400 hover:text-white transition-colors">For Agencies</a>
                        <a href="{{ route('for-freelancers') }}" class="text-sm text-neutral-400 hover:text-white transition-colors">For Freelancers</a>
                        <a href="{{ route('for-hosting-companies') }}" class="text-sm text-neutral-400 hover:text-white transition-colors">For Hosting Companies</a>
                    </nav>
                </div>
                <div class="space-y-5">
                    <h4 class="text-sm font-medium text-white">Account</h4>
                    <nav class="flex flex-col gap-4">
                        <a href="{{ route('login') }}" class="text-sm text-neutral-400 hover:text-white transition-colors">Sign In</a>
                        @auth
                            <a href="{{ route('dashboard') }}" class="text-sm text-neutral-400 hover:text-white transition-colors">Dashboard</a>
                        @endauth
                    </nav>
                </div>
                <div class="space-y-5">
                    <h4 class="text-sm font-medium text-white">Legal</h4>
                    <nav class="flex flex-col gap-4">
                        <a href="{{ route('terms-of-service') }}" class="text-sm text-neutral-400 hover:text-white transition-colors">Terms of Service</a>
                        <a href="{{ route('privacy-policy') }}" class="text-sm text-neutral-400 hover:text-white transition-colors">Privacy Policy</a>
                        <a href="{{ route('refund-policy') }}" class="text-sm text-neutral-400 hover:text-white transition-colors">Refund Policy</a>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</footer>
