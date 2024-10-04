@if ( $this->getRecord() && !$this->getRecord()->ssh_connection )
<div style="display: block; background: rgba(255, 0, 0, 0.1);"  class="fi-section mt- rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10" x-transition:enter-start="opacity-0 translate-x-12" x-transition:leave-end="opacity-0 scale-95">
    <div class="flex items-center w-full gap-3 p-4 ">
        <div class="mt-0.5 grid flex-1">
            <h3 class="fi-no-notification-title text-sm font-medium text-gray-950 dark:text-white">
                Issue with SSH connection.
            </h3>
            <p class="fi-no-notification-body text-sm text-gray-500 dark:text-gray-400 mt-1">
                A connection cannot be made with the website. Please make sure your account SSH key is added to the website's server authorized keys. Most of the features will be disabled until a connection is established.
            </p>
        </div>
        <a href="" style="--c-400:var(--primary-400);--c-500:var(--primary-500);--c-600:var(--primary-600);" class="fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-color-custom fi-btn-color-primary fi-size-md fi-btn-size-md gap-1.5 px-3 py-2 text-sm inline-grid shadow-sm bg-custom-600 text-white hover:bg-custom-500 dark:bg-custom-500 dark:hover:bg-custom-400 focus-visible:ring-custom-500/50 dark:focus-visible:ring-custom-400/50">
            <span class="fi-btn-label">
                Check Connection
            </span>
        </a>
    </div>
</div>
@endif