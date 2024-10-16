<x-layouts.app>
    <x-slot name="title">
        {{ __('WPGRIP ') }}
    </x-slot>

    <x-section.hero class="w-full mb-8 md:mb-72 bg-gradient-to-b from-gray-50 to-white">

        <div class="mx-auto text-center h-160 md:h-180 px-4">
            <x-pill class="text-black font-bold bg-primary-50">
                Tailor-Made for Developers. No Plugins. No Complications.
            </x-pill>

            <x-heading.h1 class="mt-4 font-bold">
                All Your WordPress Sites.
                <br class="hidden sm:block">
                One Control Panel.
            </x-heading.h1>

            <p class="text-black m-3">
                Gain complete control with an encrypted SSH connection and streamlined experience.<br>
                Manage plugins, track performance, and keep your sites secure — all from one powerful dashboard.
            </p>

            <div class="flex flex-wrap gap-4 justify-center flex-col md:flex-row mt-6">

                <x-button-link.primary href="/register" class="self-center !py-3" elementType="a">
                    {{ __('Start Your Free Trial') }}
                </x-button-link.primary>
                <x-button-link.primary-outline href="/pricing"
                    class=" bg-transparent self-center !py-3 border-primary-500">
                    {{ __('Check Pricing') }}
                </x-button-link.primary-outline>

            </div>

            <svg class="hidden lg:block absolute right-full transform translate-x-1/2 translate-y-12" width="404"
                height="784" fill="none" viewBox="0 0 404 784" aria-hidden="true">
                <defs>
                    <pattern id="64e643ad-2176-4f86-b3d7-f2c5da3b6a6d" x="0" y="0" width="20" height="20"
                        patternUnits="userSpaceOnUse">
                        <rect x="0" y="0" width="4" height="4" class="text-gray-200" fill="currentColor">
                        </rect>
                    </pattern>
                </defs>
                <rect width="404" height="784" fill="url(#64e643ad-2176-4f86-b3d7-f2c5da3b6a6d)"></rect>
            </svg>

            {{-- <x-effect.glow></x-effect.glow> --}}


            <div class="mx-auto md:max-w-3xl lg:max-w-5xl">
                <img class="drop-shadow-2xl mt-8 transition rounded-2xl"
                    src="{{ URL::asset('/images/features/wphusk_dashboard.jpg') }}" />
            </div>

        </div>
    </x-section.hero>



    <div class="py-16 bg-gray-50 overflow-hidden lg:py-24">
        <div class="relative max-w-xl mx-auto px-4 sm:px-6 lg:px-8 lg:max-w-7xl">
            <svg class="hidden lg:block absolute left-full transform -translate-x-1/2 -translate-y-1/4" width="404"
                height="784" fill="none" viewBox="0 0 404 784" aria-hidden="true">
                <defs>
                    <pattern id="b1e6e422-73f8-40a6-b5d9-c8586e37e0e7" x="0" y="0" width="20" height="20"
                        patternUnits="userSpaceOnUse">
                        <rect x="0" y="0" width="4" height="4" class="text-gray-200" fill="currentColor">
                        </rect>
                    </pattern>
                </defs>
                <rect width="404" height="784" fill="url(#b1e6e422-73f8-40a6-b5d9-c8586e37e0e7)"></rect>
            </svg>

            <div class="relative">

                <x-heading.h2 class="mt-2 text-3xl text-center font-extrabold">
                    Different Tool. Modern Approach.
                </x-heading.h2>

                <p class="mt-4 max-w-3xl mx-auto text-center text-xl text-gray-500">
                    Most WordPress management tools require installing multiple plugins that can slow down your site and
                    introduce new security risks. Our solution is different. By connecting directly via SSH and using
                    WP-CLI commands, we eliminate the need for extra plugins, giving you complete control without added
                    vulnerabilities. This means faster performance, enhanced security, and a direct, efficient way to
                    manage all your WordPress sites — perfect for developers and agencies.
                </p>
            </div>


            <x-section.columns class="max-w-none md:max-w-6xl pt-16 items-center" id="features">
                <x-section.column>
                    <div>
                        <x-heading.h2>
                            GIT Deployments
                        </x-heading.h2>
                    </div>

                    <p class="mt-4">
                        Add and manage multiple plugin or theme reposoitories and share them across your sites.
                        Manually deploy at any time of day or night with confidence, or use automated webhooks
                        to trigger the deployments on new commit.
                    </p>


                </x-section.column>

                <x-section.column>
                    <img src="{{ URL::asset('/images/features/git.svg') }}" dir="right"></img>
                </x-section.column>

            </x-section.columns>

            <x-section.columns class="max-w-none md:max-w-6xl  flex-wrap-reverse">
                <x-section.column>
                    <img src="{{ URL::asset('/images/features/performance.svg') }}"></img>
                </x-section.column>

                <x-section.column>
                    <div>
                        <x-heading.h2>
                            Performance Efficiency
                        </x-heading.h2>
                    </div>

                    <p class="mt-4">
                        Experience lightning-fast WordPress site management with WP-CLI executed over secure SSH
                        connections. WPGrip provides 2x faster efficiency compared to traditional methods, ensuring your
                        sites run seamlessly without adding any strain to your WordPress frontend.
                    </p>
                </x-section.column>

            </x-section.columns>

            <x-section.columns class="max-w-none md:max-w-6xl mt-6">
                <x-section.column>
                    <div x-intersect="$el.classList.add('slide-in-top')">
                        <x-heading.h2>
                            No Plugins Needed
                        </x-heading.h2>
                    </div>

                    <p class="mt-4">
                        Start managing your WordPress sites right away without the hassle of installing extra plugins.
                        With WPGrip, you get a unique SSH key that you simply add to your hosting—giving you instant,
                        secure control and reducing ongoing maintenance headaches. It's easy, fast, and puts you in
                        control from day one.
                    </p>
                </x-section.column>

                <x-section.column>
                    <img src="{{ URL::asset('/images/features/security.svg') }}" />
                </x-section.column>

            </x-section.columns>

            <svg class="hidden lg:block absolute right-full transform translate-x-1/2 translate-y-12" width="404"
                height="784" fill="none" viewBox="0 0 404 784" aria-hidden="true">
                <defs>
                    <pattern id="64e643ad-2176-4f86-b3d7-f2c5da3b6a6d" x="0" y="0" width="20" height="20"
                        patternUnits="userSpaceOnUse">
                        <rect x="0" y="0" width="4" height="4" class="text-gray-200" fill="currentColor">
                        </rect>
                    </pattern>
                </defs>
                <rect width="404" height="784" fill="url(#64e643ad-2176-4f86-b3d7-f2c5da3b6a6d)"></rect>
            </svg>


        </div>

    </div>

    <div class="bg-white">
        <div
            class="max-w-7xl mx-auto items-center py-16 px-4 sm:px-6 lg:py-24 lg:px-8 lg:grid lg:grid-cols-3 lg:gap-x-32 align-center">
            <div>
                <x-heading.h6 class="text-primary-500 tracking-wide uppercase">
                    Introducing
                </x-heading.h6>
                <x-heading.h2 class="mt-2 text-3xl font-extrabold">
                    Our core features
                </x-heading.h2>
                <p class="mt-4 text-lg text-gray-500">
                    Every plan has access to all our core features.
                </p>
            </div>


            <div class="mt-4 sm:mt-8 md:mt-10 md:grid md:grid-cols-2 md:gap-x-8 xl:mt-0 lg:col-span-2">
                <ul class="divide-y divide-gray-200 -mt-4">
                    @php
                        $items = [
                            'Uptime Monitoring',
                            'SSL Monitoring',
                            'Domain Monitoring',
                            'Vulnerabilities Monitoring',
                            'Performance Monitoring',
                            'History Stats',
                        ];

                    @endphp

                    @foreach ($items as $item)
                        <li class="py-4 flex">
                            <svg aria-hidden="true" class="flex-shrink-0 h-6 w-6 text-green-500" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"></path>
                            </svg>
                            <span class="ml-3 text-base text-gray-500">
                                {{ $item }}
                            </span>
                        </li>
                    @endforeach
                </ul>
                <ul class="border-t border-gray-200 divide-y divide-gray-200 md:border-t-0 md:-mt-4">
                    @php
                        $items2 = [
                            'Powerful administration',
                            'Fine-Tuned AI Insights',
                            'One-Click Updates',
                            'Git Deployments',
                            'DB Cloud Backups',
                            'Client & Server management',
                        ];
                    @endphp

                    @foreach ($items2 as $item)
                        <li class="py-4 flex">
                            <svg aria-hidden="true" class="flex-shrink-0 h-6 w-6 text-green-500" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"></path>
                            </svg>
                            <span class="ml-3 text-base text-gray-500">
                                {{ $item }}
                            </span>
                        </li>
                    @endforeach

                </ul>
            </div>
        </div>
    </div>

    {{-- CTA AI --}}
    <div class="bg-gradient-to-br from-blue-700 to-blue-900 relative">
        <x-section.columns class="max-w-none items-center md:max-w-6xl mt-6">
            <x-section.column>
                <div>
                    <x-pill class="text-black font-bold mb-8 bg-primary-50">
                        Smarter Site Management. Clear Suggestions.
                    </x-pill>

                    <x-heading.h2 class="text-white mt-6">
                        WPGriP AI Assistant
                    </x-heading.h2>
                </div>

                <p class="mt-4 text-white">
                    WPGrip offers you an AI model specifically tuned for WordPress. Unsure if a plugin could cause
                    problems? Ask our AI. Need a boost for your PageSpeed score? Let AI guide you. Get smart insights
                    from your site data and make informed decisions quickly—your AI assistant is here 24/7 to support
                    you.
                </p>

            </x-section.column>

            <x-section.column>
                <img src="{{ URL::asset('/images/features/ai-white.svg') }}" class="rounded-2xl" />
            </x-section.column>

        </x-section.columns>



    </div>

    {{-- Tabs --}}
    <div class="py-16 bg-gray-50 overflow-hidden lg:py-32">

        <div class="relative max-w-xl mx-auto px-4 sm:px-6 lg:px-8 lg:max-w-7xl">
            <svg class="hidden lg:block absolute left-full transform -translate-x-1/2 -translate-y-1/4" width="404"
                height="784" fill="none" viewBox="0 0 404 784" aria-hidden="true">
                <defs>
                    <pattern id="b1e6e422-73f8-40a6-b5d9-c8586e37e0e7" x="0" y="0" width="20" height="20"
                        patternUnits="userSpaceOnUse">
                        <rect x="0" y="0" width="4" height="4" class="text-gray-200" fill="currentColor">
                        </rect>
                    </pattern>
                </defs>
                <rect width="404" height="784" fill="url(#b1e6e422-73f8-40a6-b5d9-c8586e37e0e7)"></rect>
            </svg>

            <div class="relative">
                <x-heading.h2 class="mt-2 text-3xl text-center font-extrabold">
                    Powerful Features
                </x-heading.h2>
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
                    <x-heading.h3 class="mt-6">
                        Vulnerability Detection
                    </x-heading.h3>
                    <p class="mt-2 text-gray-950">
                        WPGrip has a continuously updated vulnerability database for plugins, themes, and WordPress
                        core. Our
                        system detects if any of your sites are running vulnerable versions, allowing you to act quickly
                        to
                        secure your WordPress ecosystem. This proactive approach helps to mitigate risks and keep your
                        websites
                        safe from potential exploits.
                    </p>
                </x-tab-slider.tab-content>

                <x-tab-slider.tab-content class="text-center mt-8" id="tab-2">
                    <x-heading.h3 class="mt-6">
                        Team Collaboration
                    </x-heading.h3>
                    <p class="mt-2 text-gray-950">Invite team members to your workspace and collaborate effortlessly.
                        WPGrip also
                        makes it
                        possible to be invited to other workspaces, making it easy to manage multiple projects and
                        teams. The
                        platform allows you to organize servers and link them to specific websites, ensuring smooth
                        operations
                        and efficient teamwork. Whether you're a solo developer or part of a larger agency, WPGrip keeps
                        everyone on the same page, making site management more streamlined.</p>
                </x-tab-slider.tab-content>

                <x-tab-slider.tab-content class="text-center mt-8" id="tab-3">
                    <x-heading.h3 class="mt-6">
                        Backups
                    </x-heading.h3>
                    <p class="mt-2 text-gray-950">WPGrip supports encrypted cloud backups that keep your databases
                        secure. With
                        easy
                        scheduling options, you can automate the process and restore backups whenever needed, ensuring
                        your data
                        is always protected and easily recoverable.</p>
                </x-tab-slider.tab-content>

                <x-tab-slider.tab-content class="text-center mt-8" id="tab-4">
                    <x-heading.h3 class="mt-6">
                        Performance Monitoring
                    </x-heading.h3>
                    <p class="mt-2 text-gray-950">WPGrip monitors Google PageSpeed scores for both mobile and desktop,
                        offering
                        real-time
                        insights into your site’s performance. Historical data is saved so you can track trends and make
                        adjustments to enhance user experience over time. This feature ensures that your sites are
                        always
                        optimized and performing at their peak.</p>
                </x-tab-slider.tab-content>

                <x-tab-slider.tab-content id="tab-5">
                    <x-heading.h3 class="mt-6">
                        Direct Management & Deployments
                    </x-heading.h3>
                    <p class="mt-2 text-gray-950">WPGrip makes plugin, theme, and core management straightforward
                        through WP-CLI
                        commands.
                        Whether you need to install, update, or deactivate, WPGrip’s direct command line access makes
                        the
                        process simple and efficient. For deployment, the platform allows seamless integration with
                        GitHub or
                        Bitbucket. You can automate your workflow with webhooks, set up automatic or manual deployments
                        for
                        individual components, and effortlessly roll back to previous versions if required. WPGrip gives
                        you the
                        freedom and control to update your sites in the way that works best for you.</p>

                    <div class="flex gap-3 pt-1 flex-wrap">
                        @svg('colored/github', 'h-12 w-12 py-2 px-2 border border-primary-50 rounded-lg')
                        @svg('colored/gitlab', 'h-12 w-12 py-2 px-2 border border-primary-50 rounded-lg')
                        @svg('colored/bitbucket', 'h-12 w-12 py-2 px-2 border border-primary-50 rounded-lg')
                    </div>
                </x-tab-slider.tab-content>

                <x-tab-slider.tab-content class="text-center mt-8" id="tab-6">
                    <x-heading.h3 class="mt-3">
                        Monitoring & Alerts
                    </x-heading.h3>
                    <p class="mt-2">WPGrip’s monitoring tools provide instant insights into your website's health.
                        With daily
                        regression tests, WPGrip keeps a vigilant eye on any changes that could affect your sites'
                        stability.
                        The platform also provides uptime monitoring and tracks SSL certificate status to ensure your
                        sites
                        remain accessible and secure. When an issue is detected, you receive immediate notifications
                        through
                        email or Slack, so you can act fast. Additionally, WPGrip allows you to monitor up to 3 custom
                        URLs per
                        site, offering comprehensive oversight of every important aspect of your WordPress ecosystem.
                    </p>
                </x-tab-slider.tab-content>
            </x-tab-slider>



        </div>

    </div>


    <div class="relative">
        <div class="max-w-7xl mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:px-8 z-10 relative">
            <x-heading.h2 class="mt-2 text-3xl text-center font-extrabold">
                Got a Question?
            </x-heading.h2>


            <div class="mt-6  border-blue-600 border-opacity-25 pt-10">
                <x-accordion class="mt-4 p-8">

                    <x-accordion.item active="false" name="what-is-wpgrip">
                        <x-slot name="title">What is WPGrip?</x-slot>
                        WPGrip is an all-in-one WordPress management platform that lets you control all your sites from
                        a single, secure dashboard.
                    </x-accordion.item>

                    <x-accordion.item active="false" name="how-does-it-work">
                        <x-slot name="title">How does it work?</x-slot>
                        WPGrip connects directly to your hosting via SSH, executing WP-CLI commands for maximum
                        control. No plugins are required, making it lightweight and secure.
                    </x-accordion.item>

                    <x-accordion.item active="false" name="hosting-support">
                        <x-slot name="title">What type of hosting providers does it support?</x-slot>
                        WPGrip supports any hosting provider that allows SSH access and has WP-CLI installed, which
                        covers almost 99% of the current hosting companies.
                    </x-accordion.item>

                    <x-accordion.item active="false" name="server-support">
                        <x-slot name="title">Are custom servers supported?</x-slot>
                        Not only are custom servers supported, but we actually recommend using a VPS. You need to have a
                        Linux user that is the owner of the WordPress folder and can run any command inside.
                    </x-accordion.item>

                    <x-accordion.item active="false" name="requirements">
                        <x-slot name="title">What are the requirements?</x-slot>
                        To use WPGrip, your server must allow SSH connections and have WP-CLI available for the SSH
                        user.
                    </x-accordion.item>

                    <x-accordion.item active="false" name="security">
                        <x-slot name="title"> How does WPGrip enhance security?</x-slot>
                        By eliminating the need for third-party plugins, WPgrip reduces vulnerabilities often introduced
                        by plugins. Our direct SSH encrypted connections ensure a secure and efficient management
                        experience.
                    </x-accordion.item>

                    <x-accordion.item active="false" name="alternative">
                        <x-slot name="title">What features can I use without SSH access?</x-slot>
                        Your experience will be fairly limited, but you can still use our monitoring tools to check your
                        uptime, SSL status, domain expiry, and performance.

                    </x-accordion.item>
                </x-accordion>
            </div>
        </div>
    </div>

    {{-- <div class="bg-blue-50">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-24 lg:px-8 lg:flex lg:items-center lg:justify-between">
            <h2 class="text-3xl font-extrabold tracking-tight text-blue-900 sm:text-4xl">
                <span class="block">Have to migrate?</span>
                <span class="block text-blue-600">Configure for free.</span>
            </h2>
            <div class="mt-8 flex lg:flex-shrink-0 lg:mt-0">
                <div class="inline-flex rounded-md shadow">
                    <a href="https://move.ploi.app/register" class="bg-blue-600 border border-transparent rounded-md py-3 px-5 inline-flex items-center justify-center text-base font-medium text-white hover:bg-blue-700">
                        Get started
                    </a>
                </div>
            </div>
        </div>
    </div> --}}

    <x-section.outro>
        <x-heading.h6 class="text-primary-50 text-center">
            Ready to dive in?
        </x-heading.h6>
        <x-heading.h2 class="text-primary-50 text-center">
            Start your free trial today
        </x-heading.h2>


        <p class="max-w-3xl text-primary-50 text-center mx-auto mt-4">
            Get access to our all-in-one dashboard today. With features like uptime and SSL monitoring, performance
            insights, git deployments, and powerful AI-driven insights, our platform offers everything you need for
            seamless management.
        </p>

        <div class="mt-10 text-center">

            <x-button-link.secondary href="/register">
                Start Free Trial
            </x-button-link.secondary>
        </div>
    </x-section.outro>


</x-layouts.app>
