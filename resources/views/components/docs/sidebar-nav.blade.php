{{-- Documentation Sidebar Navigation Component --}}
@props([
    'sections' => [],
    'currentPath' => ''
])

<div class="content-stretch flex flex-col gap-7 items-start justify-start relative shrink-0 w-[280px]" data-name="Left Sidebar">
    @foreach($sections as $section)
        <div class="content-stretch flex flex-col gap-2.5 items-start justify-start relative shrink-0 w-full">
            {{-- Section Header --}}
            <div class="box-border content-stretch flex items-center justify-between pl-2.5 pr-0 py-0 relative shrink-0 w-full cursor-pointer">
                <div class="content-stretch flex gap-2.5 items-center justify-center relative shrink-0">
                    @if(isset($section['icon']))
                        <div class="relative shrink-0 size-6">
                            {!! $section['icon'] !!}
                        </div>
                    @endif
                    <div class="font-inter-tight leading-[0] not-italic relative shrink-0 text-charcoal text-base text-nowrap">
                        <p class="leading-[1.5] whitespace-pre">{{ $section['title'] }}</p>
                    </div>
                </div>
                @if(isset($section['items']) && count($section['items']) > 0)
                    <div class="font-['Font_Awesome_6_Free:Solid',_sans-serif] leading-[0] not-italic relative shrink-0 text-gray-darker text-sm text-nowrap">
                        <p class="leading-[1.42] whitespace-pre">{{ $section['expanded'] ?? false ? 'angle-up' : 'angle-down' }}</p>
                    </div>
                @endif
            </div>
            
            {{-- Section Items (shown when expanded) --}}
            @if(isset($section['items']) && ($section['expanded'] ?? false))
                <div class="content-stretch flex flex-col items-start justify-start relative shrink-0 w-full">
                    @foreach($section['items'] as $item)
                        <div class="@if($currentPath === $item['url']) bg-yellow @else bg-off-white @endif box-border content-stretch flex gap-2.5 items-center justify-start pl-2.5 pr-0 py-2.5 relative shrink-0 w-full">
                            <div class="font-inter-tight leading-[0] not-italic relative shrink-0 text-charcoal text-sm text-nowrap">
                                <p class="leading-[1.42] whitespace-pre">{{ $item['title'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endforeach
</div>