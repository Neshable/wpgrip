

<x-layouts.focus-center class="md:!max-w-6xl mx-auto overflow-x-hidden relative" :backButton="false">

    <x-effect.gradient class="!-start-1/3 -top-50 !w-2/4"/>
    <x-effect.gradient class="!-end-1/3 -top-72 !w-2/4"/>

    <div class="mx-auto md:max-w-6xl text-center mt-8">
        <div class="mx-4">
            
            

            <x-heading.h2 class="!text-2xl md:!text-3xl !font-semibold mb-4">
                Done, you're all set!
            </x-heading.h2>

          
            <p class="mt-4">
                {{__('New to WPGrip?')}} <a class="text-primary-500 font-bold" href="{{ route('register') }}">{{__('Create an account')}}</a>
            </p>

            <div class="card text-center mx-auto md:max-w-xl mt-8 bg-base-100 shadow-xl p-4 md:p-8">
                <p>
                    
                    Your account is now ready to go. Dive in and start exploring WPGrip's powerful features. We're thrilled to have you with us.

                    <x-button-link.primary class="inline-block !w-full mt-6" href="{{ route('home') }}">
                        {{ __('Continue') }}
                    </x-button-link.primary>
                </p>
            </div>

            <p class=" text-xs mt-4">
                Need help getting started? Visit our support page or check out our quick-start guide!
            </p>

        </div>
    </div>

</x-layouts.focus-center>


