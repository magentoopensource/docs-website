{{-- New documentation layout matching Figma node-id=61-192 --}}
<div class="bg-flamingo-50 min-h-screen pt-16">
    <div class="flex gap-[31px] items-start justify-start px-[279px] py-[21px]">
        {{-- Left Sidebar Navigation --}}
        <aside class="flex flex-col gap-[30px] items-start justify-start pr-5 w-[269px]">
            {{-- Closed Side Menu --}}
            <div class="flex flex-col gap-2.5 items-start justify-start w-full">
                <div class="flex flex-row gap-2.5 items-center justify-center pl-2.5 pr-0 py-0 w-full">
                    <div class="basis-0 grow min-h-px min-w-px font-alegreya text-waterloo-900 text-base leading-6">
                        Closed Side Menu
                    </div>
                    <div class="overflow-clip size-6">
                        <svg class="w-full h-full text-waterloo-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Expanded Side Menu --}}
            <div class="flex flex-col gap-2.5 items-start justify-start w-full">
                <div class="flex flex-row gap-2.5 items-center justify-center pl-2.5 pr-0 py-0 w-full">
                    <div class="basis-0 grow min-h-px min-w-px font-alegreya font-bold text-waterloo-900 text-base leading-6">
                        Expanded Side Menu
                    </div>
                    <div class="rotate-180">
                        <svg class="w-6 h-6 text-waterloo-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
                <div class="flex flex-col gap-2 items-start justify-start w-full">
                    <div class="bg-lightningYellow-500 flex flex-row gap-2.5 items-center justify-start p-2.5 rounded-[5px] w-full">
                        <div class="font-alegreya text-waterloo-950 text-sm leading-[1.42] whitespace-pre">
                            Active Side Menu item
                        </div>
                    </div>
                    <div class="flex flex-row gap-2.5 items-center justify-center px-2.5 py-0 w-full">
                        <div class="basis-0 grow min-h-px min-w-px font-alegreya text-waterloo-900 text-sm leading-[1.42]">
                            Inactive Side Menu item
                        </div>
                    </div>
                </div>
            </div>

            {{-- Side Menu Item with Icon --}}
            <div class="flex flex-col gap-2.5 items-center justify-center w-full">
                <div class="flex flex-row items-center justify-between pl-2.5 pr-0 py-0 w-full">
                    <div class="basis-0 flex flex-row gap-2.5 grow items-center justify-center min-h-px min-w-px">
                        <div class="h-[19px] w-4">
                            <svg class="w-full h-full text-waterloo-900" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                                <polyline points="9,22 9,12 15,12 15,22"/>
                            </svg>
                        </div>
                        <div class="basis-0 grow min-h-px min-w-px font-alegreya text-waterloo-900 text-base leading-6">
                            Side Menu item with icon
                        </div>
                    </div>
                    <div class="overflow-clip size-6">
                        <svg class="w-full h-full text-waterloo-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Always Expanded Side Menu --}}
            <div class="flex flex-col gap-2.5 items-start justify-start w-full">
                <div class="flex flex-row gap-2.5 items-center justify-center pl-2.5 pr-0 py-0 w-full">
                    <div class="basis-0 grow min-h-px min-w-px font-alegreya font-bold text-waterloo-900 text-base leading-6">
                        Always Expanded Side Menu
                    </div>
                </div>
                <div class="flex flex-col gap-2 items-start justify-start w-full">
                    <div class="flex flex-row gap-2.5 items-center justify-center px-2.5 py-0 w-full">
                        <div class="basis-0 grow min-h-px min-w-px font-alegreya text-waterloo-900 text-sm leading-[1.42]">
                            Inactive Side Menu item
                        </div>
                    </div>
                    <div class="flex flex-row gap-2.5 items-center justify-center px-2.5 py-0 w-full">
                        <div class="basis-0 grow min-h-px min-w-px font-alegreya text-waterloo-900 text-sm leading-[1.42]">
                            Inactive Side Menu item
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        {{-- Main Content and Right Sidebar --}}
        <div class="flex flex-row gap-[30px] items-start justify-start">
            {{-- Main Content Area --}}
            <main class="flex flex-col gap-8 items-start justify-start w-[670px]">
                {{ $slot }}
            </main>

            {{-- Right Sidebar - Page Navigation --}}
            <aside class="flex flex-col gap-[9px] items-start justify-start px-0 py-[13px] w-[170px]">
                <div class="font-alegreya text-[#45556c] text-sm leading-[1.42] w-full">
                    On this page
                </div>
                <div class="flex flex-col font-alegreya gap-2 items-start justify-start text-[#1d293d] text-sm leading-[1.42] w-full">
                    <div class="w-full">
                        What you'll learn
                    </div>
                    <div class="w-full">
                        Before you begin
                    </div>
                </div>
            </aside>
        </div>
    </div>
</div>