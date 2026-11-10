{{--
    Contributor box: top-3 podium, a "places" meter for the top 10 (full list behind a
    disclosure), a "Suggest an idea" card and an "Improve this page" card.

    No Alpine — it throws on every merchant docs page (known live bug, own ticket). This
    is server-rendered with a small scoped vanilla script using data-* hooks for the radio
    change, the top-10 toggle and building the mailto: on click.

    Shared contract: MA-DOCS-IDEAS-WIDGET.md §6 (template names, field id `page_url`, title
    prefixes, mail subject format) must stay identical to the issue forms, the developer-docs
    contributors.js and this file.

    Styling note: the compiled merchant CSS (public/build/assets/app-*.css) is stale against
    this source tree — most gap-x-*, items-end, peer-*, tabular-nums, arbitrary bracket values
    and several color shades used by the visual design are NOT compiled. Verified with a
    word-boundary-safe check against the built CSS before writing this file. Utility classes
    are used only where confirmed present; everything else is scoped CSS below under .qbcb.
--}}
@props(['editUrl' => '', 'pageTitle' => null, 'expanded' => false, 'contributors' => null, 'hub' => false, 'layout' => 'card'])

@php
    // Hub pages (landing, /merchant, section indexes) and the contributors page have no source
    // file of their own: their improve card opens the docs repository and says so.
    // layout: 'card' (the full two-card box), 'compact' (hub pages: one row of actions) or
    // 'landing' (inside the landing page's "Built by the Community" section: no eyebrow).
    $repoCard = $hub || $expanded || $layout !== 'card';
    if ($repoCard) {
        $editUrl = 'https://github.com/magentoopensource/docs';
    }

    // Issue forms (idea-content.yml, idea-website.yml) live in the docs content repo.
    $ideaRepo = 'magentoopensource/docs';

    // Anonymous Blade components don't inherit the including view's data, so a caller that
    // already injected GitHubContributorsService (e.g. ContributorsController) passes the
    // list in; docs.blade.php's existing include (out of scope for this task) doesn't, so we
    // fall back to resolving the service from the container here — never a bare `new`.
    // The per-page box no longer shows contributors (only the contributors page does), so the
    // service is only called for the expanded variant.
    if ($contributors === null) {
        $contributors = $expanded ? app(\App\Services\GitHubContributorsService::class)->getTopContributors(10) : [];
    }

    $boxId = 'gh-cbox-' . \Illuminate\Support\Str::random(6);
    $top10Id = $boxId . '-top10';
    $titleId = $boxId . '-title';

    // This component is an anonymous Blade component; it does not automatically inherit the
    // including view's $title. the including view's <x-contributor-box> include is
    // out of scope for this task, so when no explicit page title is passed we derive a
    // reasonable label from the route slug. Good enough for a title prefix on an issue form,
    // not a substitute for the real H1.
    if ($pageTitle === null) {
        $slug = request()->route('page') ?: request()->route('category');
        $pageTitle = $slug
            ? \Illuminate\Support\Str::title(str_replace(['-', '_'], ' ', \Illuminate\Support\Str::afterLast($slug, '/')))
            : 'Merchant Documentation';
    }
    $pageUrl = url()->current();

    $ideaIssueBase = "https://github.com/{$ideaRepo}/issues/new";
    $ideaTypes = [
        'content' => [
            'label' => 'A docs topic',
            'hint' => 'New content or a missing subject',
            'mailLabel' => 'Topic',
            'template' => 'idea-content.yml',
            'titlePrefix' => '[Docs topic] ',
        ],
        'website' => [
            'label' => 'This website',
            'hint' => 'Navigation, search, layout',
            'mailLabel' => 'Website',
            'template' => 'idea-website.yml',
            'titlePrefix' => '[Website] ',
        ],
    ];

    foreach ($ideaTypes as $type => $meta) {
        $issueTitle = $type === 'website' ? $meta['titlePrefix'] . $pageTitle : $meta['titlePrefix'];
        $ideaTypes[$type]['issueUrl'] = $ideaIssueBase . '?' . http_build_query([
            'template' => $meta['template'],
            'title' => $issueTitle,
            'page_url' => $pageUrl,
        ]);
        $mailBody = "Page: {$pageUrl}\n\n" . ($type === 'website'
            ? "What should we improve?\n\n\nWhy would it help?\n\n"
            : "What topic is missing?\n\n\nWho is it for? (merchant / developer / both)\n\n")
            . "\n\xE2\x80\x94\nSent from the docs \xE2\x80\x9CSuggest an idea\xE2\x80\x9D widget";
        $ideaTypes[$type]['mailSubject'] = "[Docs idea \xE2\x80\x93 {$meta['mailLabel']}] {$pageTitle}";
        $ideaTypes[$type]['mailBody'] = $mailBody;
    }

    $defaultType = 'content';
    $places = 10;
    $taken = count($contributors);
    $open = $places - $taken;
@endphp

@if($layout === 'compact')
{{-- Compact block for hub pages (Carl, 2026-10-05): heading, then one row of actions. --}}
<section
    data-gh-cbox
    id="{{ $boxId }}"
    class="qbcb qbcb--compact mt-16 border-2 border-charcoal bg-white"
    aria-labelledby="{{ $titleId }}"
>
    <div class="px-5 sm:px-6 py-5">
        <div class="qbcb-kicker inline-flex items-center gap-2 mb-1.5 font-mono text-xs font-medium uppercase text-orange-700">
            <i class="qbcb-dot" aria-hidden="true"></i>Written by the community
        </div>
        <h2 id="{{ $titleId }}" class="m-0 text-lg sm:text-xl font-extrabold leading-tight text-charcoal">
            Help build the Magento docs, and get your name on them
        </h2>
        <div class="qbcb-compact-row">
            <div class="qbcb-compact-group">
                <h3 class="qbcb-compact-label m-0 text-sm font-bold text-charcoal">Suggest an idea</h3>
                <fieldset class="m-0 p-0 border-0 min-w-0">
                    <legend class="sr-only">What kind of idea is it?</legend>
                    <div class="qbcb-choices border border-charcoal">
                        @foreach($ideaTypes as $type => $meta)
                            <label class="qbcb-choice">
                                <input
                                    type="radio"
                                    name="{{ $boxId }}-idea-type"
                                    value="{{ $type }}"
                                    data-gh-cbox-issue-url="{{ $meta['issueUrl'] }}"
                                    data-gh-cbox-mail-subject="{{ $meta['mailSubject'] }}"
                                    data-gh-cbox-mail-body="{{ $meta['mailBody'] }}"
                                    {{ $type === $defaultType ? 'checked' : '' }}
                                    class="qbcb-choice-input"
                                />
                                <span class="qbcb-choice-label"><strong class="text-[13px] font-semibold">{{ $meta['label'] }}</strong></span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>
                <div class="flex flex-wrap items-center gap-x-4 gap-y-2">
                    <a
                        data-gh-cbox-github
                        href="{{ $ideaTypes[$defaultType]['issueUrl'] }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="qbcb-cta-github inline-flex items-center justify-center gap-2 px-4 py-2 bg-red-600 border-2 border-red-600 text-white text-sm font-semibold no-underline focus:outline-none focus:ring-2 focus:ring-orange focus:ring-offset-2"
                    >
                        <svg class="w-4 h-4 flex-none" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0016 8c0-4.42-3.58-8-8-8z"/></svg>
                        Suggest on GitHub<span class="sr-only"> (opens in a new tab)</span>
                    </a>
                    <a
                        data-gh-cbox-mail
                        href="#"
                        class="qbcb-mail-link py-1 text-[13px] font-medium text-charcoal underline hover:text-orange-700 focus:outline-none focus:ring-2 focus:ring-orange focus:ring-offset-2"
                    >
                        Email us
                    </a>
                </div>
            </div>
            <div class="qbcb-compact-group qbcb-compact-group--edit">
                <h3 class="qbcb-compact-label m-0 text-sm font-bold text-charcoal">Improve a page</h3>
                <a
                    href="{{ $editUrl }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="qbcb-cta-edit inline-flex items-center justify-center gap-2 px-4 py-2 bg-white hover:bg-charcoal border-2 border-charcoal text-charcoal hover:text-white text-sm font-semibold no-underline focus:outline-none focus:ring-2 focus:ring-orange focus:ring-offset-2"
                >
                    <svg class="w-4 h-4 flex-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Open the docs repository<span class="sr-only"> (opens in a new tab)</span>
                </a>
            </div>
        </div>
    </div>
</section>
@else
<section
    data-gh-cbox
    id="{{ $boxId }}"
    class="qbcb {{ $expanded ? 'qbcb--expanded' : '' }} {{ $layout === 'landing' ? 'mt-12 text-left' : 'mt-16' }} border-2 border-charcoal bg-white"
    aria-labelledby="{{ $titleId }}"
>
    {{-- Head: kicker and heading; the contributors page adds the subtext and podium (Carl, 2026-10-04). --}}
    <div class="qbcb-head px-6 pt-6 pb-5 border-b border-gray-200 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-6">
        <div class="min-w-0">
            @unless($layout === 'landing')
            <div class="qbcb-kicker inline-flex items-center gap-2 mb-2 font-mono text-xs font-medium uppercase text-orange-700">
                <i class="qbcb-dot" aria-hidden="true"></i>Written by the community
            </div>
            @endunless
            <h2 id="{{ $titleId }}" class="m-0 mb-1.5 text-xl sm:text-2xl font-extrabold leading-tight text-charcoal">
                Help build the Magento docs, and get your name on them
            </h2>
            @if($expanded)
                <p class="qbcb-subtext m-0 text-sm text-gray-600">
                    Every merged edit counts toward the top 10 contributors. Ideas shape what we write next.
                </p>
            @endif
        </div>


        @if($expanded && count($contributors))
            <div class="qbcb-podium" role="group" aria-label="Top 3 contributors">
                @foreach([2, 1, 3] as $rank)
                    @php $c = $contributors[$rank - 1] ?? null; @endphp
                    @if($c)
                        <a
                            href="{{ $c['html_url'] }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="qbcb-step qbcb-step--{{ $rank }} group flex flex-col items-center gap-1.5 no-underline text-charcoal focus:outline-none focus:ring-2 focus:ring-orange focus:ring-offset-2"
                        >
                            <span class="qbcb-avatar">
                                <img src="{{ $c['avatar_url'] }}" alt="" width="60" height="60" loading="lazy" class="w-full h-full object-cover" />
                                <span class="qbcb-rank qbcb-rank--{{ $rank }}" aria-hidden="true">{{ $rank }}</span>
                            </span>
                            <span class="qbcb-login text-xs font-semibold group-hover:underline">
                                <span class="sr-only">Rank {{ $rank }}, </span>{{ $c['login'] }}
                            </span>
                            <span class="font-mono text-xs font-medium text-orange-700">
                                {{ number_format($c['contributions']) }}<span class="sr-only"> contributions (opens GitHub profile in a new tab)</span>
                            </span>
                        </a>
                    @endif
                @endforeach
            </div>
        @endif
    </div>

    @if($expanded)
    {{-- Places meter + top 10 (contributors page only) --}}
    <div class="qbcb-meter px-6 py-3.5 bg-gray-50 border-b border-gray-200 flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap items-center gap-3">
            <div class="qbcb-cells" aria-hidden="true">
                @for($i = 0; $i < $places; $i++)
                    <span class="qbcb-cell {{ $i < $taken ? 'is-on' : ($i === $taken ? 'is-next' : 'is-open') }}"></span>
                @endfor
            </div>
            <span data-gh-cbox-meter-text class="text-sm text-charcoal">
                <b class="font-bold">{{ $taken }} of {{ $places }}</b> places taken.
                @if($open > 0)
                    <em class="not-italic font-semibold text-orange-700">{{ $open }} open.</em>
                    Place #{{ $taken + 1 }} is yours with 1 contribution.
                @endif
            </span>
        </div>

        @unless($expanded)
            <button
                type="button"
                data-gh-cbox-toggle
                aria-expanded="false"
                aria-controls="{{ $top10Id }}"
                class="qbcb-toggle inline-flex items-center gap-1.5 bg-transparent border-0 py-1 px-0 cursor-pointer text-sm font-semibold text-charcoal underline focus:outline-none focus:ring-2 focus:ring-orange focus:ring-offset-2"
            >
                <span data-gh-cbox-toggle-label>See the top 10</span>
                <svg data-gh-cbox-chevron class="qbcb-chevron w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
            </button>
        @endunless
    </div>

    {{-- Top 10 list --}}
    <div id="{{ $top10Id }}" class="qbcb-top10 px-6 pt-3 pb-4 border-b border-gray-200" @unless($expanded) hidden @endunless>
        <ol class="qbcb-rows list-none m-0 p-0">
            @for($p = 1; $p <= $places; $p++)
                @php $c = $contributors[$p - 1] ?? null; @endphp
                <li class="qbcb-row grid items-center py-2 border-b border-gray-200 text-sm">
                    <span class="font-mono text-xs font-medium text-gray-500">{{ str_pad((string) $p, 2, '0', STR_PAD_LEFT) }}</span>
                    @if($c)
                        <span class="qbcb-row-avatar"><img src="{{ $c['avatar_url'] }}" alt="" width="30" height="30" loading="lazy" class="w-full h-full object-cover" /></span>
                        <span class="truncate font-semibold text-charcoal">{{ $c['login'] }}</span>
                        <span class="qbcb-row-count font-mono text-xs font-medium text-orange-700 bg-orange-50">{{ number_format($c['contributions']) }}<span class="sr-only"> contributions</span></span>
                    @else
                        @php $next = $p === $taken + 1; @endphp
                        <span class="qbcb-row-avatar {{ $next ? 'is-next' : 'is-open' }}" aria-hidden="true"></span>
                        <span class="truncate {{ $next ? 'font-medium text-charcoal' : 'font-medium text-gray-400' }}">{{ $next ? 'Open place, this could be you' : 'Open place' }}</span>
                        <span></span>
                    @endif
                </li>
            @endfor
        </ol>
        <p class="mt-3 mb-0 text-xs text-gray-500">Counts combine the <code class="font-mono">docs</code> and <code class="font-mono">docs-website</code> repositories. Updated periodically.</p>

    </div>
    @endif

    {{-- Ways to contribute --}}
    <div class="grid grid-cols-1 {{ $editUrl ? 'md:grid-cols-2' : '' }}">
        {{-- Suggest an idea --}}
        <div class="qbcb-card px-6 pt-5 pb-6">
            <div class="flex flex-wrap justify-between items-baseline gap-2 mb-3">
                <h3 class="m-0 text-base font-bold text-charcoal">Suggest an idea</h3>
                <span class="qbcb-chip font-mono text-[10px] font-medium uppercase text-orange-700 bg-orange-50">Credited in the issue</span>
            </div>
            <p class="mt-0 mb-3.5 text-sm text-gray-600">Something missing, or something we could do better? No code needed.</p>

            <fieldset class="m-0 p-0 border-0 min-w-0 mb-3.5">
                <legend class="sr-only">What kind of idea is it?</legend>
                <div class="qbcb-choices border border-charcoal">
                    @foreach($ideaTypes as $type => $meta)
                        <label class="qbcb-choice">
                            <input
                                type="radio"
                                name="{{ $boxId }}-idea-type"
                                value="{{ $type }}"
                                data-gh-cbox-issue-url="{{ $meta['issueUrl'] }}"
                                data-gh-cbox-mail-subject="{{ $meta['mailSubject'] }}"
                                data-gh-cbox-mail-body="{{ $meta['mailBody'] }}"
                                {{ $type === $defaultType ? 'checked' : '' }}
                                class="qbcb-choice-input"
                            />
                            <span class="qbcb-choice-label">
                                <strong class="text-[13px] font-semibold">{{ $meta['label'] }}</strong>
                                <small class="text-xs opacity-80">{{ $meta['hint'] }}</small>
                            </span>
                        </label>
                    @endforeach
                </div>
            </fieldset>

            <div class="flex flex-wrap items-center gap-x-4 gap-y-2">
                <a
                    data-gh-cbox-github
                    href="{{ $ideaTypes[$defaultType]['issueUrl'] }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="qbcb-cta-github inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-red-600 border-2 border-red-600 text-white text-sm font-semibold no-underline focus:outline-none focus:ring-2 focus:ring-orange focus:ring-offset-2"
                >
                    <svg class="w-4 h-4 flex-none" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0016 8c0-4.42-3.58-8-8-8z"/></svg>
                    Suggest on GitHub<span class="sr-only"> (opens in a new tab)</span>
                </a>
                <a
                    data-gh-cbox-mail
                    href="#"
                    class="qbcb-mail-link py-1 text-[13px] font-medium text-charcoal underline hover:text-orange-700 focus:outline-none focus:ring-2 focus:ring-orange focus:ring-offset-2"
                >
                    Email us
                </a>
            </div>
        </div>

        {{-- Improve this page --}}
        @if($editUrl)
            <div class="qbcb-card qbcb-card--edit px-6 pt-5 pb-6 border-t md:border-t-0 md:border-l border-gray-200">
                <div class="flex flex-wrap justify-between items-baseline gap-2 mb-3">
                    <h3 class="m-0 text-base font-bold text-charcoal">{{ $repoCard ? 'Improve a page' : 'Improve this page' }}</h3>
                    <span class="qbcb-chip font-mono text-[10px] font-medium uppercase text-orange-700 bg-orange-50">Counts toward the top 10</span>
                </div>
                <p class="mt-0 mb-3.5 text-sm text-gray-600">{{ $repoCard ? 'Every documentation page carries an “Edit this page on GitHub” button. Open the repository to browse the source and start a pull request.' : 'Spotted something wrong? Fix it directly. Your edit becomes a pull request we review.' }}</p>
                <ul class="list-none m-0 p-0 mb-3.5 text-[13px] text-gray-600">
                    @foreach(['Typos and unclear wording', 'Steps that changed in Magento 2.4.8', 'Missing examples or screenshots'] as $bullet)
                        <li class="qbcb-bullet">{{ $bullet }}</li>
                    @endforeach
                </ul>
                <a
                    href="{{ $editUrl }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="qbcb-cta-edit inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white hover:bg-charcoal border-2 border-charcoal text-charcoal hover:text-white text-sm font-semibold no-underline focus:outline-none focus:ring-2 focus:ring-orange focus:ring-offset-2"
                >
                    <svg class="w-4 h-4 flex-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    {{ $repoCard ? 'Open the docs repository' : 'Edit this page on GitHub' }}<span class="sr-only"> (opens in a new tab)</span>
                </a>
            </div>
        @endif
    </div>

    <div class="qbcb-footer px-6 py-3 border-t border-gray-200 flex flex-wrap justify-between gap-x-4 gap-y-1 text-xs text-gray-500">
        <span>Ideas and edits on GitHub are public.</span>
        <span>Emailed ideas go straight to the docs team.</span>
    </div>
</section>
@endif

<style>
    /*
     * Scoped styles for the contributor box (.qbcb). The compiled merchant CSS
     * (public/build/assets/app-*.css) is stale against this source tree: gap-x-*,
     * items-end, peer-*, tabular-nums, border-dashed and most arbitrary bracket values
     * are not compiled (checked with a word-boundary-safe grep against the build output
     * before writing this). Only Tailwind classes confirmed present above are used;
     * everything else lives here. Colors are matched to tailwind.config.js tokens, with
     * gold/silver/bronze rank badges hand-picked since only "gold" has a defined shade
     * scale — silver/bronze have no token in this project.
     */
    .qbcb-dot { display: inline-block; width: 10px; height: 10px; background: #F26423; }
    .qbcb-podium { display: flex; align-items: flex-end; gap: 8px; }
    .qbcb-step--1 .qbcb-avatar { width: 60px; height: 60px; }
    .qbcb-step--2 .qbcb-avatar, .qbcb-step--3 .qbcb-avatar { width: 50px; height: 50px; }
    .qbcb--expanded .qbcb-step--1 .qbcb-avatar { width: 88px; height: 88px; }
    .qbcb--expanded .qbcb-step--2 .qbcb-avatar, .qbcb--expanded .qbcb-step--3 .qbcb-avatar { width: 70px; height: 70px; }
    .qbcb-avatar { position: relative; display: block; border: 2px solid #fff; outline: 1px solid #e5e7eb; background: #f1f1f1; overflow: hidden; }
    .qbcb-login { max-width: 96px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .qbcb-rank { position: absolute; top: -8px; right: -8px; display: flex; align-items: center; justify-content: center; width: 19px; height: 19px; font-size: 10px; font-weight: 800; }
    .qbcb-rank--1 { background: #F1BC1B; color: #2c2c2c; }
    .qbcb-rank--2 { background: #d9d9d9; color: #474747; }
    .qbcb-rank--3 { background: #d9a066; color: #442204; } /* bronze — no token in tailwind.config.js */

    .qbcb-cells { display: grid; grid-template-columns: repeat(10, 16px); gap: 4px; }
    .qbcb-cell { display: block; height: 16px; background: #fff; border: 1px solid #d1d5db; }
    .qbcb-cell.is-on { background: #2c2c2c; border-color: #2c2c2c; }
    .qbcb-cell.is-next { background: #FFF7F0; border: 2px solid #F26423; }
    .qbcb-cell.is-open { border-style: dashed; }

    .qbcb-chevron { transition: transform 0.2s; }
    .qbcb-toggle[aria-expanded="true"] .qbcb-chevron { transform: rotate(180deg); }
    @media (prefers-reduced-motion: reduce) { .qbcb-chevron { transition: none; } }

    .qbcb-rows { columns: 1; column-gap: 28px; }
    @media (min-width: 640px) { .qbcb-rows { columns: 2; } }
    .qbcb-row { grid-template-columns: 26px 30px minmax(0, 1fr) auto; column-gap: 10px; break-inside: avoid; }
    /* The docs typography numbers every `ol > li` with a ::before counter (resources/css/_typography.css).
       Inside this grid that pseudo-element becomes an extra cell and shifts every column, so switch it off here. */
    .qbcb-rows > .qbcb-row::before { content: none; display: none; }
    .qbcb-row-avatar { display: block; width: 30px; height: 30px; border: 1px solid #fff; outline: 1px solid #e5e7eb; background: #f1f1f1; overflow: hidden; }
    .qbcb-row-avatar.is-next { border: 2px solid #F26423; background: #FFF7F0; }
    .qbcb-row-avatar.is-open { border: 1px dashed #d1d5db; background: transparent; }
    .qbcb-row-count { padding: 2px 7px; }

    .qbcb-choices { display: grid; grid-template-columns: 1fr; }
    @media (min-width: 421px) { .qbcb-choices { grid-template-columns: 1fr 1fr; } }
    /* The docs styles give every `label` opacity .4, uppercase, 10px and wide tracking
       (resources/css/_components.css) and force `strong` to charcoal (_typography.css); undo
       both inside the idea choice so it matches the developer docs. */
    .qbcb-choice { position: relative; display: block; opacity: 1; text-transform: none; letter-spacing: normal; font-size: inherit; line-height: inherit; margin: 0; }
    .qbcb-choice-label strong { color: inherit; }
    .qbcb-choice + .qbcb-choice { border-top: 1px solid #2c2c2c; }
    @media (min-width: 421px) { .qbcb-choice + .qbcb-choice { border-top: 0; border-left: 1px solid #2c2c2c; } }
    .qbcb-choice-input { position: absolute; inset: 0; z-index: 1; margin: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; }
    .qbcb-choice-label { display: grid; gap: 1px; height: 100%; padding: 9px 12px; color: #2c2c2c; cursor: pointer; }
    .qbcb-choice-input:checked + .qbcb-choice-label { background: #2c2c2c; color: #fff; }
    .qbcb-choice-input:not(:checked):hover + .qbcb-choice-label { background: #f5f5f4; }
    .qbcb-choice-input:focus-visible + .qbcb-choice-label { outline: 3px solid #F26423; outline-offset: -3px; }

    /* Compact (hub pages): the two ways to contribute share one row from 1024px, stack below. */
    .qbcb-compact-row { display: grid; gap: 16px; margin-top: 16px; padding-top: 16px; border-top: 1px solid #e5e7eb; }
    @media (min-width: 1024px) { .qbcb-compact-row { grid-template-columns: minmax(0, 1fr) auto; align-items: center; column-gap: 32px; } }
    .qbcb-compact-group { display: flex; flex-wrap: wrap; align-items: center; gap: 10px 16px; }
    @media (min-width: 1024px) { .qbcb-compact-group--edit { padding-left: 32px; border-left: 1px solid #e5e7eb; } }
    .qbcb--compact .qbcb-choices { grid-template-columns: 1fr 1fr; }
    .qbcb--compact .qbcb-choice + .qbcb-choice { border-top: 0; border-left: 1px solid #2c2c2c; }
    .qbcb--compact .qbcb-choice-label { padding: 7px 12px; }

    .qbcb-cta-github:hover, .qbcb-cta-github:focus-visible { background: #A62D10; border-color: #A62D10; }
    .qbcb-chip { padding: 4px 6px; letter-spacing: 0.08em; white-space: nowrap; }
    .qbcb-bullet { position: relative; padding-left: 18px; margin-bottom: 6px; }
    .qbcb-bullet::before { content: ""; position: absolute; left: 0; top: 7px; width: 6px; height: 6px; background: #F26423; }
</style>

<script>
    (function () {
        var box = document.getElementById({{ \Illuminate\Support\Js::from($boxId) }});
        if (!box) { return; }

        var github = box.querySelector('[data-gh-cbox-github]');
        var mail = box.querySelector('[data-gh-cbox-mail]');
        var toggle = box.querySelector('[data-gh-cbox-toggle]');
        var top10 = document.getElementById({{ \Illuminate\Support\Js::from($top10Id) }});

        function checkedInput() {
            return box.querySelector('input[name="{{ $boxId }}-idea-type"]:checked');
        }

        // The GitHub href and mailto subject/body are precomputed server-side per idea type
        // (they depend only on the page title/URL, which are known at render time) and carried
        // on each radio input as data-* attributes. JS here only swaps between them — it never
        // rebuilds the shared-contract URL or mail format, so there is one source of truth.
        function syncGithub() {
            var input = checkedInput();
            if (github && input) {
                github.setAttribute('href', input.getAttribute('data-gh-cbox-issue-url'));
            }
        }

        box.addEventListener('change', function (e) {
            if (e.target && e.target.name === '{{ $boxId }}-idea-type') { syncGithub(); }
        });

        if (mail) {
            // The address is assembled only at click time, from parts that are never
            // written into the rendered HTML (D1: interim inbox, split to avoid scraping).
            mail.addEventListener('click', function () {
                var input = checkedInput();
                if (!input) { return; }
                var parts = ['documentation-committee', 'magentoassociation.org'];
                var subject = input.getAttribute('data-gh-cbox-mail-subject') || '';
                var body = input.getAttribute('data-gh-cbox-mail-body') || '';
                mail.setAttribute(
                    'href',
                    'mailto:' + parts.join('@') + '?subject=' + encodeURIComponent(subject) + '&body=' + encodeURIComponent(body)
                );
            });
        }

        if (toggle && top10) {
            var label = toggle.querySelector('[data-gh-cbox-toggle-label]');
            toggle.addEventListener('click', function () {
                var open = toggle.getAttribute('aria-expanded') === 'true';
                toggle.setAttribute('aria-expanded', open ? 'false' : 'true');
                top10.hidden = open;
                if (label) { label.textContent = open ? 'See the top 10' : 'Hide the top 10'; }
            });
        }
    })();
</script>
