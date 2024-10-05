<x-layouts.app>
    <x-slot name="title">
        {{ __('WPGRIP ') }}
    </x-slot>

    <x-section.hero class="w-full">

        <div class="mx-auto text-center px-4">
            <x-heading.h1 class="mt-4 text-gray-900 font-bold">
                Flexible Plans That Grow With You
            </x-heading.h1>

            <p class="text-gray-600 m-3">
                Whether you're a freelancer or an agency, our plans are designed to fit your needs perfectly.
            </p>
        </div>
    </x-section.hero>


    <div class="py-16 bg-white overflow-hidden lg:py-24">
        <x-plans.all calculate-saving-rates="true" preselected-interval="month"></x-plans.all>
    </div>

    
    <div class="bg-white">
        <div class="max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:py-24 lg:px-8 lg:grid lg:grid-cols-3 lg:gap-x-32 align-center">
            <div>
                <x-heading.h6 class="text-gray-600 tracking-wide uppercase">
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
                            Uptime & SSL Monitoring
                        </span>
                    </li>
                    <li class="py-4 flex">
                        <svg aria-hidden="true" class="flex-shrink-0 h-6 w-6 text-green-500" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                            </path>
                        </svg>
                        <span class="ml-3 text-base text-gray-500">
                            AI Insights
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
                            One-Click Updates
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
        <x-heading.h2 class="text-primary-900">
            Frequently Asked Questions
        </x-heading.h2>
        <p>{{ __('Here are the most common questions to help you with your decision.') }}</p>
    </div>

    {{-- FAQ --}}
    <div class="max-w-none md:max-w-6xl mx-auto">
        <x-accordion class="mt-4 p-8">

            <x-accordion.item active="true" name="faqs">
                <x-slot name="title">What is WPGrip?</x-slot>
                <p>WPGrip is an all-in-one solution for managing multiple WordPress sites from a single dashboard. It offers tools for performance, security, and team management to make your workflow seamless.</p>
            </x-accordion.item>

            <x-accordion.item name="faqs">
                <x-slot name="title">Can I change my plan?</x-slot>
                <p>Yes, you can upgrade or downgrade your plan at any time directly from your dashboard. We make it easy to adapt as your needs change.</p>
            </x-accordion.item>
            
            <x-accordion.item name="faqs">
                <x-slot name="title">How long is the free trial?</x-slot>
                <p>We offer a 5-day free trial. This gives you plenty of time to explore all the features WPGrip has to offer.</p>
            </x-accordion.item>
            
            <x-accordion.item name="faqs">
                <x-slot name="title">Do you offer refunds?</x-slot>
                <p>Yes, we offer a 30-day money-back guarantee on all our annual subscriptions. If you're not satisfied, we've got you covered.</p>
            </x-accordion.item>
            
            <x-accordion.item name="faqs">
                <x-slot name="title">Do you offer 24/7 support?</x-slot>
                <p>No, we do not offer 24/7 support. However, we always aim to respond as quickly as possible to ensure you get the help you need.</p>
            </x-accordion.item>
            
            <x-accordion.item name="faqs">
                <x-slot name="title">What happens after my free trial ends?</x-slot>
                <p>Once your trial ends, your subscription will move to the free plan. You will be notified beforehand so you can choose the plan that works best for you.</p>
            </x-accordion.item>
            
            
            <x-accordion.item name="faqs">
                <x-slot name="title">Which payment providers are supported?</x-slot>
                <p>We support major credit cards and also offer payment via PayPal for your convenience.</p>
            </x-accordion.item>
            
            <x-accordion.item name="faqs">
                <x-slot name="title">Can I pay through PayPal?</x-slot>
                <p>Yes, PayPal is available as a payment option for all our plans.</p>
            </x-accordion.item>
            
            <x-accordion.item name="faqs">
                <x-slot name="title">Is there a setup fee?</x-slot>
                <p>No, there are no setup fees. You can get started immediately without any additional costs.</p>
            </x-accordion.item>
            
            <x-accordion.item name="faqs">
                <x-slot name="title">What are the terms of subscription?</x-slot>
                <p>All our plans are available on a yearly subscription basis without any minimum commitments. You can cancel your account at any time directly from the dashboard—no questions asked.</p>
            </x-accordion.item>
        </x-accordion>
    </div>

</x-layouts.app>
