@extends('partials.layout')

@section('content')
    {{-- Hero Section - Commerce begins with Community --}}
    <section class="bg-white flex flex-col items-center justify-center py-12 sm:pt-16 md:pt-20 lg:pt-24 gap-8 sm:gap-12 md:gap-16 pb-0 mb-0">
        {{-- Main Hero Content --}}
        <div class="flex flex-col items-start justify-start text-charcoal">
            <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-inter-tight leading-none text-center sm:text-left font-bold mb-8">
                Commerce begins with Community.
            </h1>
            <p class="text-xl font-inter-tight font-medium leading-[1.4] text-center w-full">
                Your comprehensive guide to building, managing, and growing a successful Magento 2 store.
            </p>
        </div>

        <div class="w-full">
            <svg width="1728" height="680" viewBox="0 0 1728 680" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto">
                <rect x="759" y="191" width="10" height="190" fill="#2C2C2C"/>
                <rect x="779" y="191" width="10" height="190" fill="#2C2C2C"/>
                <rect x="799" y="191" width="10" height="190" fill="#2C2C2C"/>
                <rect x="819" y="191" width="10" height="190" fill="#2C2C2C"/>
                <rect x="839" y="191" width="10" height="190" fill="#2C2C2C"/>
                <rect x="859" y="191" width="10" height="190" fill="#2C2C2C"/>
                <rect x="879" y="191" width="10" height="190" fill="#2C2C2C"/>
                <rect x="899" y="191" width="10" height="190" fill="#2C2C2C"/>
                <rect x="919" y="191" width="10" height="190" fill="#2C2C2C"/>
                <rect x="939" y="191" width="10" height="190" fill="#2C2C2C"/>
                <rect x="959" y="191" width="10" height="190" fill="#2C2C2C"/>
                <path d="M859.833 80.2274C862.621 78.7439 865.967 78.7438 868.756 80.2274L869.032 80.3807L966.327 136.372C969.273 138.067 971.088 141.207 971.088 144.605V256.564C971.088 259.963 969.273 263.102 966.327 264.798L869.032 320.788C866.099 322.476 862.489 322.476 859.556 320.788L762.262 264.798C759.316 263.103 757.5 259.963 757.5 256.564V144.605C757.5 141.207 759.316 138.067 762.262 136.372L859.556 80.3807L859.833 80.2274ZM805.131 166.552V234.616L864.294 268.663L923.458 234.615V166.553L864.294 132.505L805.131 166.552Z" fill="#FAFAFA" stroke="#FAFAFA" stroke-width="11"/>
                <path d="M963.289 57.1386C964.53 57.8524 965.294 59.1744 965.294 60.6055V172.564C965.294 173.995 964.53 175.317 963.289 176.031L865.995 232.021C864.76 232.732 863.24 232.732 862.005 232.021L764.711 176.031C763.47 175.317 762.706 173.995 762.706 172.564V60.6055C762.706 59.1744 763.47 57.8524 764.711 57.1386L862.005 1.14808C863.24 0.437294 864.76 0.437294 865.995 1.14808L963.289 57.1386ZM801.341 78.2187C800.101 78.9325 799.337 80.2546 799.337 81.6856V151.483C799.337 152.915 800.101 154.237 801.341 154.95L862.005 189.861C863.24 190.572 864.76 190.572 865.995 189.861L926.658 154.95C927.899 154.237 928.663 152.915 928.663 151.483V81.6856C928.663 80.2546 927.899 78.9325 926.658 78.2187L865.995 43.3081C864.76 42.5973 863.24 42.5973 862.005 43.3081L801.341 78.2187Z" fill="#F1BC1B"/>
                <path d="M963.29 99.0441C964.53 99.7581 965.294 101.08 965.294 102.51V214.575C965.294 216.006 964.53 217.327 963.29 218.041L865.996 274.082C864.76 274.793 863.239 274.793 862.003 274.082L764.709 218.041C763.47 217.327 762.706 216.006 762.706 214.575V102.51C762.706 101.08 763.47 99.7581 764.709 99.0441L862.003 43.0035C863.239 42.2917 864.76 42.2917 865.996 43.0035L963.29 99.0441ZM801.34 120.143C800.1 120.857 799.337 122.179 799.337 123.609V193.476C799.337 194.906 800.1 196.228 801.34 196.942L862.003 231.884C863.239 232.595 864.76 232.595 865.996 231.884L926.66 196.942C927.899 196.228 928.663 194.906 928.663 193.476V123.609C928.663 122.179 927.899 120.857 926.66 120.143L865.996 85.2012C864.76 84.4894 863.239 84.4894 862.003 85.2012L801.34 120.143Z" fill="#F26423"/>
                <path d="M963.289 141.054C964.53 141.768 965.294 143.09 965.294 144.521V256.48C965.294 257.911 964.53 259.233 963.289 259.947L865.995 315.937C864.76 316.648 863.24 316.648 862.005 315.937L764.711 259.947C763.47 259.233 762.706 257.911 762.706 256.48V144.521C762.706 143.09 763.47 141.768 764.711 141.054L862.005 85.0639C863.24 84.3531 864.76 84.3531 865.995 85.0639L963.289 141.054ZM801.341 162.135C800.101 162.848 799.337 164.17 799.337 165.601V235.399C799.337 236.83 800.101 238.152 801.341 238.866L862.005 273.777C863.24 274.488 864.76 274.488 865.995 273.777L926.658 238.866C927.899 238.152 928.663 236.83 928.663 235.399V165.601C928.663 164.17 927.899 162.848 926.658 162.135L865.995 127.224C864.76 126.513 863.24 126.513 862.005 127.224L801.341 162.135Z" fill="#2C2C2C"/>
                <rect x="1728" y="670" width="10.0001" height="1728" transform="rotate(90 1728 670)" fill="#2C2C2C"/>
                <path d="M759 381H769V571H759V381Z" fill="#2C2C2C"/>
                <path d="M779 381H789V601H779V381Z" fill="#2C2C2C"/>
                <path d="M799 381H809V621H799V381Z" fill="#2C2C2C"/>
                <path d="M819 381H829V641H819V381Z" fill="#2C2C2C"/>
                <path d="M839 381H849V661H839V381Z" fill="#2C2C2C"/>
                <path d="M859 381H869V680H859V381Z" fill="#2C2C2C"/>
                <path d="M879 381H889V661H879V381Z" fill="#2C2C2C"/>
                <path d="M899 381H909V641H899V381Z" fill="#2C2C2C"/>
                <path d="M919 381H929V621H919V381Z" fill="#2C2C2C"/>
                <path d="M939 381H949V601H939V381Z" fill="#2C2C2C"/>
                <path d="M959 381H969V571H959V381Z" fill="#2C2C2C"/>
                <path d="M1728 571V581L959 581V571H1728Z" fill="#2C2C2C"/>
                <path d="M769 571V581L0 581L4.37116e-07 571L769 571Z" fill="#2C2C2C"/>
                <path d="M789 591V601H0L4.37116e-07 591L789 591Z" fill="#2C2C2C"/>
                <path d="M849 651V661H0L4.37116e-07 651L849 651Z" fill="#2C2C2C"/>
                <path d="M829 631V641H0L4.37116e-07 631L829 631Z" fill="#2C2C2C"/>
                <path d="M809 611V621H0L4.37116e-07 611L809 611Z" fill="#2C2C2C"/>
                <path d="M1728 591V601L939 601V591L1728 591Z" fill="#2C2C2C"/>
                <path d="M1728 651V661L879 661V651L1728 651Z" fill="#2C2C2C"/>
                <path d="M1728 631V641L899 641V631L1728 631Z" fill="#2C2C2C"/>
                <path d="M1728 611V621L919 621V611L1728 611Z" fill="#2C2C2C"/>
            </svg>

        </div>
    </section>

    {{-- Search Section with Search Content --}}
    <section class="relative flex flex-col gap-2.5 items-center justify-start pt-24">
        {{-- Search Content --}}
        <div class="flex flex-col items-center justify-start gap-6 sm:gap-8 md:gap-10 relative w-full ">
            {{-- Search Header --}}
            <div class="flex flex-col items-center justify-start gap-6 sm:gap-8 w-full">
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-inter-tight font-bold leading-none text-charcoal text-center mb-2">
                    How can we help you today?
                </h2>
                <p class="text-lg sm:text-xl font-inter-tight font-medium leading-[1.4] text-center text-charcoal max-w-4xl">
                    Find step-by-step guides, best practices, and expert tips to unlock your store's full potential.
                </p>
            </div>

            {{-- Search Bar --}}
            <div class="px-6 sm:px-12 md:px-16 lg:px-24 xl:px-32 w-full">
                <button class="flex flex-col gap-3 sm:gap-4 w-full pt-3 sm:pt-4 cursor-pointer hover:bg-off-white transition-colors duration-200 rounded-lg py-4 focus:outline-none focus:ring-2 focus:ring-orange focus:ring-opacity-50" id="homepage-search">
                    <div class="flex items-center justify-between w-full">
                        <span class="text-base sm:text-lg font-inter-tight leading-[1.42] text-charcoal">
                            Search the documentation
                        </span>
                        <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8 16C9.77498 15.9996 11.4988 15.4054 12.897 14.312L17.293 18.708L18.707 17.294L14.311 12.898C15.405 11.4997 15.9996 9.77544 16 8C16 3.589 12.411 0 8 0C3.589 0 0 3.589 0 8C0 12.411 3.589 16 8 16ZM8 2C11.309 2 14 4.691 14 8C14 11.309 11.309 14 8 14C4.691 14 2 11.309 2 8C2 4.691 4.691 2 8 2Z" fill="#F26423"/>
                        </svg>

                    </div>
                    <div class="bg-charcoal h-1 w-full"></div>
                </button>
            </div>
        </div>
    </section>

    {{-- Categories Grid Section --}}
    <section class="flex flex-col items-center justify-start gap-8 sm:gap-10 md:gap-12 px-6 sm:px-12 md:px-16 lg:px-24 xl:px-32 py-12 sm:py-16">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8 lg:gap-[30px] w-full">
            @foreach($categories as $category)
                {{-- Category Card --}}
                <div class="bg-white flex flex-col gap-[30px] items-start justify-start p-6 pt-8 pb-6 rounded border-t-4 border-yellow shadow-lg hover:shadow-xl transition-all duration-300 hover:border-orange focus-within:ring-2 focus-within:ring-orange focus-within:ring-opacity-20">
                    <div class="flex items-center justify-start gap-[26px] w-full">
                        <h3 class="text-3xl font-inter-tight font-bold leading-[1.2] text-charcoal">
                            {{ $category['name'] }}
                        </h3>
                    </div>
                    @if($category['description'])
                        <div class="flex flex-col items-start justify-start gap-[15px] w-full">
                            <p class="text-base font-inter-tight font-normal leading-[1.5] text-charcoal">
                                {{ $category['description'] }}
                            </p>
                        </div>
                    @endif

                    {{-- Article Links --}}
                    <div class="flex flex-col gap-2 items-start justify-start w-full">
                        @foreach($category['articles'] as $article)
                            <a href="/docs/{{ $article['path'] }}" class="bg-white flex flex-col gap-4 items-center justify-start pb-5 w-full hover:bg-off-white transition-colors duration-200 rounded-lg p-2 -mx-2">
                                <div class="bg-gray-light h-1 w-full"></div>
                                <div class="flex gap-2.5 items-start justify-start px-2 w-full">
                                    <div class="w-6 h-6 flex-shrink-0">
                                        <svg class="w-full h-full text-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <div class="flex flex-col items-start justify-center flex-1">
                                        <div class="text-base font-inter-tight font-medium leading-[1.5] text-charcoal">
                                            {{ $article['title'] }}
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <a href="/docs/{{ $category['slug'] }}" class="text-base font-inter-tight leading-[1.5] text-red underline decoration-solid">See all in this section</a>
                </div>
            @endforeach
        </div>
    </section>
@endsection