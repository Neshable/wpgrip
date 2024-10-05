<x-layouts.app>
    <x-slot name="title">
        {{ __('WPGRIP ') }}
    </x-slot>

    <x-section.hero class="w-full mb-8 md:mb-72 bg-gradient-to-b from-gray-50 to-white">

        <div class="mx-auto text-center h-160 md:h-180 px-4">
            <x-pill class="text-black font-bold bg-primary-50">
                No Plugins. No Complications. Just Complete Control.
            </x-pill>

            <x-heading.h1 class="mt-4 font-bold">
                All Your WordPress Sites.
                <br class="hidden sm:block">
                One Dashboard.
            </x-heading.h1>

            <p class="text-black m-3">
                Gain complete control over your WordPress websites with a secure, streamlined experience. <br>
                Manage plugins, optimize performance, and keep your sites secure — all from one powerful dashboard.
            </p>

            <div class="flex flex-wrap gap-4 justify-center flex-col md:flex-row mt-6">

                <x-button-link.secondary href="#pricing" class="self-center !py-3" elementType="a">
                    {{ __('Start Your Free Trial') }}
                </x-button-link.secondary>
                <x-button-link.primary-outline href="//demo.saasykit.com"
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
                    One Unified Dashboard for <br> All Your WordPress Sites
                </x-heading.h2>

                <p class="mt-4 max-w-3xl mx-auto text-center text-xl text-gray-500">
                    WPGrip brings all your WordPress sites together in one unified dashboard, making complex site
                    management easy and efficient. With secure SSH connections, WP-CLI command integration, and zero
                    dependency on extra plugins, WPGrip redefines what's possible for WordPress professionals.
                </p>
            </div>


            <x-section.columns class="max-w-none md:max-w-6xl pt-16 items-center" id="features">
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

                <x-section.column>
                    <img src="{{ URL::asset('/images/features/performance.svg') }}" dir="right"></img>
                </x-section.column>

            </x-section.columns>

            <x-section.columns class="max-w-none md:max-w-6xl  flex-wrap-reverse">
                <x-section.column>
                    <img src="{{ URL::asset('/images/features/security.svg') }}" />
                </x-section.column>

                <x-section.column>
                    <div>
                        <x-heading.h2>
                            Maximum Security
                        </x-heading.h2>
                    </div>

                    <p class="mt-4">
                        Gain peace of mind with WPGrip’s robust security. Our secure SSH connections and encrypted keys
                        eliminate plugin vulnerabilities, keeping your data and sites protected from potential threats.
                        WPGrip also fetches and maintains an up-to-date database of vulnerabilities for WordPress core,
                        plugins, and themes, ensuring that any potential risks are identified and addressed quickly.
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
                    {{-- <img src="{{ URL::asset('/images/features/plans.png') }}" class="rounded-2xl" /> --}}
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
                    {{ __('Redefining') }}
                </x-heading.h6>
                <x-heading.h2 class="mt-2 text-3xl font-extrabold">
                    WordPress <br> Management
                </x-heading.h2>
                <p class="mt-4 text-lg text-gray-500">
                    WPGrip brings all your WordPress sites together in one unified dashboard, making complex site
                    management easy and efficient. With secure SSH connections, WP-CLI command integration, and zero
                    dependency on extra plugins, WPGrip redefines what's possible for WordPress professionals.
                </p>
            </div>


            <div class="mt-4 sm:mt-8 md:mt-10 md:grid md:grid-cols-2 md:gap-x-8 xl:mt-0 lg:col-span-2">
                <ul class="divide-y divide-gray-200 -mt-4">
                    <li class="py-4 flex">
                        <svg aria-hidden="true" class="flex-shrink-0 h-6 w-6 text-green-500" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                            </path>
                        </svg>
                        <span class="ml-3 text-base text-gray-500">
                            Uptime Monitoring
                        </span>
                    </li>
                    <li class="py-4 flex">
                        <svg aria-hidden="true" class="flex-shrink-0 h-6 w-6 text-green-500" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                            </path>
                        </svg>
                        <span class="ml-3 text-base text-gray-500">
                            SSL Monitoring
                        </span>
                    </li>
                    <li class="py-4 flex">
                        <svg aria-hidden="true" class="flex-shrink-0 h-6 w-6 text-green-500" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                            </path>
                        </svg>
                        <span class="ml-3 text-base text-gray-500">
                            Easy Updates
                        </span>
                    </li>
                    <li class="py-4 flex">
                        <svg aria-hidden="true" class="flex-shrink-0 h-6 w-6 text-green-500" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                            </path>
                        </svg>
                        <span class="ml-3 text-base text-gray-500">
                            Server & Hosting Management
                        </span>
                    </li>
                </ul>
                <ul class="border-t border-gray-200 divide-y divide-gray-200 md:border-t-0 md:-mt-4">
                    <li class="py-4 flex">
                        <svg aria-hidden="true" class="flex-shrink-0 h-6 w-6 text-green-500" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                            </path>
                        </svg>
                        <span class="ml-3 text-base text-gray-500">
                            Git deployments
                        </span>
                    </li>
                    <li class="py-4 flex">
                        <svg aria-hidden="true" class="flex-shrink-0 h-6 w-6 text-green-500" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                            </path>
                        </svg>
                        <span class="ml-3 text-base text-gray-500">
                            Database size monitoring
                        </span>
                    </li>
                    <li class="py-4 flex">
                        <svg aria-hidden="true" class="flex-shrink-0 h-6 w-6 text-green-500" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                            </path>
                        </svg>
                        <span class="ml-3 text-base text-gray-500">
                            Page Speed monitoring
                        </span>
                    </li>
                    <li class="py-4 flex">
                        <svg aria-hidden="true" class="flex-shrink-0 h-6 w-6 text-green-500" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                            </path>
                        </svg>
                        <span class="ml-3 text-base text-gray-500">
                            More ...
                        </span>
                    </li>
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
                FAQ
            </x-heading.h2>
   
            <div class="mt-6 border-t border-blue-600 border-opacity-25 pt-10">
                <x-accordion class="mt-4 p-8">
                    <x-accordion.item active="true" name="refund">
                        <x-slot name="title">Do you offer a refund?</x-slot>
                
                        Yes, we do offer a 30 days money back guarantee.
                
                    </x-accordion.item>
                
                    <x-accordion.item active="false" name="trial">
                        <x-slot name="title">Do you offer a trial?</x-slot>
                
                        Yes, we do offer a 30 days free trial.
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


    <div class="bg-gradient-to-b py-16 lg:py-24 from-blue-100 to-white">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 px-4">
            <div class="text-center">
                <x-heading.h6>
                    Take control today and start controlling your WordPress sites with WPGrip
                </x-heading.h6>
                <x-heading.h2>
                    Ready to Harness AI Insights?
                </x-heading.h2>
            </div>

            <div class="max-w-none md:max-w-6xl mx-auto text-center">
                <p class="mt-4">
                    Optimize your WordPress experience with AI-powered insights. WPGrip analyzes performance, predicts
                    plugin conflicts, identifies issues, and reviews logs—empowering you to make data-driven decisions.
                    Our
                    AI model, specifically trained on WordPress data, delivers deep, actionable insights tailored to
                    your
                    needs.
                </p>
                <x-button-link.primary href="/pricing" class=" mt-8">
                    Start Now
                </x-button-link.primary>
            </div>
        </div>
    </div>



</x-layouts.app>
