{{-- On This Page Navigation Component --}}
@props([
    'items' => []
])

<div class="content-stretch flex items-center justify-center relative shrink-0">
    <div class="box-border content-stretch flex flex-col gap-2 items-start justify-center pb-0 pt-4 px-0 relative shrink-0">
        <div class="content-stretch flex gap-2.5 items-center justify-center relative shrink-0">
            <div class="font-inter-tight leading-[0] not-italic relative shrink-0 text-charcoal text-base text-nowrap">
                <p class="leading-relaxed whitespace-pre">On this page</p>
            </div>
        </div>
        
        @if(count($items) > 0)
            <div class="box-border content-stretch flex flex-col gap-1 items-start justify-start pl-0.5 pr-0 py-0 relative shrink-0">
                @foreach($items as $item)
                    <div class="content-stretch flex gap-2.5 items-center justify-center relative shrink-0">
                        <div class="w-3 h-3 relative flex-shrink-0">
                            <svg class="block max-w-none size-full" viewBox="0 0 14 16" fill="none">
                                <path d="M7 0L13.062 4V12L7 16L0.938 12V4L7 0Z" fill="#f26423" stroke="#f26423"/>
                            </svg>
                        </div>
                        <div class="font-inter-tight leading-[0] not-italic relative shrink-0 text-charcoal text-sm text-nowrap">
                            @if(isset($item['url']))
                                <a href="{{ $item['url'] }}" class="leading-normal whitespace-pre hover:text-orange">{{ $item['title'] }}</a>
                            @else
                                <p class="leading-normal whitespace-pre">{{ is_array($item) ? $item['title'] : $item }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>