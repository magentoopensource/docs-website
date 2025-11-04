{{-- Ecosystem Menu - Top dark bar from Figma --}}
<div class="bg-charcoal flex items-center justify-center px-8 sm:px-16 md:px-24 lg:px-32 py-0 h-10 w-full">
    <div class="max-w-[1440px] w-full flex items-center justify-between relative">
    {{-- Left side: "Explore the Magento Open Source Ecosystem" text --}}
    <div class="flex gap-2.5 items-center justify-center self-stretch shrink-0">
        <div class="font-inter-tight text-sm leading-[1.42] text-gray-lightest font-bold">
            Explore the Magento<span class="text-[9px]">®</span> Open Source Ecosystem
        </div>
    </div>
    
    {{-- Right side: Navigation buttons --}}
    <div class="flex flex-wrap gap-px items-center justify-start shrink-0">
        {{-- Magento Open Source --}}
        <div class="bg-charcoal flex gap-2.5 items-center justify-start px-5 py-2.5 text-sm leading-[1.42]">
            <a href="https://github.com/magento/magento2" target="_blank" class="font-inter-tight text-gray-lightest font-bold no-underline">Magento Open Source</a>
            <svg width="8" height="11" viewBox="0 0 8 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1.75 11C1.50391 11 1.28516 10.918 1.12109 10.7539C0.765625 10.4258 0.765625 9.85156 1.12109 9.52344L4.86719 5.75L1.12109 2.00391C0.765625 1.67578 0.765625 1.10156 1.12109 0.773438C1.44922 0.417969 2.02344 0.417969 2.35156 0.773438L6.72656 5.14844C7.08203 5.47656 7.08203 6.05078 6.72656 6.37891L2.35156 10.7539C2.1875 10.918 1.96875 11 1.75 11Z" fill="#F26423"/>
            </svg>

        </div>
        
        {{-- Separator line --}}
        <div class="h-10 w-px bg-gray-darkest"></div>
        
        {{-- Magento Association --}}
        <div class="bg-charcoal flex gap-2.5 items-center justify-start px-5 py-2.5 text-sm leading-[1.42]">
            <a href="https://www.magentoassociation.org/home" target="_blank" class="font-inter-tight text-gray-lightest font-bold no-underline">Magento Association</a>
            <svg width="8" height="11" viewBox="0 0 8 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1.75 11C1.50391 11 1.28516 10.918 1.12109 10.7539C0.765625 10.4258 0.765625 9.85156 1.12109 9.52344L4.86719 5.75L1.12109 2.00391C0.765625 1.67578 0.765625 1.10156 1.12109 0.773438C1.44922 0.417969 2.02344 0.417969 2.35156 0.773438L6.72656 5.14844C7.08203 5.47656 7.08203 6.05078 6.72656 6.37891L2.35156 10.7539C2.1875 10.918 1.96875 11 1.75 11Z" fill="#F26423"/>
            </svg>

        </div>
        
        {{-- Separator line --}}
        <div class="h-10 w-px bg-gray-darkest"></div>
        
        {{-- Meet Magento --}}
        <div class="bg-charcoal flex gap-2.5 items-center justify-start px-5 py-2.5 text-sm leading-[1.42]">
            <a href="https://www.meet-magento.com/" target="_blank" class="font-inter-tight text-gray-lightest font-bold no-underline">Meet Magento</a>
            <svg width="8" height="11" viewBox="0 0 8 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1.75 11C1.50391 11 1.28516 10.918 1.12109 10.7539C0.765625 10.4258 0.765625 9.85156 1.12109 9.52344L4.86719 5.75L1.12109 2.00391C0.765625 1.67578 0.765625 1.10156 1.12109 0.773438C1.44922 0.417969 2.02344 0.417969 2.35156 0.773438L6.72656 5.14844C7.08203 5.47656 7.08203 6.05078 6.72656 6.37891L2.35156 10.7539C2.1875 10.918 1.96875 11 1.75 11Z" fill="#F26423"/>
            </svg>
        </div>
        
        {{-- Separator line --}}
        <div class="h-10 w-px bg-gray-darkest"></div>
        
        {{-- Development Resources --}}
        <div class="bg-charcoal flex gap-2.5 items-center justify-start px-5 py-2.5 text-sm leading-[1.42]">
            <a href="https://devdocs.mage-os.org/" target="_blank" class="font-inter-tight text-gray-lightest font-bold no-underline">Development Resources</a>
            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M11.8125 5.75C11.8125 6.24219 11.4023 6.65234 10.9375 6.65234H7V10.5898C7 11.0547 6.58984 11.4375 6.125 11.4375C5.63281 11.4375 5.25 11.0547 5.25 10.5898V6.65234H1.3125C0.820312 6.65234 0.4375 6.24219 0.4375 5.75C0.4375 5.28516 0.820312 4.90234 1.3125 4.90234H5.25V0.964844C5.25 0.472656 5.63281 0.0625 6.125 0.0625C6.58984 0.0625 7 0.472656 7 0.964844V4.90234H10.9375C11.4023 4.875 11.8125 5.28516 11.8125 5.75Z" fill="#F26423"/>
            </svg>
        </div>
    </div>
    
        {{-- Collapse button (right side) --}}
        <button class="right-8 top-2 w-6 h-6 cursor-pointer text-white">
            {{-- Double chevron up icon from Figma --}}
            <svg width="12" height="13" viewBox="0 0 12 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0.292969 6.293L1.70697 7.707L5.99997 3.414L10.293 7.707L11.707 6.293L5.99997 0.586L0.292969 6.293Z" fill="#D9D9D9"/>
                <path d="M0.292969 11.293L1.70697 12.707L5.99997 8.414L10.293 12.707L11.707 11.293L5.99997 5.586L0.292969 11.293Z" fill="#D9D9D9"/>
            </svg>

        </button>
    </div>
</div>