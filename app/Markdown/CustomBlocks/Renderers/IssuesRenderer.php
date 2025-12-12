<?php

namespace App\Markdown\CustomBlocks\Renderers;

use App\Markdown\CustomBlocks\IssuesBlock;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;

/**
 * Renders IssuesBlock as an accordion of common issues
 *
 * Uses the .issues-accordion CSS class from _docs.css
 */
class IssuesRenderer implements NodeRendererInterface
{
    private int $issueCounter = 0;

    public function render(Node $node, ChildNodeRendererInterface $childRenderer): HtmlElement
    {
        IssuesBlock::assertInstanceOf($node);

        /** @var IssuesBlock $node */
        $content = $node->getContent();

        // Parse issues from content
        $issues = $this->parseIssues($content);

        $html = '';
        foreach ($issues as $issue) {
            $html .= $this->renderIssue($issue);
        }

        return new HtmlElement(
            'div',
            ['class' => 'issues-accordion'],
            $html
        );
    }

    private function parseIssues(string $content): array
    {
        $issues = [];
        $lines = explode("\n", $content);
        $currentIssue = null;
        $currentContent = [];
        $inSolution = false;
        $solutionItems = [];

        foreach ($lines as $line) {
            $trimmed = trim($line);

            // Match issue headers like "### Issue Title | SEVERITY"
            if (preg_match('/^###\s+(.+?)\s*\|\s*(CRITICAL|WARNING|INFO)$/i', $trimmed, $matches)) {
                // Save previous issue
                if ($currentIssue !== null) {
                    $issues[] = [
                        'title' => $currentIssue['title'],
                        'severity' => $currentIssue['severity'],
                        'description' => trim(implode("\n", $currentContent)),
                        'solution' => $solutionItems,
                    ];
                }

                $currentIssue = [
                    'title' => trim($matches[1]),
                    'severity' => strtolower(trim($matches[2])),
                ];
                $currentContent = [];
                $solutionItems = [];
                $inSolution = false;
                continue;
            }

            // Check for solution header
            if (preg_match('/^\*\*Solution:?\*\*$/i', $trimmed)) {
                $inSolution = true;
                continue;
            }

            // Handle solution list items
            if ($inSolution && preg_match('/^-\s+(.+)$/', $trimmed, $matches)) {
                $solutionItems[] = trim($matches[1]);
                continue;
            }

            // Add to description if not in solution
            if (!$inSolution && $currentIssue !== null && !empty($trimmed)) {
                $currentContent[] = $trimmed;
            }
        }

        // Save last issue
        if ($currentIssue !== null) {
            $issues[] = [
                'title' => $currentIssue['title'],
                'severity' => $currentIssue['severity'],
                'description' => trim(implode("\n", $currentContent)),
                'solution' => $solutionItems,
            ];
        }

        return $issues;
    }

    private function renderIssue(array $issue): string
    {
        $this->issueCounter++;
        $id = 'issue-' . $this->issueCounter;
        $severity = $issue['severity'];

        $html = '<div class="issue-panel ' . $severity . '">';

        // Header button (clickable to expand)
        $html .= '<button class="issue-header-btn" ';
        $html .= 'x-data="{ open: false }" ';
        $html .= '@click="open = !open; $refs.solution' . $this->issueCounter . '.classList.toggle(\'hidden\')" ';
        $html .= 'aria-expanded="false" aria-controls="' . $id . '">';

        // Issue text (title and description)
        $html .= '<div class="issue-text">';
        $html .= '<h3>' . e($issue['title']) . '</h3>';
        if (!empty($issue['description'])) {
            $html .= '<p>' . e($issue['description']) . '</p>';
        }
        $html .= '</div>';

        // Severity badge
        $html .= '<span class="severity-label ' . $severity . '">' . strtoupper($severity) . '</span>';

        // Expand chevron
        $html .= '<svg class="expand-btn" fill="none" stroke="currentColor" viewBox="0 0 24 24">';
        $html .= '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>';
        $html .= '</svg>';

        $html .= '</button>';

        // Solution panel (hidden by default)
        $html .= '<div class="solution-panel hidden" id="' . $id . '" x-ref="solution' . $this->issueCounter . '">';
        $html .= '<div class="solution-grid">';
        $html .= '<div class="solution-content">';
        $html .= '<h4>Solution</h4>';

        if (!empty($issue['solution'])) {
            $html .= '<ul>';
            foreach ($issue['solution'] as $item) {
                $html .= '<li>' . $this->parseInlineMarkdown($item) . '</li>';
            }
            $html .= '</ul>';
        }

        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '</div>';

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
