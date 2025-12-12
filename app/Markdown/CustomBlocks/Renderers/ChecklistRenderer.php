<?php

namespace App\Markdown\CustomBlocks\Renderers;

use App\Markdown\CustomBlocks\ChecklistBlock;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;

/**
 * Renders ChecklistBlock as a verification checklist
 *
 * Uses the .verification-checklist CSS class from _docs.css
 */
class ChecklistRenderer implements NodeRendererInterface
{
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): HtmlElement
    {
        ChecklistBlock::assertInstanceOf($node);

        /** @var ChecklistBlock $node */
        $content = $node->getContent();

        // Parse sections by bold headings
        $sections = $this->parseSections($content);

        $html = '';
        foreach ($sections as $section) {
            $html .= $this->renderSection($section);
        }

        return new HtmlElement(
            'ul',
            ['class' => 'verification-checklist'],
            $html
        );
    }

    private function parseSections(string $content): array
    {
        $sections = [];
        $lines = explode("\n", $content);
        $currentSection = null;
        $currentItems = [];

        foreach ($lines as $line) {
            $trimmed = trim($line);

            // Match section headers like "**Security Hardening** (REQUIRED)"
            if (preg_match('/^\*\*(.+?)\*\*\s*(\(.+?\))?$/', $trimmed, $matches)) {
                // Save previous section
                if ($currentSection !== null) {
                    $sections[] = [
                        'title' => $currentSection['title'],
                        'badge' => $currentSection['badge'],
                        'items' => $currentItems,
                    ];
                }

                $currentSection = [
                    'title' => trim($matches[1]),
                    'badge' => isset($matches[2]) ? trim($matches[2], '() ') : 'REQUIRED',
                ];
                $currentItems = [];
                continue;
            }

            // Match list items
            if (preg_match('/^-\s+(.+)$/', $trimmed, $matches)) {
                $currentItems[] = trim($matches[1]);
            }
        }

        // Save last section
        if ($currentSection !== null) {
            $sections[] = [
                'title' => $currentSection['title'],
                'badge' => $currentSection['badge'],
                'items' => $currentItems,
            ];
        }

        return $sections;
    }

    private function renderSection(array $section): string
    {
        $html = '<li class="verification-step">';

        // Section title with badge
        $html .= '<strong>' . e($section['title']) . '</strong>';

        // Items list
        if (!empty($section['items'])) {
            $html .= '<ul>';
            foreach ($section['items'] as $item) {
                $html .= '<li>' . $this->parseInlineMarkdown($item) . '</li>';
            }
            $html .= '</ul>';
        }

        $html .= '</li>';

        return $html;
    }

    private function parseInlineMarkdown(string $text): string
    {
        // Convert **bold** to <strong>
        $text = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $text);

        // Convert `code` to <code>
        $text = preg_replace('/`(.+?)`/', '<code>$1</code>', $text);

        // Convert [text](url) to <a>
        $text = preg_replace('/\[(.+?)\]\((.+?)\)/', '<a href="$2">$1</a>', $text);

        return $text;
    }
}
