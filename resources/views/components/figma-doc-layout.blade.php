{{-- New documentation layout matching Figma node-id=61-192 --}}
<div class="bg-flamingo-50 min-h-screen pt-16">
    <div class="flex gap-[31px] items-start justify-start px-[279px] py-[21px]">
        {{-- Left Sidebar Navigation --}}
        <aside class="flex flex-col gap-[28px] items-start justify-start pr-5 w-[280px]">
            {{-- Closed Side Menu --}}
            <div class="flex flex-col gap-2.5 items-start justify-start w-full">
                <div class="flex flex-row items-center justify-between pl-2.5 pr-0 py-0 w-full">
                    <div class="flex gap-2.5 items-center justify-center">
                        <p class="font-alegreya font-bold text-[#2c2c2c] text-base leading-6">
                            Closed Side Menu
                        </p>
                    </div>
                    <p class="font-['Font_Awesome_6_Free'] text-sm leading-[1.42] not-italic text-[#818181]">

                    </p>
                </div>
            </div>

            {{-- Expanded Side Menu --}}
            <div class="flex flex-col gap-2.5 items-start justify-start w-full">
                <div class="flex flex-row items-center justify-between pl-2.5 pr-0 py-0 w-full">
                    <div class="flex gap-2.5 items-center justify-center">
                        <p class="font-alegreya font-bold text-[#2c2c2c] text-base leading-6">
                            Expanded Side Menu
                        </p>
                    </div>
                    <p class="font-['Font_Awesome_6_Free'] text-sm leading-[1.42] not-italic text-[#818181]">

                    </p>
                </div>
                <div class="flex flex-col items-start w-full">
                    <div class="bg-[#f1bc1b] flex gap-2.5 items-center py-2.5 pl-2.5 pr-0 w-full">
                        <p class="font-alegreya text-[#2c2c2c] text-sm leading-[1.42] whitespace-pre">
                            Active Side Menu Item
                        </p>
                    </div>
                    <div class="bg-[#fafafa] flex gap-2.5 items-center py-2.5 pl-2.5 pr-0 w-full">
                        <p class="font-alegreya text-[#2c2c2c] text-sm leading-[1.42] whitespace-pre">
                            Inactive Side Menu Item
                        </p>
                    </div>
                </div>
            </div>

            {{-- Side Menu Item with Icon --}}
            <div class="flex flex-col gap-2.5 items-center justify-center w-full">
                <div class="flex flex-row items-center justify-between pl-2.5 pr-0 py-0 w-full">
                    <div class="flex gap-2.5 items-center justify-center">
                        <div class="size-6">
                            <svg class="w-full h-full text-[#2c2c2c]" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/>
                            </svg>
                        </div>
                        <p class="font-alegreya font-bold text-[#2c2c2c] text-base leading-6">
                            Side Menu with icon
                        </p>
                    </div>
                    <p class="font-['Font_Awesome_6_Free'] text-sm leading-[1.42] not-italic text-[#818181]">

                    </p>
                </div>
            </div>

            {{-- Always Expanded Side Menu --}}
            <div class="flex flex-col gap-2.5 items-start justify-start w-full">
                <div class="flex gap-2.5 items-center pl-2.5 pr-0 py-0 w-full">
                    <p class="font-alegreya font-bold text-[#2c2c2c] text-base leading-6">
                        Always Expanded Side Menu
                    </p>
                </div>
                <div class="flex flex-col items-start w-full">
                    <div class="bg-[#fafafa] flex gap-2.5 items-center py-2.5 pl-2.5 pr-0 w-full">
                        <p class="font-alegreya text-[#2c2c2c] text-sm leading-[1.42] whitespace-pre">
                            Active Side Menu Item
                        </p>
                    </div>
                    <div class="bg-[#fafafa] flex gap-2.5 items-center py-2.5 pl-2.5 pr-0 w-full">
                        <p class="font-alegreya text-[#2c2c2c] text-sm leading-[1.42] whitespace-pre">
                            Inactive Side Menu Item
                        </p>
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