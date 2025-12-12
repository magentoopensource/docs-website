<?php

namespace App\Markdown\CustomBlocks\Renderers;

use App\Markdown\CustomBlocks\FeaturedBlock;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;

/**
 * Renders FeaturedBlock as a featured boxout
 *
 * Uses the .featured-boxout CSS class from _docs.css
 */
class FeaturedRenderer implements NodeRendererInterface
{
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): HtmlElement
    {
        FeaturedBlock::assertInstanceOf($node);

        /** @var FeaturedBlock $node */
        $content = $node->getContent();

        $html = $this->parseContent($content);

        return new HtmlElement(
            'div',
            ['class' => 'featured-boxout'],
            $html
        );
    }

    private function parseContent(string $content): string
    {
        $html = '';
        $lines = explode("\n", $content);
        $inList = false;
        $listItems = [];

        foreach ($lines as $line) {
            $trimmed = trim($line);

            if (empty($trimmed)) {
                // Close list if open
                if ($inList && !empty($listItems)) {
                    $html .= '<ul>' . implode('', $listItems) . '</ul>';
                    $listItems = [];
                    $inList = false;
                }
                continue;
            }

            // Match heading ### Title
            if (preg_match('/^###\s+(.+)$/', $trimmed, $matches)) {
                $html .= '<h3>' . e($matches[1]) . '</h3>';
                continue;
            }

            // Match list items
            if (preg_match('/^-\s+(.+)$/', $trimmed, $matches)) {
                $inList = true;
                $listItems[] = '<li>' . $this->parseInlineMarkdown($matches[1]) . '</li>';
                continue;
            }

            // Close list if we hit non-list content
            if ($inList && !empty($listItems)) {
                $html .= '<ul>' . implode('', $listItems) . '</ul>';
                $listItems = [];
                $inList = false;
            }

            // Regular paragraph
            $html .= '<p>' . $this->parseInlineMarkdown($trimmed) . '</p>';
        }

        // Close any remaining list
        if ($inList && !empty($listItems)) {
            $html .= '<ul>' . implode('', $listItems) . '</ul>';
        }

        return $html;
    }

    private function parseInlineMarkdown(string $text): string
    {
        // Convert **bold** to <strong>
        $text = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $text);

        // Convert *italic* to <em>
        $text = preg_replace('/(?<!\*)\*(?!\*)(.+?)(?<!\*)\*(?!\*)/', '<em>$1</em>', $text);

        // Convert `code` to <code>
        $text = preg_replace('/`(.+?)`/', '<code>$1</code>', $text);

        // Convert [text](url) to <a>
        $text = preg_replace('/\[(.+?)\]\((.+?)\)/', '<a href="$2">$1</a>', $text);

        return $text;
    }
}
