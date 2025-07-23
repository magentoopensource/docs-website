@extends('partials.layout')

@section('content')
    <x-accessibility.skip-to-content-link/>
    
    {{-- Frame 54 Header - Enhanced version --}}
    <x-frame54-header />

    {{-- New Figma Documentation Layout --}}
    <x-figma-doc-layout>
        {{-- Page Heading Section --}}
        <div class="flex flex-col gap-6 items-start justify-start mb-8 w-full">
            @if (isset($title))
                <div class="font-alegreya font-bold text-slate-900 text-4xl leading-[1.1] w-full">
                    {{ $title }}
                </div>
            @endif
            <div class="font-alegreya font-light text-waterloo-950 text-base leading-6 w-full">
                Standard paragraph text aliquam parturient viverra phasellus mus dolor nulla in scelerisque nulla elementum morbi eleifend scelerisque vestibulum a blandit elementum ligula a nam phasellus a dui a. Dis proin sem id magna consequat metus magnis vestibulum vel dictum nisi consequat parturient at.
            </div>
        </div>

        {{-- Example Boxouts --}}
        <x-figma-boxouts type="standard">
            <x-figma-boxout-item text="Boxout text example" />
            <x-figma-boxout-item text="Boxout text example" />
            <x-figma-boxout-item text="Boxout text example" />
            <x-figma-boxout-item text="Boxout text example" />
            <x-figma-boxout-item text="Boxout text example" />
        </x-figma-boxouts>

        <x-figma-boxouts type="featured">
            <x-figma-boxout-item text="Boxout text example" />
            <x-figma-boxout-item text="Boxout text example" />
            <x-figma-boxout-item text="Boxout text example" />
            <x-figma-boxout-item text="Boxout text example" />
            <x-figma-boxout-item text="Boxout text example" />
        </x-figma-boxouts>

        <x-figma-boxouts type="tip" />

        <x-figma-boxouts type="warning" />

        {{-- Main Content Section --}}
        <div class="flex flex-col gap-5 items-start justify-start w-full">
            <div class="font-alegreya font-bold text-slate-900 text-2xl leading-[1.333] w-full">
                This is a standard heading
            </div>
            <div class="flex flex-col gap-2.5 items-end justify-start w-full">
                {{-- Enhanced content with Figma styling --}}
                <div class="figma-docs-content w-full">
                    {!! $content !!}
                </div>
                
                {{-- Image Caption Container --}}
                <div class="flex flex-row gap-2.5 items-center justify-center pb-1.5 pt-0 px-0 border-b border-flamingo-200">
                    <div class="font-alegreya text-waterloo-900 text-sm leading-[1.333] text-right">
                        This is the caption for image
                    </div>
                </div>
            </div>
        </div>

        {{-- Edit Page Button --}}
        <div class="mt-12 pt-8 border-t border-gray-200">
            <a href="{{ $edit_link }}"
                target="_blank"
                class="inline-flex items-center justify-center px-6 py-3 text-base font-medium text-white bg-flamingo-400 hover:bg-flamingo-500 transition-all duration-200 rounded-md shadow-sm hover:shadow-md">
                <svg fill="currentColor" class="w-4 h-4 mr-2" viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z">
                    </path>
                </svg>
                Edit this page
            </a>
        </div>
    </x-figma-doc-layout>

    <style>
    /* Enhanced documentation content styling */
    .figma-docs-content {
        font-family: 'Alegreya Sans', sans-serif;
        line-height: 1.75;
        color: #434246; /* Waterloo 900 */
    }

    .figma-docs-content h1 {
        @apply text-3xl font-bold mb-8 text-waterloo-900 font-alegreya;
    }

    .figma-docs-content h2 {
        @apply text-2xl font-semibold mb-6 text-waterloo-900 font-alegreya mt-12;
    }

    .figma-docs-content h3 {
        @apply text-xl font-semibold mb-4 text-waterloo-900 font-alegreya mt-8;
    }

    .figma-docs-content h4 {
        @apply text-lg font-medium mb-3 text-waterloo-900 font-alegreya mt-6;
    }

    .figma-docs-content p {
        @apply mb-6 text-waterloo-900 font-alegreya text-base leading-7;
    }

    .figma-docs-content a {
        @apply text-flamingo-400 hover:text-flamingo-500 transition-colors underline;
    }

    .figma-docs-content ul, .figma-docs-content ol {
        @apply mb-6 pl-6;
    }

    .figma-docs-content li {
        @apply mb-2 text-waterloo-900 font-alegreya;
    }

    .figma-docs-content code {
        @apply bg-gray-100 text-blackrock-900 px-2 py-1 rounded text-sm font-mono;
    }

    .figma-docs-content pre {
        @apply bg-blackrock-900 text-white rounded-lg p-6 mb-8 overflow-x-auto;
    }

    .figma-docs-content blockquote {
        @apply border-l-4 border-flamingo-400 bg-flamingo-50 p-6 mb-6 rounded-r-lg;
    }

    .figma-docs-content table {
        @apply w-full border-collapse mb-8 bg-white rounded-lg shadow-sm overflow-hidden;
    }

    .figma-docs-content th {
        @apply font-semibold text-waterloo-900 bg-gray-50 px-6 py-4 text-left border-b border-gray-200;
    }

    .figma-docs-content td {
        @apply px-6 py-4 text-waterloo-900 border-b border-gray-100;
    }

    .figma-docs-content tr:last-child td {
        @apply border-b-0;
    }

    /* Smooth scrolling for anchor links */
    html {
        scroll-behavior: smooth;
    }

    /* Anchor offset for fixed header */
    .figma-docs-content h1[id],
    .figma-docs-content h2[id],
    .figma-docs-content h3[id],
    .figma-docs-content h4[id] {
        scroll-margin-top: 6rem;
    }
    </style>
@endsection
