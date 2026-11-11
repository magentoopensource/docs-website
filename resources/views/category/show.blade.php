@extends('partials.layout')

@push('jsonld')
@include('partials.jsonld', ['data' => \App\Services\StructuredData::graph(
    \App\Services\StructuredData::breadcrumb([
        ['Home', rtrim(url('/'), '/') . '/'],
        [$category['name'], url($canonical)],
    ])
)])
@endpush

@section('content')
    {{-- Category Header --}}
    <x-category-header
        :title="$category['name']"
        :description="$category['description'] ?? 'Learn more about ' . $category['name']"
    />

    {{-- Articles Grid --}}
    <section class="flex flex-col gap-12 items-center justify-start py-24 w-full">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 w-full max-w-7xl xl:max-w-8xl mx-auto px-4 sm:px-6 lg:px-8">
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

    {{-- Contributor block (compact): hub pages get it; article pages keep the top-contributors row --}}
    <div class="w-full max-w-7xl xl:max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 pb-24">
        <x-contributor-box layout="compact" :page-title="$category['name']" />
    </div>
@endsection
