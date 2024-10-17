<x-layouts.app>

    <div class="m-4">
        <div class="text-center pt-4 pb-0 md:pt-12 md:mb-10">
            <x-heading.h1 class="font-semibold !text-4xl">
                Welcome
            </x-heading.h1>
            <p class="pt-4">
                Take your first steps to get you going.
            </p>

        </div>


        <div class="mx-auto max-w-4xl mt-16">
            <div
                class="divide-y divide-gray-200 rounded-lg bg-white shadow-lg shadow-black/5 ring-1 ring-black/5 dark:divide-gray-800 dark:bg-gray-700 dark:text-gray-300 dark:ring-gray-800 relative z-10 mb-6">
                <ul class="divide-y divide-gray-200 dark:divide-gray-800">
                    <li class="flex items-center space-x-4 p-4 dark:border-gray-800"><!---->
                        <div class="flex-1">
                            <x-heading.h3 class="font-semibold mb-4">
                                Subscribe to a plan 
                            </x-heading.h3>

                            <p class="max-w-2xl text-sm text-gray-500 dark:text-gray-400">
                                To start adding your own sites and using WPGrip features, you will <strong>need to choose a subscription plan</strong>. Don't
                                worry, after subscribing you will be granted a free trial so you can check and see if WPGrip works for you.
                            </p>
                        </div>
                        <x-button-link.secondary href="#plans" class="self-center transition-all !py-3" elementType="a">
                            {{ __('See all plans') }}
                        </x-button-link.secondary>

                    </li>
                    <li class="flex items-center space-x-4 p-4 dark:border-gray-800"><!---->

                        <div class="flex-1">
                            <x-heading.h3 class="font-semibold mb-4">
                                Just collaborating?
                            </x-heading.h3>
                            <p class="max-w-2xl text-sm mb-4 text-gray-500 dark:text-gray-400">
                                Now that you have an account, your team member can invite you to their workspace via the
                                workspace's "Team" menu. Once they do, you'll be able to view the sites from the external workspace.
                            </p>
                            <p class="max-w-2xl text-sm text-gray-800 font-bold dark:text-gray-400">
                                You don't need a paid plan to be invited to other workspaces.
                            </p>
                        </div><a disabled="false"
                            class="inline-flex items-center justify-center text-sm font-medium transition-all ease-in-out duration-100 focus:outline-none focus:ring border rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-600 text-gray-900 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:border-gray-400 focus:bg-white px-3 py-2 text-sm w-48"
                            target="_self" href="/invitations">See Invitations</a>
                    </li>
                </ul>

            </div>

        </div>

        <div id="plans" class="py-16 bg-white overflow-hidden lg:pt-24 lg:pb-8">
            <x-plans.all calculate-saving-rates="true" preselected-interval="month"></x-plans.all>
        </div>
    </div>

</x-layouts.app>
