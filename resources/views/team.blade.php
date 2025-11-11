@extends('partials.layout')

@section('content')
    {{-- Hero Section --}}
    <section class="bg-white flex flex-col items-center justify-center py-12 sm:py-16 md:py-20 lg:py-24 px-6 sm:px-8 md:px-12">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-4xl sm:text-5xl md:text-6xl font-inter-tight font-normal leading-none text-charcoal mb-6">
                Meet the Team
            </h1>
            <p class="text-lg sm:text-xl font-inter-tight font-normal leading-snug text-charcoal max-w-3xl mx-auto">
                The passionate people behind Magento Merchant Documentation. We're dedicated to helping merchants succeed with clear, comprehensive, and actionable documentation.
            </p>
        </div>
    </section>

    {{-- Team Grid Section --}}
    <section class="bg-off-white py-12 sm:py-16 md:py-20 lg:py-24 px-6 sm:px-8 md:px-12">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-12">
                @foreach($team as $member)
                <div class="bg-white rounded-lg border-t-4 border-yellow shadow-lg hover:shadow-xl transition-all duration-300 hover:border-orange overflow-hidden">
                    {{-- Avatar --}}
                    <div class="flex items-center justify-center pt-8 pb-6">
                        <div class="w-32 h-32 rounded-full overflow-hidden bg-gray-light flex items-center justify-center">
                            @if(isset($member['github_username']))
                                <img src="https://unavatar.io/github/{{ $member['github_username'] }}"
                                     alt="{{ $member['name'] }}"
                                     class="w-full h-full object-cover"
                                     loading="lazy">
                            @else
                                <svg class="w-20 h-20 text-gray-darkest" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            @endif
                        </div>
                    </div>

                    {{-- Info --}}
                    <div class="px-6 pb-6 text-center">
                        <h3 class="text-xl font-inter-tight font-semibold text-charcoal mb-2">
                            {{ $member['name'] }}
                        </h3>

                        @if(isset($member['role']))
                        <p class="text-base font-inter-tight text-orange mb-3">
                            {{ $member['role'] }}
                        </p>
                        @endif

                        @if(isset($member['location']))
                        <div class="flex items-center justify-center gap-1.5 text-sm text-gray-darkest mb-4">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="font-inter-tight">{{ $member['location'] }}</span>
                        </div>
                        @endif

                        @if(isset($member['bio']))
                        <p class="text-sm font-inter-tight text-charcoal leading-relaxed mb-4">
                            {{ $member['bio'] }}
                        </p>
                        @endif

                        {{-- Social Links --}}
                        <div class="flex items-center justify-center gap-4 pt-4 border-t border-gray-light">
                            @if(isset($member['github_username']))
                            <a href="https://github.com/{{ $member['github_username'] }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="text-gray-darkest hover:text-orange transition-colors duration-200">
                                <span class="sr-only">GitHub</span>
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/>
                                </svg>
                            </a>
                            @endif

                            @if(isset($member['twitter_username']))
                            <a href="https://twitter.com/{{ $member['twitter_username'] }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="text-gray-darkest hover:text-orange transition-colors duration-200">
                                <span class="sr-only">Twitter</span>
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                </svg>
                            </a>
                            @endif

                            @if(isset($member['linkedin_username']))
                            <a href="https://linkedin.com/in/{{ $member['linkedin_username'] }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="text-gray-darkest hover:text-orange transition-colors duration-200">
                                <span class="sr-only">LinkedIn</span>
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                </svg>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Call to Action Section --}}
    <section class="bg-white py-12 sm:py-16 md:py-20 px-6 sm:px-8 md:px-12">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-3xl sm:text-4xl font-inter-tight font-semibold text-charcoal mb-6">
                Want to Contribute?
            </h2>
            <p class="text-lg font-inter-tight text-charcoal leading-relaxed mb-8">
                We're always looking for passionate community members to help improve our documentation.
                Whether you're fixing typos, adding examples, or writing new guides, every contribution matters.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 items-center justify-center">
                <a href="https://github.com/mage-os/devdocs"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="inline-flex items-center gap-2 px-8 py-3 bg-orange text-white font-inter-tight font-medium rounded hover:bg-orange transition-colors duration-200">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/>
                    </svg>
                    Contribute on GitHub
                </a>
                <a href="/docs"
                   class="inline-flex items-center gap-2 px-8 py-3 bg-white text-charcoal font-inter-tight font-medium rounded border-2 border-charcoal hover:bg-off-white transition-colors duration-200">
                    Browse Documentation
                </a>
            </div>
        </div>
    </section>
@stop
