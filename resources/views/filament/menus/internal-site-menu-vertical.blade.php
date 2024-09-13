<!-- component -->
<div class="md:min-h-screen flex flex-col flex-auto flex-shrink-0 antialiased ">
    <div class=" flex flex-col top-0 left-0 h-full fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
      <div class="overflow-y-auto overflow-x-hidden flex-grow">
        <ul class="flex flex-col py-4 space-y-1">
          <li class="px-5">
            <div class="flex flex-row items-center h-8">
              <div class="text-sm font-light tracking-wide text-gray-500">Menu</div>
            </div>
          </li>

          <li>
            <a href="#" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-50 text-gray-600 hover:text-gray-800 border-l-4 border-transparent hover:border-indigo-500 pr-6">  
              <x-filament::icon
                icon="icon-backups"
                class="ml-4 h-5 w-5 transition duration-75 text-gray-400 dark:text-gray-500"
              />
              <span class="ml-2 text-sm tracking-wide truncate">Checksums</span>
            </a>
          </li>   

          <li>
            <a href="#" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-50 text-gray-600 hover:text-gray-800 border-l-4 border-transparent hover:border-indigo-500 pr-6">  
              <x-filament::icon
                icon="icon-backups"
                class="ml-4 h-5 w-5 transition duration-75 text-gray-400 dark:text-gray-500"
              />
              <span class="ml-2 text-sm tracking-wide truncate">Checksums</span>
            </a>
          </li>
          <li>
            <x-filament::icon
                icon="icon-backups"
                class="ml-4 h-5 w-5 transition duration-75 text-primary-600 dark:text-primary-400"
              /> 
            <a href="#" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-50 text-gray-600 hover:text-gray-800 border-l-4 border-transparent hover:border-indigo-500 pr-6">  
              <span class="ml-4 text-sm tracking-wide truncate">Certificates</span>
            </a>
          </li>
          <li>
            <a href="#" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-50 text-gray-600 hover:text-gray-800 border-l-4 border-transparent hover:border-indigo-500 pr-6">  
              <span class="ml-4 text-sm tracking-wide truncate">Vulnerabilities</span>
            </a>
          </li>
          
        </ul>
      </div>
    </div>
  </div>