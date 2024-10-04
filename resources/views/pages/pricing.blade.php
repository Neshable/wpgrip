<x-layouts.app>
    <x-slot name="title">
        {{ __('WPGRIP ') }}
    </x-slot>

    <x-section.hero class="w-full">

        <div class="mx-auto text-center px-4">
            <x-heading.h1 class="mt-4 text-primary-900 font-bold">
                Affordable plans for everyone
            </x-heading.h1>

            <p class="text-black font-bold m-3">
                Choose a plan that's right for you
            </p>
        </div>
    </x-section.hero>


    <div class="py-16 bg-white overflow-hidden lg:py-24">
        <x-plans.all calculate-saving-rates="true" preselected-interval="month"></x-plans.all>
    </div>

    
    <div class="bg-white">
        <div class="max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:py-24 lg:px-8 lg:grid lg:grid-cols-3 lg:gap-x-32 align-center">
            <div>
                <x-heading.h6 class="text-primary-500 tracking-wide uppercase">
                    {{ __('A solid foundation') }}
                </x-heading.h6>
                <x-heading.h2 class="text-primary-900 mt-2 text-3xl font-extrabold">
                    {{ __('Included in all plans') }}
                </x-heading.h2>
                <p class="mt-4 text-lg text-gray-500">
                    It takes care of everything important to keep your site secure and under control.
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
                            Powerful administration
                        </span>
                    </li>
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
                            PageSpeed Monitoring
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
                            Vulnerabilities Monitor
                        </span>
                    </li>
                    <li class="py-4 flex">
                        <svg aria-hidden="true" class="flex-shrink-0 h-6 w-6 text-green-500" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                            </path>
                        </svg>
                        <span class="ml-3 text-base text-gray-500">
                            Easy one-click updates
                        </span>
                    </li>
                    <li class="py-4 flex">
                        <svg aria-hidden="true" class="flex-shrink-0 h-6 w-6 text-green-500" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                            </path>
                        </svg>
                        <span class="ml-3 text-base text-gray-500">
                            Git Repositories
                        </span>
                    </li>
                    <li class="py-4 flex">
                        <svg aria-hidden="true" class="flex-shrink-0 h-6 w-6 text-green-500" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                            </path>
                        </svg>
                        <span class="ml-3 text-base text-gray-500">
                            Client & Server management
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>


    <div class="text-center mt-24 mx-4" id="faq">
        <x-heading.h6 class="text-primary-500">
            {{ __('FAQ') }}
        </x-heading.h6>
        <x-heading.h2 class="text-primary-900">
            {{ __('Got a Question?') }}
        </x-heading.h2>
        <p>{{ __('Here are the most common questions to help you with your decision.') }}</p>
    </div>

    {{-- FAQ --}}
    <div class="max-w-none md:max-w-6xl mx-auto">
        <x-accordion class="mt-4 p-8">
            <x-accordion.item active="true" name="faqs">
                <x-slot name="title">{{ __('What is SaaSykit?') }}</x-slot>

                <p>
                    {{ __('SaaSykit is a complete SaaS starter kit that includes everything you need to start your SaaS business. It comes ready with a huge list of reusable components, a complete admin panel, user dashboard, user authentication, user & role management, plans & pricing, subscriptions, payments, emails, and more.') }}
                </p>

            </x-accordion.item>

            <x-accordion.item active="false" name="faqs">
                <x-slot name="title">{{ __('What features does SaaSykit offer?') }}</x-slot>

                <p class="mt-4">
                    {{ __('Here are some of the features included in SaaSykit in a nutshell:') }}
                </p>

                <ul class="mt-4 list-disc ms-4 ps-4">
                    <li>{{ __('Customize Styles: Customize the styles &amp; colors, error page of your application to fit your brand.') }}</li>
                    <li>{{ __('Product, Plans &amp; Pricing: Create and manage your products, plans, and pricing from a beautiful and easy-to-use admin panel.') }}</li>
                    <li>{{ __('Beautiful checkout process: Your customers can subscribe to your plans from a beautiful checkout process.') }}</li>
                    <li>{{ __('Huge list of ready-to-use components: Plans &amp; Pricing, hero section, features section, testimonials, FAQ, Call to action, tab slider, and much more.') }}</li>
                    <li>{{ __('User authentication: Comes with user authentication out of the box, whether classic email/password or social login (Google, Facebook, Twitter, Github, LinkedIn, and more).') }}</li>
                    <li>{{ __('Discounts: Create and manage your discounts and reward your customers.') }}</li>
                    <li>{{ __('SaaS metric stats: View your MRR, Churn rates, ARPU, and other SaaS metrics.') }}</li>
                    <li>{{ __('Multiple payment providers: Stripe, Paddle, and more coming soon.') }}</li>
                    <li>{{ __('Multiple email providers: Mailgun, Postmark, Amazon SES, and more coming soon.') }}</li>
                    <li>{{ __('Blog: Create and manage your blog posts.') }}</li>
                    <li>{{ __('User &amp; Role Management: Create and manage your users and roles, and assign permissions to your users.') }}</li>
                    <li>{{ __('Fully translatable: Translate your application to any language you want.') }}</li>
                    <li>{{ __('Sitemap &amp; SEO: Sitemap and SEO optimization out of the box.') }}</li>
                    <li>{{ __('Admin Panel: Manage your SaaS application from a beautiful admin panel powered by ') }} <a href="https://filamentphp.com/" target="_blank" rel="noopener noreferrer">Filament</a>.</li>
                    <li>{{ __('User Dashboard: Your customers can manage their subscriptions, change payment method, upgrade plan, cancel subscription, and more from a beautiful user dashboard powered by') }} <a href="https://filamentphp.com/" target="_blank" rel="noopener noreferrer">Filament</a>.</li>
                    <li>{{ __('Automated Tests: Comes with automated tests for critical components of the application.') }}</li>
                    <li>{{ __('One-line deployment: Provision your server and deploy your application easily with integrated') }} <a href="https://deployer.org/" target="_blank" rel="noopener noreferrer">Deployer</a> {{ __('  support.') }}</li>
                    <li>{{ __('Developer-friendly: Built with developers in mind, uses best coding practices.') }}</li>
                    <li>{{ __('And much more...') }}</li>
                </ul>

            </x-accordion.item>

            <x-accordion.item active="false" name="faqs">
                <x-slot name="title">{{ __('Which payment providers are supported?') }}</x-slot>

                <p>
                    {{ __('SaaSykit supports Stripe and Paddle out of the box. You can easily add more payment providers by extending the code. More payment method will be added in the future as well (e.g. Lemon Squeezy)') }}
                </p>

            </x-accordion.item>

            <x-accordion.item active="false" name="faqs">
                <x-slot name="title">{{ __('Do you offer support?') }}</x-slot>

                <p>
                    {{ __('Of course! we offer email and discord support to help you with any issues you might face or questions you have. Write us an email at') }} <a href="mailto:{{config('app.support_email')}}" class="text-primary-500 hover:underline">{{config('app.support_email')}}</a> {{ __('or join our') }} <a href="{{config('app.social_links.discord')}}">{{ __('discord server')}}</a> {{ __('to get help.')}}
                </p>

            </x-accordion.item>

            <x-accordion.item active="false" name="faqs">
                <x-slot name="title">{{'What Tech stack is used?'}}</x-slot>

                <p>
                    {{ __('SaaSykit is built on top of') }} <a href="https://laravel.com" target="_blank">Laravel</a> {{ __('Laravel, the most popular PHP framework, and') }} <a target="_blank" href="https://filamentphp.com/">Filament</a> {{ __(', a beautiful and powerful admin panel for Laravel. It also uses TailwindCSS, AlpineJS, and Livewire.')}}
                </p>
                <p class="mt-4">
                    {{ __('You can use your favourite database (MySQL, PostgreSQL, SQLite) and your favourite queue driver (Redis, Amazon SQS, etc).')}}
                </p>

            </x-accordion.item>

            <x-accordion.item active="false" name="faqs">
                <x-slot name="title">{{'How often is SaaSykit updated?'}}</x-slot>

                <p>
                    {{ __('SaaSykit is updated regularly to keep up with the latest Laravel and Filament versions, and to add new features and improvements.')}}
                </p>

            </x-accordion.item>

            <x-accordion.item active="false" name="faqs">
                <x-slot name="title">{{'Do you offer refunds?'}}</x-slot>

                <p>
                    {{ __('Yes, we offer a 14-day money-back guarantee. If you are not satisfied with SaaSykit, you can request a refund within 14 days of your purchase. Please write us an email at') }} <a href="mailto:{{config('app.support_email')}}" class="text-primary-500 hover:underline">{{config('app.support_email')}}</a> {{ __('to request a refund.')}}
                </p>

            </x-accordion.item>

            <x-accordion.item active="false" name="faqs">
                <x-slot name="title">{{'Where can I host my SaaS application?'}}</x-slot>

                <p>
                    {{ __('You can host your SaaS application on any server that supports PHP, such as DigitalOcean, AWS, Hetzner, Linode, and more. You can also use a platform like Laravel Forge to manage your server and deploy your application.')}}
                </p>

            </x-accordion.item>

            <x-accordion.item active="false" name="faqs">
                <x-slot name="title">{{'Is there a demo available?'}}</x-slot>

                <p>
                    {{ __('Yes, a demo is available to help you get a feel of SaaSykit. You can find the demo') }} <a href="https://saasykit.com/demo" target="_blank" rel=”nofollow” >here</a>.
                </p>

            </x-accordion.item>

            <x-accordion.item active="false" name="faqs">
                <x-slot name="title">{{'Is there documentation available?'}}</x-slot>

                <p>
                    {{ __('Yes, an extensive documentation is available to help you get started with SaaSykit. You can find the documentation ')}} <a href="https://saasykit.com/docs" target="_blank">here</a>.
                </p>

            </x-accordion.item>

            <x-accordion.item active="false" name="faqs">
                <x-slot name="title">{{'How is SaaSykit different from just using Laravel directly?'}}</x-slot>

                <p>
                    {{__('SaaSykit is built on top of Laravel with the intention to save you time and effort by not having to build everything needed for a modern SaaS from scratch, like payment provider integration, subscription management, user authentication, user & role management, having a beautiful admin panel, a user dashboard to manage their subscriptions/payments, and more.')}}
                </p>
                <p class="mt-4">
                    {{__('You can choose to base your SaaS on vanilla Laravel and build everything from scratch if you prefer and that is totally fine, but you will need a few months to build what SaaSykit offers out of the box, then on top of that, you will need to start to build your actual SaaS application.')}}
                </p>

                <p class="mt-4">
                    {{__('SaaSykit is a great starting point for your SaaS application, it is built with best coding practices, and it is developer-friendly. It is also built with the intention to be easily customizable and extendable. Any developer who is familiar with Laravel will feel right at home.')}}
                </p>

            </x-accordion.item>
        </x-accordion>
    </div>

</x-layouts.app>
