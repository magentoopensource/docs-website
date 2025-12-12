<?php

namespace App\Markdown\CustomBlocks;

use League\CommonMark\Parser\Block\BlockStart;
use League\CommonMark\Parser\Block\BlockStartParserInterface;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Parser\MarkdownParserStateInterface;

/**
 * Parses custom fenced blocks starting with :::blocktype
 */
class CustomFencedBlockStartParser implements BlockStartParserInterface
{
    public function tryStart(Cursor $cursor, MarkdownParserStateInterface $parserState): ?BlockStart
    {
        if ($cursor->isIndented()) {
            return BlockStart::none();
        }

        $match = $cursor->match('/^:::([a-z]+)\s*$/');

        if ($match === null) {
            return BlockStart::none();
        }

        // Extract the block type from the match
        preg_match('/^:::([a-z]+)/', $match, $matches);
        $type = $matches[1] ?? '';

        // Create the appropriate block based on type
        $block = $this->createBlockForType($type);

        if ($block === null) {
            return BlockStart::none();
        }

        return BlockStart::of(new CustomFencedBlockParser($block))
            ->at($cursor);
    }

    private function createBlockForType(string $type): ?AbstractCustomBlock
    {
        // Callout types
        if (CalloutBlock::isCalloutType($type)) {
            return new CalloutBlock($type);
        }

        // Other block types
        return match ($type) {
            'steps' => new StepsBlock($type),
            'checklist' => new ChecklistBlock($type),
            'featured' => new FeaturedBlock($type),
            'prerequisites' => new PrerequisitesBlock($type),
            'issues' => new IssuesBlock($type),
            default => null,
        };
    }
}
