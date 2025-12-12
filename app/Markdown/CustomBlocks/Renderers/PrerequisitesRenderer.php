<?php

namespace App\Markdown\CustomBlocks\Renderers;

use App\Markdown\CustomBlocks\PrerequisitesBlock;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;

/**
 * Renders PrerequisitesBlock as a prerequisites list
 *
 * Uses the .prerequisites-list CSS class from _docs.css
 */
class PrerequisitesRenderer implements NodeRendererInterface
{
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): HtmlElement
    {
        PrerequisitesBlock::assertInstanceOf($node);

        /** @var PrerequisitesBlock $node */
        $content = $node->getContent();

        $html = $this->parseContent($content);

        return new HtmlElement(
            'div',
            ['class' => 'prerequisites-list'],
            $html
        );
    }

    private function parseContent(string $content): string
    {
        $lines = explode("\n", $content);
        $items = [];

        foreach ($lines as $line) {
            $trimmed = trim($line);

            // Match list items like "- **Title** - Description"
            if (preg_match('/^-\s+(.+)$/', $trimmed, $matches)) {
                $items[] = '<li>' . $this->parseInlineMarkdown($matches[1]) . '</li>';
            }
        }

        if (empty($items)) {
            return '';
        }

        return '<ul>' . implode('', $items) . '</ul>';
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
