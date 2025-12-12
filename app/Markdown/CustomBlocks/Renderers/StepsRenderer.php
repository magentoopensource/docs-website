<?php

namespace App\Markdown\CustomBlocks\Renderers;

use App\Markdown\CustomBlocks\StepsBlock;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;

/**
 * Renders StepsBlock as a step-by-step timeline
 *
 * Uses the .step-timeline CSS class from _docs.css
 */
class StepsRenderer implements NodeRendererInterface
{
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): HtmlElement
    {
        StepsBlock::assertInstanceOf($node);

        /** @var StepsBlock $node */
        $content = $node->getContent();

        // Split content by step headers (## Step N: Title)
        $steps = $this->parseSteps($content);

        $html = '';
        foreach ($steps as $index => $step) {
            $stepNumber = $index + 1;
            $html .= $this->renderStep($stepNumber, $step['title'], $step['content']);
        }

        return new HtmlElement(
            'div',
            ['class' => 'step-timeline'],
            $html
        );
    }

    private function parseSteps(string $content): array
    {
        $steps = [];
        $lines = explode("\n", $content);
        $currentStep = null;
        $currentContent = [];

        foreach ($lines as $line) {
            // Match step headers like "## Step 1: Configure Payment Gateway"
            if (preg_match('/^##\s*Step\s*\d+[:\.]?\s*(.+)$/i', $line, $matches)) {
                // Save previous step
                if ($currentStep !== null) {
                    $steps[] = [
                        'title' => $currentStep,
                        'content' => trim(implode("\n", $currentContent)),
                    ];
                }
                $currentStep = trim($matches[1]);
                $currentContent = [];
            } else {
                $currentContent[] = $line;
            }
        }

        // Save last step
        if ($currentStep !== null) {
            $steps[] = [
                'title' => $currentStep,
                'content' => trim(implode("\n", $currentContent)),
            ];
        }

        return $steps;
    }

    private function renderStep(int $number, string $title, string $content): string
    {
        $html = '<div class="step-item">';

        // Step header with number badge
        $html .= '<div class="step-header">';
        $html .= '<div class="step-number-box">' . $number . '</div>';
        $html .= '<h3>' . e($title) . '</h3>';
        $html .= '</div>';

        // Step content
        $html .= '<div class="step-content">';
        $html .= $this->parseContent($content);
        $html .= '</div>';

        $html .= '</div>';

        return $html;
    }

    private function parseContent(string $content): string
    {
        $html = '';
        $lines = explode("\n", $content);
        $inList = false;
        $inVerifyBox = false;
        $listItems = [];
        $verifyItems = [];

        foreach ($lines as $line) {
            $trimmed = trim($line);

            // Check for verify section header
            if (preg_match('/^###\s*Verify/i', $trimmed)) {
                // Close any open list
                if ($inList && !empty($listItems)) {
                    $html .= '<ul class="icon-list">' . implode('', $listItems) . '</ul>';
                    $listItems = [];
                    $inList = false;
                }
                $inVerifyBox = true;
                continue;
            }

            // Handle task list items in verify box
            if ($inVerifyBox && preg_match('/^-\s*\[[\sx]\]\s*(.+)$/', $trimmed, $matches)) {
                $verifyItems[] = '<li>' . $this->parseInlineMarkdown($matches[1]) . '</li>';
                continue;
            }

            // Handle regular list items
            if (preg_match('/^-\s+(.+)$/', $trimmed, $matches)) {
                if (!$inList) {
                    $inList = true;
                }
                $listItems[] = '<li><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>' . $this->parseInlineMarkdown($matches[1]) . '</li>';
                continue;
            }

            // Close list if we hit a non-list line
            if ($inList && !empty($listItems) && !empty($trimmed) && !preg_match('/^-/', $trimmed)) {
                $html .= '<ul class="icon-list">' . implode('', $listItems) . '</ul>';
                $listItems = [];
                $inList = false;
            }

            // Handle subheadings (###)
            if (preg_match('/^###\s+(.+)$/', $trimmed, $matches)) {
                $html .= '<h4>' . e($matches[1]) . '</h4>';
                continue;
            }

            // Handle code blocks
            if (preg_match('/^```(\w*)$/', $trimmed, $matches)) {
                // Start of code block - collect until closing
                continue;
            }

            // Handle inline code in paragraphs
            if (!empty($trimmed) && !$inList && !$inVerifyBox) {
                $html .= '<p>' . $this->parseInlineMarkdown($trimmed) . '</p>';
            }
        }

        // Close any remaining list
        if ($inList && !empty($listItems)) {
            $html .= '<ul class="icon-list">' . implode('', $listItems) . '</ul>';
        }

        // Add verify box if we have verify items
        if (!empty($verifyItems)) {
            $html .= '<div class="verify-box">';
            $html .= '<div class="verify-header">Verify It Works</div>';
            $html .= '<ul>' . implode('', $verifyItems) . '</ul>';
            $html .= '</div>';
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
