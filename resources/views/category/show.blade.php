@extends('partials.layout')

@section('content')
    {{-- Category Header --}}
    <x-category-header
        :title="$category['name']"
        :description="$category['description'] ?? 'Learn more about ' . $category['name']"
    />

    {{-- Articles Grid --}}
    <section class="flex flex-col gap-12 items-center justify-start px-6 sm:px-12 md:px-16 lg:px-24 xl:px-32 py-24 w-full">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 w-full max-w-[1440px]">
            @foreach($articles as $article)
            <div class="flex-1">
                <x-article-card
                    title="{{ $article['title'] }}"
                    description="{{ $article['description'] }}"
                    difficulty="{{ strtolower($article['difficulty']) }}"
                    icon="product"
                    readTime="{{ $article['read_time'] }} minute read"
                    link="{{ $article['url'] }}"
                />
            </div>
            @endforeach
        </div>
    </section>
@endsection
