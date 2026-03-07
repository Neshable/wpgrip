<x-layouts.app>

<section class="relative w-full pt-40 pb-16" style="display: flow-root;">
    <div class="max-w-4xl mx-auto px-6">
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-semibold tracking-tight text-white">
                Welcome
            </h1>
            <p class="mt-4 text-neutral-400">
                Take your first steps to get you going.
            </p>
        </div>

        <div class="divide-y divide-white/10 rounded-xl border border-white/10 bg-white/[0.03] relative z-10 mb-6">
            <div class="flex items-center gap-6 p-6">
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-white mb-3">Subscribe to a plan</h3>
                    <p class="max-w-2xl text-sm text-neutral-400">
                        To start adding your own sites and using WPGrip features, you will <strong class="text-neutral-200">need to choose a subscription plan</strong>. Don't
                        worry, after subscribing you will be granted a free trial so you can check and see if WPGrip works for you.
                    </p>
                </div>
                <a href="#plans" class="flex-shrink-0 inline-flex items-center justify-center h-10 px-5 rounded-lg border border-white/10 bg-white/5 text-sm font-medium text-neutral-300 hover:bg-white/10 hover:text-white transition-all duration-200">
                    {{ __('See all plans') }}
                </a>
            </div>
            <div class="flex items-center gap-6 p-6">
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-white mb-3">Just collaborating?</h3>
                    <p class="max-w-2xl text-sm mb-3 text-neutral-400">
                        Now that you have an account, your team member can invite you to their workspace via the
                        workspace's "Team" menu. Once they do, you'll be able to view the sites from the external workspace.
                    </p>
                    <p class="max-w-2xl text-sm text-neutral-200 font-semibold">
                        You don't need a paid plan to be invited to other workspaces.
                    </p>
                </div>
                <a href="/invitations" class="flex-shrink-0 inline-flex items-center justify-center h-10 px-5 rounded-lg border border-white/10 bg-white/5 text-sm font-medium text-neutral-300 hover:bg-white/10 hover:text-white transition-all duration-200">
                    See Invitations
                </a>
            </div>
        </div>
    </div>
</section>

<section id="plans" class="max-w-4xl mx-auto px-6 pb-16">
    <x-plans.all calculate-saving-rates="true" preselected-interval="month"></x-plans.all>
</section>

</x-layouts.app>
