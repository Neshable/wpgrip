<x-layouts.simple>

    <x-slot name="title">Refund Policy — {{ config('app.name') }}</x-slot>

    <x-heading.h1 class="md:!text-4xl !text-4xl pt-4 pb-6">
        Refund Policy
    </x-heading.h1>

    <p class="text-sm text-neutral-400 mb-8">Last updated: {{ date('F j, Y') }}</p>

    <p class="mb-6">
        Thank you for subscribing to {{ config('app.name') }}. We want to make sure you are completely satisfied with your purchase.
        Please read this Refund Policy carefully before subscribing.
    </p>

    <x-heading.h2 class="text-xl mb-2">14-Day Money-Back Guarantee</x-heading.h2>
    <p class="mb-6">
        We offer a <strong>14-day money-back guarantee</strong> on all new subscriptions. If you are not satisfied with
        {{ config('app.name') }} for any reason within the first 14 days of your initial subscription, contact us at
        <a href="mailto:{{ config('app.support_email', 'support@' . parse_url(config('app.url'), PHP_URL_HOST)) }}"
           class="text-blue-400 hover:underline">{{ config('app.support_email', 'support@' . parse_url(config('app.url'), PHP_URL_HOST)) }}</a>
        and we will issue a full refund — no questions asked.
    </p>

    <x-heading.h2 class="text-xl mb-2">Eligibility</x-heading.h2>
    <p class="mb-4">To be eligible for a refund under this policy:</p>
    <ul class="list-disc list-inside mb-6 space-y-1 text-neutral-300">
        <li>Your refund request must be submitted within <strong>14 days</strong> of the original purchase date.</li>
        <li>The request must apply to your <strong>first billing period</strong> only. Renewals and subsequent billing cycles are not eligible.</li>
        <li>Your account must not have been suspended or terminated for a violation of our Terms of Service.</li>
    </ul>

    <x-heading.h2 class="text-xl mb-2">Renewals & Recurring Charges</x-heading.h2>
    <p class="mb-6">
        Subscription renewals — whether monthly or annual — are <strong>non-refundable</strong>. We send a renewal reminder
        email before each billing date. You can cancel your subscription at any time from your account dashboard to prevent
        future charges; access continues until the end of the paid period.
    </p>

    <x-heading.h2 class="text-xl mb-2">Annual Plans</x-heading.h2>
    <p class="mb-6">
        If you purchased an annual plan and cancel after the 14-day window, you will retain access until the end of your
        billing year. We do not provide pro-rated refunds for unused months on annual subscriptions outside the 14-day
        guarantee period.
    </p>

    <x-heading.h2 class="text-xl mb-2">Plan Downgrades</x-heading.h2>
    <p class="mb-6">
        Downgrading your plan does not entitle you to a refund for the difference. The downgrade takes effect at the start
        of your next billing cycle. No credits or partial refunds are issued for the remainder of the current period.
    </p>

    <x-heading.h2 class="text-xl mb-2">Exceptional Circumstances</x-heading.h2>
    <p class="mb-6">
        We review refund requests on a case-by-case basis outside of the 14-day window when there is evidence of a billing
        error on our part or a prolonged, documented service outage that materially impacted your use of the product.
        Such requests are at our sole discretion.
    </p>

    <x-heading.h2 class="text-xl mb-2">How to Request a Refund</x-heading.h2>
    <p class="mb-4">To request a refund, please email us at:</p>
    <p class="mb-6">
        <a href="mailto:{{ config('app.support_email', 'support@' . parse_url(config('app.url'), PHP_URL_HOST)) }}"
           class="text-blue-400 hover:underline font-medium">
            {{ config('app.support_email', 'support@' . parse_url(config('app.url'), PHP_URL_HOST)) }}
        </a>
    </p>
    <p class="mb-6">
        Include your account email address and the reason for your request. We aim to respond within <strong>2 business days</strong>.
        Approved refunds are typically processed within <strong>5–10 business days</strong> depending on your payment provider.
    </p>

    <x-heading.h2 class="text-xl mb-2">Contact Us</x-heading.h2>
    <p class="mb-6">
        If you have any questions about this Refund Policy, please contact us at
        <a href="mailto:{{ config('app.support_email', 'support@' . parse_url(config('app.url'), PHP_URL_HOST)) }}"
           class="text-blue-400 hover:underline">{{ config('app.support_email', 'support@' . parse_url(config('app.url'), PHP_URL_HOST)) }}</a>.
    </p>

</x-layouts.simple>
