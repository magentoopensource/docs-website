<?php

namespace App\Markdown\CustomBlocks\Renderers;

use App\Markdown\CustomBlocks\CalloutBlock;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;
use Illuminate\Support\Str;

/**
 * Renders CalloutBlock as styled HTML
 *
 * Uses the .callout CSS class from _docs.css
 */
class CalloutRenderer implements NodeRendererInterface
{
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): HtmlElement
    {
        CalloutBlock::assertInstanceOf($node);

        /** @var CalloutBlock $node */
        $type = $node->getType();
        $content = $node->getContent();

        // Parse the content to extract title and body
        $lines = explode("\n", trim($content));
        $title = '';
        $body = [];

        foreach ($lines as $line) {
            // Check for bold title line: **Title**
            if (preg_match('/^\*\*(.+?)\*\*$/', trim($line), $matches)) {
                if (empty($title)) {
                    $title = $matches[1];
                    continue;
                }
            }
            $body[] = $line;
        }

        $bodyContent = trim(implode("\n", $body));

        // Build the HTML structure
        $innerHtml = '';

        // Add icon based on type
        $icon = $this->getIconForType($type);
        if ($icon) {
            $innerHtml .= '<div class="callout-icon">' . $icon . '</div>';
        }

        $innerHtml .= '<div class="callout-content">';

        if ($title) {
            $innerHtml .= '<strong class="callout-title">' . e($title) . '</strong>';
        }

        // Convert markdown in body to HTML (basic conversion)
        $bodyHtml = $this->parseInlineMarkdown($bodyContent);
        $innerHtml .= '<p>' . $bodyHtml . '</p>';

        $innerHtml .= '</div>';

        return new HtmlElement(
            'div',
            ['class' => 'callout ' . $type],
            $innerHtml
        );
    }

    private function getIconForType(string $type): string
    {
        return match ($type) {
            'warning' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>',
            'tip' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>',
            'note' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            'success' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            'error' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            'featured' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>',
            default => '',
        };
    }

    private function parseInlineMarkdown(string $text): string
    {
        // Convert **bold** to <strong>
        $text = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $text);

        // Convert *italic* to <em>
        $text = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $text);

        // Convert `code` to <code>
        $text = preg_replace('/`(.+?)`/', '<code>$1</code>', $text);

        // Convert [text](url) to <a>
        $text = preg_replace('/\[(.+?)\]\((.+?)\)/', '<a href="$2">$1</a>', $text);

        // Convert newlines to <br>
        $text = nl2br($text);

        return $text;
    }
}
