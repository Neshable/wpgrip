<x-layouts.app>
    <x-slot name="title">{{ __('Features — WPGrip') }}</x-slot>

    {{-- Hero --}}
    <x-section.hero class="w-full">
        <div class="mx-auto text-center px-4">
            <x-pill class="text-black font-bold bg-primary-50">
                No Plugins. No Complications.
            </x-pill>
            <x-heading.h1 class="mt-4 font-bold">
                Everything You Need.<br class="hidden sm:block">
                Nothing You Don't.
            </x-heading.h1>
            <p class="text-gray-600 m-3 max-w-2xl mx-auto">
                WPGrip connects directly to your servers via SSH so you get full control without installing a single plugin.
                Here's what's included in every plan.
            </p>
            <div class="flex flex-wrap gap-4 justify-center flex-col md:flex-row mt-6">
                <x-button-link.primary href="/register" class="self-center !py-3" elementType="a">
                    {{ __('Start Your Free Trial') }}
                </x-button-link.primary>
                <x-button-link.primary-outline href="/pricing"
                    class="bg-transparent self-center !py-3 border-primary-500">
                    {{ __('See Pricing') }}
                </x-button-link.primary-outline>
            </div>
        </div>
    </x-section.hero>

    {{-- SSH / No plugins pitch --}}
    <div class="py-16 bg-gray-50 overflow-hidden lg:py-24">
        <div class="relative max-w-xl mx-auto px-4 sm:px-6 lg:px-8 lg:max-w-7xl">

            <div class="relative">
                <x-heading.h2 class="mt-2 text-3xl text-center font-extrabold">
                    Different Tool. Modern Approach.
                </x-heading.h2>
                <p class="mt-4 max-w-3xl mx-auto text-center text-xl text-gray-500">
                    Most WordPress management tools require installing multiple plugins that can slow down your site and
                    introduce new security risks. WPGrip is different — it connects directly via SSH and WP-CLI,
                    eliminating extra plugins and giving you complete control without added vulnerabilities.
                </p>
            </div>

            <x-section.columns class="max-w-none md:max-w-6xl pt-16 items-center" id="git">
                <x-section.column>
                    <x-heading.h2>GIT Deployments</x-heading.h2>
                    <p class="mt-4">
                        Add and manage multiple plugin or theme repositories and share them across your sites.
                        Deploy manually at any time with confidence, or use automated webhooks to trigger
                        deployments on new commits.
                    </p>
                </x-section.column>
                <x-section.column>
                    <img src="{{ URL::asset('/images/features/git.svg') }}" dir="right">
                </x-section.column>
            </x-section.columns>

            <x-section.columns class="max-w-none md:max-w-6xl flex-wrap-reverse" id="performance">
                <x-section.column>
                    <img src="{{ URL::asset('/images/features/performance.svg') }}">
                </x-section.column>
                <x-section.column>
                    <x-heading.h2>Performance Efficiency</x-heading.h2>
                    <p class="mt-4">
                        Experience lightning-fast site management with WP-CLI over secure SSH connections.
                        WPGrip delivers 2× faster efficiency compared to traditional methods, without adding
                        any strain to your WordPress frontend.
                    </p>
                </x-section.column>
            </x-section.columns>

            <x-section.columns class="max-w-none md:max-w-6xl mt-6" id="security">
                <x-section.column>
                    <x-heading.h2>No Plugins Needed</x-heading.h2>
                    <p class="mt-4">
                        Start managing your WordPress sites right away without installing extra plugins.
                        Add a unique SSH key to your hosting and you get instant, secure control — reducing
                        ongoing maintenance headaches from day one.
                    </p>
                </x-section.column>
                <x-section.column>
                    <img src="{{ URL::asset('/images/features/security.svg') }}">
                </x-section.column>
            </x-section.columns>

        </div>
    </div>

    {{-- Core features list --}}
    <div class="bg-white">
        <div class="max-w-7xl mx-auto items-center py-16 px-4 sm:px-6 lg:py-24 lg:px-8 lg:grid lg:grid-cols-3 lg:gap-x-32 align-center">
            <div>
                <x-heading.h6 class="text-primary-500 tracking-wide uppercase">Introducing</x-heading.h6>
                <x-heading.h2 class="mt-2 text-3xl font-extrabold">Our core features</x-heading.h2>
                <p class="mt-4 text-lg text-gray-500">Every plan has access to all our core features.</p>
            </div>
            <div class="mt-4 sm:mt-8 md:mt-10 md:grid md:grid-cols-2 md:gap-x-8 xl:mt-0 lg:col-span-2">
                <ul class="divide-y divide-gray-200 -mt-4">
                    @foreach(['Uptime Monitoring','SSL Monitoring','Domain Monitoring','Vulnerabilities Monitoring','Performance Monitoring','History Stats'] as $item)
                        <li class="py-4 flex">
                            <svg aria-hidden="true" class="flex-shrink-0 h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                            </svg>
                            <span class="ml-3 text-base text-gray-500">{{ $item }}</span>
                        </li>
                    @endforeach
                </ul>
                <ul class="border-t border-gray-200 divide-y divide-gray-200 md:border-t-0 md:-mt-4">
                    @foreach(['Powerful administration','Fine-Tuned AI Insights','One-Click Updates','Git Deployments','DB Cloud Backups','Client & Server management'] as $item)
                        <li class="py-4 flex">
                            <svg aria-hidden="true" class="flex-shrink-0 h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                            </svg>
                            <span class="ml-3 text-base text-gray-500">{{ $item }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    {{-- AI CTA --}}
    <div class="bg-gradient-to-br from-blue-700 to-blue-900 relative">
        <x-section.columns class="max-w-none items-center md:max-w-6xl mt-6">
            <x-section.column>
                <x-pill class="text-black font-bold mb-8 bg-primary-50">
                    Smarter Site Management. Clear Suggestions.
                </x-pill>
                <x-heading.h2 class="text-white mt-6">WPGrip AI Assistant</x-heading.h2>
                <p class="mt-4 text-white">
                    WPGrip offers you an AI model specifically tuned for WordPress. Unsure if a plugin could cause
                    problems? Ask our AI. Need a boost for your PageSpeed score? Let AI guide you. Get smart insights
                    from your site data and make informed decisions quickly — your AI assistant is here 24/7.
                </p>
            </x-section.column>
            <x-section.column>
                <img src="{{ URL::asset('/images/features/ai-white.svg') }}" class="rounded-2xl">
            </x-section.column>
        </x-section.columns>
    </div>

    {{-- Feature tabs --}}
    <div class="py-16 bg-gray-50 overflow-hidden lg:py-32">
        <div class="relative max-w-xl mx-auto px-4 sm:px-6 lg:px-8 lg:max-w-7xl">
            <div class="relative">
                <x-heading.h2 class="mt-2 text-3xl text-center font-extrabold">Powerful Features</x-heading.h2>
            </div>
            <x-tab-slider class="mt-6 md:max-w-6xl">
                <x-slot name="tabNames">
                    <x-tab-slider.tab-name controls="tab-1" active="true">Vulnerabilities</x-tab-slider.tab-name>
                    <x-tab-slider.tab-name controls="tab-2">Team Collaboration</x-tab-slider.tab-name>
                    <x-tab-slider.tab-name controls="tab-3">Performance</x-tab-slider.tab-name>
                    <x-tab-slider.tab-name controls="tab-4">Backups</x-tab-slider.tab-name>
                    <x-tab-slider.tab-name controls="tab-5">Deployments</x-tab-slider.tab-name>
                    <x-tab-slider.tab-name controls="tab-6">Monitoring</x-tab-slider.tab-name>
                </x-slot>
                <x-tab-slider.tab-content class="text-center mt-8" id="tab-1">
                    <x-heading.h3 class="mt-6">Vulnerability Detection</x-heading.h3>
                    <p class="mt-2 text-gray-950">WPGrip has a continuously updated vulnerability database for plugins, themes, and WordPress core. Our system detects if any of your sites are running vulnerable versions, allowing you to act quickly to secure your WordPress ecosystem.</p>
                </x-tab-slider.tab-content>
                <x-tab-slider.tab-content class="text-center mt-8" id="tab-2">
                    <x-heading.h3 class="mt-6">Team Collaboration</x-heading.h3>
                    <p class="mt-2 text-gray-950">Invite team members to your workspace and collaborate effortlessly. WPGrip also makes it possible to be invited to other workspaces, making it easy to manage multiple projects and teams.</p>
                </x-tab-slider.tab-content>
                <x-tab-slider.tab-content class="text-center mt-8" id="tab-3">
                    <x-heading.h3 class="mt-6">Backups</x-heading.h3>
                    <p class="mt-2 text-gray-950">WPGrip supports encrypted cloud backups that keep your databases secure. Schedule automatic backups and restore whenever needed.</p>
                </x-tab-slider.tab-content>
                <x-tab-slider.tab-content class="text-center mt-8" id="tab-4">
                    <x-heading.h3 class="mt-6">Performance Monitoring</x-heading.h3>
                    <p class="mt-2 text-gray-950">WPGrip monitors Google PageSpeed scores for both mobile and desktop, with historical data so you can track trends and optimize over time.</p>
                </x-tab-slider.tab-content>
                <x-tab-slider.tab-content id="tab-5">
                    <x-heading.h3 class="mt-6">Direct Management & Deployments</x-heading.h3>
                    <p class="mt-2 text-gray-950">WPGrip makes plugin, theme, and core management straightforward through WP-CLI. Integrate with GitHub, GitLab, or Bitbucket — automate with webhooks, set up manual or automatic deployments, and roll back to previous versions effortlessly.</p>
                    <div class="flex gap-3 pt-4 flex-wrap">
                        @svg('colored/github', 'h-12 w-12 py-2 px-2 border border-primary-50 rounded-lg')
                        @svg('colored/gitlab', 'h-12 w-12 py-2 px-2 border border-primary-50 rounded-lg')
                        @svg('colored/bitbucket', 'h-12 w-12 py-2 px-2 border border-primary-50 rounded-lg')
                    </div>
                </x-tab-slider.tab-content>
                <x-tab-slider.tab-content class="text-center mt-8" id="tab-6">
                    <x-heading.h3 class="mt-3">Monitoring & Alerts</x-heading.h3>
                    <p class="mt-2">Uptime monitoring, SSL certificate tracking, and daily regression tests keep a vigilant eye on your sites. Get instant notifications via email or Slack when something needs attention.</p>
                </x-tab-slider.tab-content>
            </x-tab-slider>
        </div>
    </div>

    {{-- CTA --}}
    <x-section.outro>
        <x-heading.h6 class="text-primary-50 text-center">Ready to dive in?</x-heading.h6>
        <x-heading.h2 class="text-primary-50 text-center">Start your free trial today</x-heading.h2>
        <p class="max-w-3xl text-primary-50 text-center mx-auto mt-4">
            Get access to our all-in-one dashboard today — uptime monitoring, SSL checks, performance insights,
            git deployments, and AI-driven suggestions, all in one place.
        </p>
        <div class="mt-10 text-center">
            <x-button-link.secondary href="/register">Start Free Trial</x-button-link.secondary>
        </div>
    </x-section.outro>

</x-layouts.app>
