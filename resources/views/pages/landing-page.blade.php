<x-layouts.app>
        <div class="relative bg-gray-50 overflow-hidden">
            {{-- dot-grid background decorations --}}
            <div aria-hidden="true" class="hidden lg:block lg:absolute lg:inset-y-0 lg:h-full lg:w-full pointer-events-none">
                <div class="relative h-full max-w-7xl mx-auto">
                    <svg class="absolute right-full transform translate-y-1/4 translate-x-1/4 lg:translate-x-1/2" fill="none" height="784" viewBox="0 0 404 784" width="404">
                        <defs><pattern id="dots-l" height="20" patternUnits="userSpaceOnUse" width="20" x="0" y="0"><rect class="text-gray-200" fill="currentColor" height="4" width="4" x="0" y="0"></rect></pattern></defs>
                        <rect fill="url(#dots-l)" height="784" width="404"></rect>
                    </svg>
                    <svg class="absolute left-full transform -translate-y-3/4 -translate-x-1/4 md:-translate-y-1/2 lg:-translate-x-1/2" fill="none" height="784" viewBox="0 0 404 784" width="404">
                        <defs><pattern id="dots-r" height="20" patternUnits="userSpaceOnUse" width="20" x="0" y="0"><rect class="text-gray-200" fill="currentColor" height="4" width="4" x="0" y="0"></rect></pattern></defs>
                        <rect fill="url(#dots-r)" height="784" width="404"></rect>
                    </svg>
                </div>
            </div>

            <div class="relative pt-10 pb-12 sm:pb-16">
                <main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="lg:grid lg:grid-cols-2 lg:gap-12 lg:items-center">

                        {{-- Left: text + CTAs --}}
                        <div class="text-center lg:text-left">
                            <x-pill class="text-primary-500 bg-primary-50">{{ __('ALL YOUR WORDPRESS SITES. ONE DASHBOARD.') }}</x-pill>
                            <x-heading.h1 class="mt-4 font-bold">
                                {{ __('Easily manage and control') }}
                                <br class="hidden sm:block">
                                {{ __('your WordPress sites') }}
                            </x-heading.h1>
                            <p class="text-primary-50 mt-4 text-lg">
                                Gain 100% control and streamline operations with SSH connections and WP-CLI with 2x faster experience.
                            </p>
                            <div class="flex flex-wrap gap-4 justify-center lg:justify-start flex-col sm:flex-row mt-8">
                                <x-effect.glow></x-effect.glow>
                                <x-button-link.secondary href="#pricing" class="self-center !py-3" elementType="a">
                                    {{ __('Start Your Free Trial') }}
                                </x-button-link.secondary>
                                <x-button-link.primary-outline href="//demo.saasykit.com" class="bg-transparent self-center !py-3 text-white border-white" rel="nofollow">
                                    {{ __('Check Pricing') }}
                                </x-button-link.primary-outline>
                            </div>
                        </div>

                        {{-- Right: SVG illustration --}}
                        <div class="mt-10 lg:mt-0 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 500" class="w-full max-w-lg drop-shadow-xl">
                                <defs>
                                    <filter id="hero-glow" x="-20%" y="-20%" width="140%" height="140%">
                                        <feGaussianBlur stdDeviation="5" result="blur" />
                                        <feComposite in="SourceGraphic" in2="blur" operator="over" />
                                    </filter>
                                    <linearGradient id="hero-hubGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" stop-color="#1e293b" />
                                        <stop offset="100%" stop-color="#0f172a" />
                                    </linearGradient>
                                    <linearGradient id="hero-siteGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" stop-color="#ffffff" />
                                        <stop offset="100%" stop-color="#f8fafc" />
                                    </linearGradient>
                                </defs>
                                <style>
                                    .hero-ssh-line { stroke: #94a3b8; stroke-width: 2; stroke-dasharray: 6 6; animation: heroDash 20s linear infinite; }
                                    .hero-packet   { fill: #10b981; filter: drop-shadow(0 0 4px #10b981); }
                                    @keyframes heroDash { to { stroke-dashoffset: -200; } }
                                </style>

                                <rect width="100%" height="100%" fill="transparent" />

                                {{-- Connection lines --}}
                                <path d="M 400 250 L 180 120" class="hero-ssh-line" />
                                <path d="M 400 250 L 620 120" class="hero-ssh-line" />
                                <path d="M 400 250 L 180 380" class="hero-ssh-line" />
                                <path d="M 400 250 L 620 380" class="hero-ssh-line" />

                                {{-- Animated data packets --}}
                                <circle r="4" class="hero-packet"><animateMotion dur="3s"   repeatCount="indefinite" path="M 400 250 L 180 120" /></circle>
                                <circle r="4" class="hero-packet"><animateMotion dur="4s"   repeatCount="indefinite" path="M 620 120 L 400 250" /></circle>
                                <circle r="4" class="hero-packet"><animateMotion dur="3.5s" repeatCount="indefinite" path="M 400 250 L 180 380" /></circle>
                                <circle r="4" class="hero-packet"><animateMotion dur="2.5s" repeatCount="indefinite" path="M 620 380 L 400 250" /></circle>

                                {{-- Lock icons on lines --}}
                                <g fill="#10b981">
                                    <g transform="translate(270,165) scale(0.6)"><rect x="10" y="12" width="16" height="12" rx="2"/><path d="M12 12 V 8 A 6 6 0 0 1 24 8 V 12" fill="none" stroke="#10b981" stroke-width="3"/></g>
                                    <g transform="translate(490,165) scale(0.6)"><rect x="10" y="12" width="16" height="12" rx="2"/><path d="M12 12 V 8 A 6 6 0 0 1 24 8 V 12" fill="none" stroke="#10b981" stroke-width="3"/></g>
                                    <g transform="translate(270,295) scale(0.6)"><rect x="10" y="12" width="16" height="12" rx="2"/><path d="M12 12 V 8 A 6 6 0 0 1 24 8 V 12" fill="none" stroke="#10b981" stroke-width="3"/></g>
                                    <g transform="translate(490,295) scale(0.6)"><rect x="10" y="12" width="16" height="12" rx="2"/><path d="M12 12 V 8 A 6 6 0 0 1 24 8 V 12" fill="none" stroke="#10b981" stroke-width="3"/></g>
                                </g>

                                {{-- Site nodes --}}
                                <g transform="translate(130,90)">
                                    <rect width="100" height="60" rx="6" fill="url(#hero-siteGrad)" stroke="#cbd5e1" stroke-width="2"/>
                                    <circle cx="15" cy="15" r="4" fill="#ef4444"/><circle cx="27" cy="15" r="4" fill="#eab308"/><circle cx="39" cy="15" r="4" fill="#22c55e"/>
                                    <rect x="15" y="30" width="70" height="4" rx="2" fill="#cbd5e1"/><rect x="15" y="40" width="50" height="4" rx="2" fill="#cbd5e1"/>
                                    <text x="50" y="78" font-family="sans-serif" font-size="12" font-weight="bold" fill="#475569" text-anchor="middle">Client Site</text>
                                </g>
                                <g transform="translate(570,90)">
                                    <rect width="100" height="60" rx="6" fill="url(#hero-siteGrad)" stroke="#cbd5e1" stroke-width="2"/>
                                    <circle cx="15" cy="15" r="4" fill="#ef4444"/><circle cx="27" cy="15" r="4" fill="#eab308"/><circle cx="39" cy="15" r="4" fill="#22c55e"/>
                                    <rect x="15" y="30" width="70" height="4" rx="2" fill="#cbd5e1"/><rect x="15" y="40" width="40" height="4" rx="2" fill="#cbd5e1"/>
                                    <text x="50" y="78" font-family="sans-serif" font-size="12" font-weight="bold" fill="#475569" text-anchor="middle">Agency Shop</text>
                                </g>
                                <g transform="translate(130,350)">
                                    <rect width="100" height="60" rx="6" fill="url(#hero-siteGrad)" stroke="#cbd5e1" stroke-width="2"/>
                                    <circle cx="15" cy="15" r="4" fill="#ef4444"/><circle cx="27" cy="15" r="4" fill="#eab308"/><circle cx="39" cy="15" r="4" fill="#22c55e"/>
                                    <rect x="15" y="30" width="60" height="4" rx="2" fill="#cbd5e1"/><rect x="15" y="40" width="55" height="4" rx="2" fill="#cbd5e1"/>
                                    <text x="50" y="78" font-family="sans-serif" font-size="12" font-weight="bold" fill="#475569" text-anchor="middle">Staging WP</text>
                                </g>
                                <g transform="translate(570,350)">
                                    <rect width="100" height="60" rx="6" fill="url(#hero-siteGrad)" stroke="#cbd5e1" stroke-width="2"/>
                                    <circle cx="15" cy="15" r="4" fill="#ef4444"/><circle cx="27" cy="15" r="4" fill="#eab308"/><circle cx="39" cy="15" r="4" fill="#22c55e"/>
                                    <rect x="15" y="30" width="75" height="4" rx="2" fill="#cbd5e1"/><rect x="15" y="40" width="30" height="4" rx="2" fill="#cbd5e1"/>
                                    <text x="50" y="78" font-family="sans-serif" font-size="12" font-weight="bold" fill="#475569" text-anchor="middle">Legacy Blog</text>
                                </g>

                                {{-- Central hub --}}
                                <g transform="translate(300,190)">
                                    <rect width="200" height="120" rx="8" fill="url(#hero-hubGrad)" filter="url(#hero-glow)"/>
                                    <rect width="200" height="24" rx="8" fill="#334155"/>
                                    <circle cx="16" cy="12" r="4" fill="#ef4444"/><circle cx="28" cy="12" r="4" fill="#eab308"/><circle cx="40" cy="12" r="4" fill="#22c55e"/>
                                    <text x="16" y="50"  font-family="monospace" font-size="12" fill="#10b981">~ wp-grip connect</text>
                                    <text x="16" y="70"  font-family="monospace" font-size="12" fill="#94a3b8">Establishing SSH...</text>
                                    <text x="16" y="90"  font-family="monospace" font-size="12" fill="#38bdf8">Monitoring active.</text>
                                    <text x="100" y="145" font-family="sans-serif" font-size="18" font-weight="900" fill="#0f172a" text-anchor="middle" letter-spacing="1">WPGRIP</text>
                                </g>
                            </svg>
                        </div>

                    </div>
                </main>
            </div>
        </div>
            <div class="bg-white">
            <div class="max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:py-24 lg:px-8 lg:grid lg:grid-cols-3 lg:gap-x-32 align-center">
                <div>
                    <x-heading.h6 class="text-primary-500 tracking-wide uppercase">
                        {{ __('A solid foundation') }}
                    </x-heading.h6>
                    <x-heading.h2 class="text-primary-900 mt-2 text-3xl font-extrabold">
                        {{ __('Everything you need in one dashboard') }}
                    </x-heading.h2>
                    <p class="mt-4 text-lg text-gray-500">
                        It takes care of everything important to keep your site secure and under control. 
                    </p>
                </div>
    

                <div class="mt-4 sm:mt-8 md:mt-10 md:grid md:grid-cols-2 md:gap-x-8 xl:mt-0 lg:col-span-2">
                    <ul class="divide-y divide-gray-200 -mt-4">
                        <li class="py-4 flex">
            <svg aria-hidden="true" class="flex-shrink-0 h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
            </svg>
            <span class="ml-3 text-base text-gray-500">
                Servers
            </span>
        </li>                <li class="py-4 flex">
            <svg aria-hidden="true" class="flex-shrink-0 h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
            </svg>
            <span class="ml-3 text-base text-gray-500">
                Databases
            </span>
        </li>                <li class="py-4 flex">
            <svg aria-hidden="true" class="flex-shrink-0 h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
            </svg>
            <span class="ml-3 text-base text-gray-500">
                Scheduled jobs
            </span>
        </li>                <li class="py-4 flex">
            <svg aria-hidden="true" class="flex-shrink-0 h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
            </svg>
            <span class="ml-3 text-base text-gray-500">
                Daemons
            </span>
        </li>            </ul>
                    <ul class="border-t border-gray-200 divide-y divide-gray-200 md:border-t-0 md:-mt-4">
                        <li class="py-4 flex">
            <svg aria-hidden="true" class="flex-shrink-0 h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
            </svg>
            <span class="ml-3 text-base text-gray-500">
                Firewall rules
            </span>
        </li>                <li class="py-4 flex">
            <svg aria-hidden="true" class="flex-shrink-0 h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
            </svg>
            <span class="ml-3 text-base text-gray-500">
                Sites
            </span>
        </li>                <li class="py-4 flex">
            <svg aria-hidden="true" class="flex-shrink-0 h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
            </svg>
            <span class="ml-3 text-base text-gray-500">
                Certificates
            </span>
        </li>                <li class="py-4 flex">
            <svg aria-hidden="true" class="flex-shrink-0 h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
            </svg>
            <span class="ml-3 text-base text-gray-500">
                Site workers
            </span>
        </li>            </ul>
                </div>
            </div>
        </div>
            <div class="py-16 bg-gray-50 overflow-hidden lg:py-24">
            <div class="relative max-w-xl mx-auto px-4 sm:px-6 lg:px-8 lg:max-w-7xl">
                <svg class="hidden lg:block absolute left-full transform -translate-x-1/2 -translate-y-1/4" width="404" height="784" fill="none" viewBox="0 0 404 784" aria-hidden="true">
                    <defs>
                        <pattern id="b1e6e422-73f8-40a6-b5d9-c8586e37e0e7" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                            <rect x="0" y="0" width="4" height="4" class="text-gray-200" fill="currentColor"></rect>
                        </pattern>
                    </defs>
                    <rect width="404" height="784" fill="url(#b1e6e422-73f8-40a6-b5d9-c8586e37e0e7)"></rect>
                </svg>
        
                <div class="relative">
                    <h2 class="text-center text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                        How to?
                    </h2>
                    <p class="mt-4 max-w-3xl mx-auto text-center text-xl text-gray-500">
                        You can create an account for free to configure and preview your migration.
                    </p>
                </div>
        
                <div class="relative mt-12 lg:mt-24 lg:grid lg:grid-cols-2 lg:gap-8 lg:items-center">
                    <div class="relative">
                        <h3 class="text-2xl font-extrabold text-gray-900 tracking-tight sm:text-3xl">
                            Select your servers
                        </h3>
                        <p class="mt-3 text-lg text-gray-500">
                            All your servers get automatically detected. You decide which server to migrate and which not.
                        </p>
                    </div>
        
                    <div class="mt-10 -mx-4 relative lg:mt-0" aria-hidden="true">
                        <svg class="absolute left-1/2 transform -translate-x-1/2 translate-y-16 lg:hidden" width="784" height="404" fill="none" viewBox="0 0 784 404">
                            <defs>
                                <pattern id="ca9667ae-9f92-4be7-abcb-9e3d727f2941" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                                    <rect x="0" y="0" width="4" height="4" class="text-gray-200" fill="currentColor"></rect>
                                </pattern>
                            </defs>
                            <rect width="784" height="404" fill="url(#ca9667ae-9f92-4be7-abcb-9e3d727f2941)"></rect>
                        </svg>
                        <img class="relative mx-auto" width="490" src="img/server.png" alt="">
                    </div>
                </div>
        
                <svg class="hidden lg:block absolute right-full transform translate-x-1/2 translate-y-12" width="404" height="784" fill="none" viewBox="0 0 404 784" aria-hidden="true">
                    <defs>
                        <pattern id="64e643ad-2176-4f86-b3d7-f2c5da3b6a6d" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                            <rect x="0" y="0" width="4" height="4" class="text-gray-200" fill="currentColor"></rect>
                        </pattern>
                    </defs>
                    <rect width="404" height="784" fill="url(#64e643ad-2176-4f86-b3d7-f2c5da3b6a6d)"></rect>
                </svg>
        
                <div class="relative mt-12 sm:mt-16 lg:mt-24">
                    <div class="lg:grid lg:grid-flow-row-dense lg:grid-cols-2 lg:gap-8 lg:items-center">
                        <div class="lg:col-start-2">
                            <h3 class="text-2xl font-extrabold text-gray-900 tracking-tight sm:text-3xl">
                                Sites
                            </h3>
                            <p class="mt-3 text-lg text-gray-500">
                                Simply select all sites you want to migrate, select if the storage directory should get migrated
                                and that's it.
                            </p>
                        </div>
        
                        <div class="mt-10 -mx-4 relative lg:mt-0 lg:col-start-1">
                            <svg class="absolute left-1/2 transform -translate-x-1/2 translate-y-16 lg:hidden" width="784" height="404" fill="none" viewBox="0 0 784 404" aria-hidden="true">
                                <defs>
                                    <pattern id="e80155a9-dfde-425a-b5ea-1f6fadd20131" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                                        <rect x="0" y="0" width="4" height="4" class="text-gray-200" fill="currentColor"></rect>
                                    </pattern>
                                </defs>
                                <rect width="784" height="404" fill="url(#e80155a9-dfde-425a-b5ea-1f6fadd20131)"></rect>
                            </svg>
                            <img class="relative mx-auto" width="490" src="img/sites.png" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>    <div class="bg-gradient-to-b from-gray-50 to-blue-100">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 px-4">
            <div class="py-24 xl:flex xl:items-center xl:justify-between">
                    <div>
                        <h1 class="text-4xl font-extrabold sm:text-5xl sm:tracking-tight">
                            <span class="text-gray-900">Migrate everything for</span>
                            <span class="text-blue-600 border-b-2 border-dotted border-blue-600">free</span>
                        </h1>
                        <p class="mt-5 text-xl text-gray-500">
                            Includes everything you need.
                        </p>
                    </div>
                    <a href="https://move.ploi.app/register" class="mt-8 w-full bg-blue-600 border border-transparent px-5 py-3 inline-flex items-center justify-center text-base font-medium rounded-md text-white hover:bg-blue-700 sm:mt-10 sm:w-auto xl:mt-0">
                        Configure for free
                    </a>
                </div>
        </div></div>
            <div class="bg-gradient-to-br from-blue-700 to-blue-900 relative">
            <div class="max-w-7xl mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:px-8 z-10 relative">
                <h2 class="text-3xl font-extrabold text-white">
                    Frequently asked questions
                </h2>
                <div class="mt-6 border-t border-blue-400 border-opacity-25 pt-10">
                    <dl class="space-y-10 md:space-y-0 md:grid md:grid-cols-2  md:gap-x-8 md:gap-y-12">
                                            <div>
                                <dt class="text-lg leading-6 font-medium text-white">What sort of data will get migrated trough the tool?</dt>
                                <dd class="mt-2 text-base text-blue-200">Basically everything you need to get your site up and running: It creates database users on ploi, migrates all your databases to ploi, migrate your recipes/scripts, of course it migrates your servers including the scheduled jobs, daemons and all the firewall rules. It will migrate your sites, your environment file, the deployment script, all redirect rules, the site-repository, the queue workers and if wanted the storage directory.</dd>
                            </div>
                                            <div>
                                <dt class="text-lg leading-6 font-medium text-white">So it will migrate 100% of my system?</dt>
                                <dd class="mt-2 text-base text-blue-200">Yes and no. Something between. When you have a very basic system without any custom configuration on forge it's very likely that your system gets migrated completely. Otherwise we will just migrate the data from the question above and you have to complete the further configuration. But in both cases the migrator will do the hard part for you and will save you tons of time.</dd>
                            </div>
                                            <div>
                                <dt class="text-lg leading-6 font-medium text-white">How does the system get access to my data?</dt>
                                <dd class="mt-2 text-base text-blue-200">The tool will install SSH keys on your servers. For every server that gets migrated it will install the SSH key of the migration tool on the forge and ploi server, as well as the forge key on the new ploi server. That is needed to send all data between those servers. But of course, all of these keys will get removed when the migration finishes.</dd>
                            </div>
                                            <div>
                                <dt class="text-lg leading-6 font-medium text-white">What happens when a error occurs?</dt>
                                <dd class="mt-2 text-base text-blue-200">Nobody likes errors but sometimes they happen. In case of an error we will get in touch with you to clarify the further procedure.</dd>
                            </div>
                                            <div>
                                <dt class="text-lg leading-6 font-medium text-white">I have another question, how can I contact you?</dt>
                                <dd class="mt-2 text-base text-blue-200">If you have any open questions or problems feel free to contact us. Either via the live chat at on ploi.io or just send us a email on move@ploi.io</dd>
                            </div>
                                    </dl>
                </div>
            </div>
            <!--        <svg class="absolute shadow-lg inset-x-0 bottom-0 w-full text-blue-800 hidden md:block" fill="none"-->
            <!--                   viewBox="0 0 1440 431" xmlns="http://www.w3.org/2000/svg">-->
            <!--        <path-->
            <!--            d="M481 405C283 428.5 368 390 0.5 395V430.5H1439.5L1439 0.5C1424.83 -0.333333 1384.1 3 1334.5 23C1272.5 48 1250.5 85.5 1246 98C1234.86 128.956 1206.5 167.5 1188.5 175.5C1170.5 183.5 1026 224.5 988 278.5C950 332.5 942.5 353 876.5 372C781.242 399.423 679 381.5 481 405Z"-->
            <!--            fill="currentColor"/>-->
            <!--    </svg>-->
        </div>    <div class="bg-blue-50">
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
        </div>    

</x-layouts.app>
