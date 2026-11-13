@extends('partials.layout')

@section('content')
<div class="relative bg-off-white">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
        <div class="text-center mb-10">
            <div class="inline-flex items-center gap-2 mb-3 font-mono text-xs font-medium uppercase text-orange-700">
                <i class="inline-block w-2.5 h-2.5 bg-orange" aria-hidden="true"></i>Community
            </div>
            <h1 class="text-3xl sm:text-4xl font-extrabold leading-tight text-charcoal mb-4">
                Contributors
            </h1>
            <p class="text-base text-gray-600 max-w-2xl mx-auto">
                Everyone who has helped write and build Magento Open Source Merchant Documentation, ranked by
                contributions to the <code class="font-mono text-sm">docs</code> and
                <code class="font-mono text-sm">docs-website</code> repositories.
            </p>
        </div>

        <x-contributor-box
            :contributors="$contributors"
            :edit-url="'https://github.com/magentoopensource/docs'"
            :page-title="'Contributors'"
            :expanded="true"
        />

        <p class="mt-6 text-sm text-gray-500 text-center">
            How this is counted: rankings come from merged code contributions only. Ideas submitted through
            the "Suggest an idea" card are credited by name in the GitHub issue they create, but don't add
            to the count above.
        </p>
    </div>
</div>
@endsection
