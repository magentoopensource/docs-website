@extends('partials.layout')

@section('content')
    {{-- Hero Section - Commerce begins with Community --}}
    {{-- Break out of parent max-width container for full-viewport-width white background --}}
    <style>
        /* Full-width container breakout technique */
        .hero-full-width {
            width: 100vw;
            position: relative;
            left: 50%;
            right: 50%;
            margin-left: -50vw;
            margin-right: -50vw;
        }

        .hexagon-hero {
            width: 100vw;
            height: 320px;
            display: block;
            overflow: hidden;
            background-color: white;
            position: relative;
        }
        .hexagon-hero svg {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            height: 100%;
            width: auto;
            min-width: 100vw;
        }
        @media screen and (min-width: 1280px) {
            .hexagon-hero {
                height: 680px;
                margin-bottom: 6rem;
            }
        }
    </style>

    <section class="hero-full-width bg-white flex flex-col items-center justify-center px-4 sm:px-6 lg:px-8 pt-12 sm:pt-16 lg:pt-24 pb-0 gap-12">
        {{-- Main Hero Content --}}
        <div class="flex flex-col items-center justify-center text-charcoal max-w-7xl xl:max-w-8xl mx-auto w-full">
            <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-inter-tight leading-none text-center font-bold mb-8">
                Commerce begins with Community.
            </h1>
            <p class="text-lg sm:text-xl font-inter-tight font-medium leading-relaxed text-center max-w-3xl mx-auto px-4">
                Your comprehensive guide to building, managing, and growing a successful Magento 2 store.
            </p>
        </div>

        {{-- Full-width Hexagon Hero with proper white background --}}
        <div class="hexagon-hero w-full">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 15234.51 679.39" aria-hidden="true">
                <defs>
                    <style>
                        .cls-1 { fill: #f26423; }
                        .cls-2 { fill: #2c2c2c; stroke: #2c2c2c; stroke-width: 1; }
                        .cls-3 { fill: #f1bc1b; }
                    </style>
                </defs>
                <path class="cls-2" d="M7520.42,274.41v105.97h-10v-111.75c.17.11.34.22.52.32l9.48,5.46Z" />
                <polygon class="cls-2" points="7540.42 285.92 7540.42 380.38 7530.42 380.38 7530.42 280.16 7540.42 285.92" />
                <polygon class="cls-2" points="7560.42 297.43 7560.42 380.38 7550.42 380.38 7550.42 291.67 7560.42 297.43" />
                <polygon class="cls-2" points="7580.42 308.94 7580.42 380.38 7570.42 380.38 7570.42 303.18 7580.42 308.94" />
                <polygon class="cls-2" points="7600.42 320.45 7600.42 380.38 7590.42 380.38 7590.42 314.69 7600.42 320.45" />
                <path class="cls-2" d="M7620.42,326.17v54.21h-10v-54.42c1.7.65,3.5.97,5.29.97,1.59,0,3.18-.25,4.71-.76Z" />
                <polygon class="cls-2" points="7640.42 315.03 7640.42 380.38 7630.42 380.38 7630.42 320.78 7640.42 315.03" />
                <polygon class="cls-2" points="7660.42 303.52 7660.42 380.38 7650.42 380.38 7650.42 309.28 7660.42 303.52" />
                <polygon class="cls-2" points="7680.42 292.01 7680.42 380.38 7670.42 380.38 7670.42 297.77 7680.42 292.01" />
                <polygon class="cls-2" points="7700.42 280.5 7700.42 380.38 7690.42 380.38 7690.42 286.26 7700.42 280.5" />
                <polygon class="cls-2" points="7720.42 268.99 7720.42 380.38 7710.42 380.38 7710.42 274.75 7720.42 268.99" />
                <path class="cls-3" d="M7714.71,56.52c1.24.71,2.01,2.04,2.01,3.47v111.96c0,1.43-.76,2.75-2.01,3.47l-97.29,55.99c-1.23.71-2.76.71-3.99,0l-97.29-55.99c-1.24-.71-2.01-2.04-2.01-3.47V59.99c0-1.43.76-2.75,2.01-3.47l97.29-55.99c1.23-.71,2.76-.71,3.99,0l97.29,55.99ZM7552.76,77.6c-1.24.71-2,2.04-2,3.47v69.8c0,1.43.76,2.75,2,3.47l60.66,34.91c1.23.71,2.76.71,3.99,0l60.66-34.91c1.24-.71,2.01-2.04,2.01-3.47v-69.8c0-1.43-.76-2.75-2.01-3.47l-60.66-34.91c-1.23-.71-2.76-.71-3.99,0l-60.66,34.91Z" />
                <path class="cls-1" d="M7714.71,98.43c1.24.71,2,2.04,2,3.47v112.07c0,1.43-.76,2.75-2,3.47l-97.29,56.04c-1.24.71-2.76.71-3.99,0l-97.29-56.04c-1.24-.71-2-2.03-2-3.47v-112.07c0-1.43.76-2.75,2-3.47l97.29-56.04c1.24-.71,2.76-.71,3.99,0l97.29,56.04ZM7552.76,119.53c-1.24.71-2,2.04-2,3.47v69.87c0,1.43.76,2.75,2,3.47l60.66,34.94c1.24.71,2.76.71,3.99,0l60.66-34.94c1.24-.71,2-2.04,2-3.47v-69.87c0-1.43-.76-2.75-2-3.47l-60.66-34.94c-1.24-.71-2.76-.71-3.99,0l-60.66,34.94Z" />
                <path class="cls-2" d="M7714.71,140.44c1.24.71,2.01,2.04,2.01,3.47v111.96c0,1.43-.76,2.75-2.01,3.47l-97.29,55.99c-1.23.71-2.76.71-3.99,0l-97.29-55.99c-1.24-.71-2.01-2.04-2.01-3.47v-111.96c0-1.43.76-2.75,2.01-3.47l97.29-55.99c1.23-.71,2.76-.71,3.99,0l97.29,55.99ZM7552.76,161.52c-1.24.71-2,2.04-2,3.47v69.8c0,1.43.76,2.75,2,3.47l60.66,34.91c1.23.71,2.76.71,3.99,0l60.66-34.91c1.24-.71,2.01-2.04,2.01-3.47v-69.8c0-1.43-.76-2.75-2.01-3.47l-60.66-34.91c-1.23-.71-2.76-.71-3.99,0l-60.66,34.91Z" />
                <rect class="cls-2" x="7510.42" y="380.39" width="10" height="190" />
                <rect class="cls-2" x="7530.42" y="380.39" width="10" height="220" />
                <rect class="cls-2" x="7550.42" y="380.39" width="10" height="240" />
                <rect class="cls-2" x="7570.42" y="380.39" width="10" height="260" />
                <rect class="cls-2" x="7590.42" y="380.39" width="10" height="280" />
                <rect class="cls-2" x="7610.42" y="380.39" width="10" height="299" />
                <rect class="cls-2" x="7630.42" y="380.39" width="10" height="280" />
                <rect class="cls-2" x="7650.42" y="380.39" width="10" height="260" />
                <rect class="cls-2" x="7670.42" y="380.39" width="10" height="240" />
                <rect class="cls-2" x="7690.42" y="380.39" width="10" height="220" />
                <rect class="cls-2" x="7710.42" y="380.39" width="10" height="190" />
                <rect class="cls-2" x="7710.42" y="570.39" width="7524.09" height="10" />
                <rect class="cls-2" y="570.39" width="7520.42" height="10" />
                <rect class="cls-2" y="590.39" width="7540.42" height="10" />
                <rect class="cls-2" y="650.39" width="7600.42" height="10" />
                <rect class="cls-2" y="630.39" width="7580.42" height="10" />
                <rect class="cls-2" y="610.39" width="7560.42" height="10" />
                <rect class="cls-2" x="7690.42" y="590.39" width="7544.09" height="10" />
                <rect class="cls-2" x="7630.42" y="650.39" width="7604.09" height="10" />
                <rect class="cls-2" y="668.85" width="15234.51" height="10" />
                <rect class="cls-2" x="7650.42" y="630.39" width="7584.09" height="10" />
                <rect class="cls-2" x="7670.42" y="610.39" width="7564.09" height="10" />
            </svg>
        </div>
    </section>

    {{-- Search Section with Search Content --}}
    <section class="hero-full-width relative flex flex-col items-center justify-start px-4 sm:px-6 lg:px-8 py-16 sm:py-24 bg-off-white" aria-label="Documentation search">
        {{-- Search Content --}}
        <div class="flex flex-col items-center justify-start gap-6 sm:gap-8 md:gap-12 relative w-full ">
            {{-- Search Header --}}
            <div class="flex flex-col items-center justify-start gap-6 sm:gap-8 w-full">
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-inter-tight font-bold leading-none text-charcoal text-center mb-2">
                    How can we help you today?
                </h2>
                <p class="text-lg sm:text-xl font-inter-tight font-medium leading-relaxed text-center text-charcoal max-w-4xl">
                    Find step-by-step guides, best practices, and expert tips to unlock your store's full potential.
                </p>
            </div>

            {{-- Search Bar --}}
            <div class="w-full max-w-2xl">
                <button
                    class="flex items-center justify-between w-full px-4 sm:px-6 py-3 sm:py-4 bg-white border-2 border-gray-300 hover:border-orange hover:shadow-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-orange focus:ring-offset-2"
                    id="homepage-search"
                    aria-label="Search the documentation"
                >
                    <span class="text-base sm:text-lg font-inter-tight font-normal text-gray-600">
                        Search the documentation
                    </span>
                    <svg class="w-5 h-5 flex-shrink-0 text-orange" width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8 16C9.77498 15.9996 11.4988 15.4054 12.897 14.312L17.293 18.708L18.707 17.294L14.311 12.898C15.405 11.4997 15.9996 9.77544 16 8C16 3.589 12.411 0 8 0C3.589 0 0 3.589 0 8C0 12.411 3.589 16 8 16ZM8 2C11.309 2 14 4.691 14 8C14 11.309 11.309 14 8 14C4.691 14 2 11.309 2 8C2 4.691 4.691 2 8 2Z" fill="currentColor"/>
                    </svg>
                </button>
            </div>
        </div>
    </section>

    {{-- Categories Grid Section --}}
    <section class="flex flex-col items-center justify-start gap-8 sm:gap-12 py-12 sm:py-16">
        <div class="max-w-7xl xl:max-w-8xl mx-auto w-full px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8 w-full">
            @foreach($categories as $category)
                {{-- Category Card --}}
                <div class="bg-white flex flex-col gap-6 items-start justify-start p-6 sm:p-8 border-2 border-gray-200 border-t-4 border-t-yellow shadow-sm hover:shadow-lg hover:border-t-orange hover:-translate-y-1 transition-all duration-200 focus-within:ring-2 focus-within:ring-orange focus-within:ring-offset-2 h-full">
                    <div class="flex items-center justify-start gap-6 w-full">
                        <h3 class="text-3xl font-inter-tight font-bold leading-tight text-charcoal">
                            {{ $category['name'] }}
                        </h3>
                    </div>
                    @if($category['description'])
                        <div class="flex flex-col items-start justify-start gap-4 w-full">
                            <p class="text-base font-inter-tight font-normal leading-relaxed text-charcoal">
                                {{ $category['description'] }}
                            </p>
                        </div>
                    @endif

                    {{-- Article Links --}}
                    <div class="flex flex-col gap-2 items-start justify-start w-full flex-grow">
                        @foreach($category['articles'] as $article)
                            <a href="/merchant/{{ $article['path'] }}" class="flex items-center gap-3 p-3 -mx-3 w-full hover:bg-off-white transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-orange focus:ring-inset no-underline">
                                <svg class="w-5 h-5 flex-shrink-0 text-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span class="flex-1 text-base font-inter-tight font-medium leading-normal text-charcoal">
                                    {{ $article['title'] }}
                                </span>
                            </a>
                        @endforeach
                    </div>

                    <a href="/merchant/{{ $category['slug'] }}" class="text-base font-inter-tight leading-relaxed text-red no-underline hover:text-orange transition-colors focus:outline-none focus:ring-2 focus:ring-orange focus:ring-offset-2 rounded mt-auto">See all in this section</a>
                </div>
            @endforeach
            </div>
        </div>
    </section>
@endsection