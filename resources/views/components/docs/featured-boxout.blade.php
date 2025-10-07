{{-- Featured Boxout Component --}}
@props([
    'title' => '',
    'items' => []
])

<div class="bg-white box-border content-stretch flex flex-col gap-6 items-center justify-center pb-6 pt-8 px-6 relative rounded-bl-[5px] rounded-br-[5px] shrink-0 w-full border-t-4 border-lightning-yellow-500" data-name="Featured Boxout">
    <div class="content-stretch flex flex-col gap-6 items-start justify-start relative shrink-0 w-full">
        @if($title)
            <div class="font-inter-tight font-bold leading-[0] not-italic relative shrink-0 text-mine-shaft-500 text-2xl w-full">
                <p class="leading-[1.333]">{{ $title }}</p>
            </div>
        @endif
        
        @if(count($items) > 0)
            <div class="content-stretch flex flex-col gap-2 items-start justify-start relative shrink-0 w-full">
                @foreach($items as $item)
                    <div class="content-stretch flex gap-2.5 items-center justify-center relative shrink-0 w-full">
                        <div class="h-[13.856px] relative shrink-0 w-[11.584px] flex-shrink-0">
                            <svg class="block max-w-none size-full" viewBox="0 0 14 16" fill="none">
                                <path d="M7 0L13.062 4V12L7 16L0.938 12V4L7 0Z" fill="#f26423" stroke="#f26423"/>
                            </svg>
                        </div>
                        <div class="basis-0 font-inter-tight grow leading-[0] min-h-px min-w-px not-italic relative shrink-0 text-mine-shaft-500 text-base">
                            <p class="leading-[1.5]">{{ $item }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="content-stretch flex items-start justify-start relative shrink-0 w-full">
                {{ $slot }}
            </div>
        @endif
    </div>
</div>