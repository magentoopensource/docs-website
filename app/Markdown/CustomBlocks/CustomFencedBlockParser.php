<?php

namespace App\Markdown\CustomBlocks;

use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Parser\Block\AbstractBlockContinueParser;
use League\CommonMark\Parser\Block\BlockContinue;
use League\CommonMark\Parser\Block\BlockContinueParserInterface;
use League\CommonMark\Parser\Cursor;

/**
 * Continues parsing a custom fenced block until closing :::
 */
class CustomFencedBlockParser extends AbstractBlockContinueParser implements BlockContinueParserInterface
{
    private AbstractCustomBlock $block;
    private bool $finished = false;

    public function __construct(AbstractCustomBlock $block)
    {
        $this->block = $block;
    }

    public function getBlock(): AbstractBlock
    {
        return $this->block;
    }

    public function isContainer(): bool
    {
        return false;
    }

    public function canHaveLazyContinuationLines(): bool
    {
        return true;
    }

    public function tryContinue(Cursor $cursor, BlockContinueParserInterface $activeBlockParser): ?BlockContinue
    {
        if ($this->finished) {
            return BlockContinue::finished();
        }

        $line = $cursor->getRemainder();

        // Check for closing fence
        if (preg_match('/^:::\s*$/', $line)) {
            $this->finished = true;
            return BlockContinue::finished();
        }

        // Add line to content
        $this->block->appendContent($line);
        $cursor->advanceToEnd();

        return BlockContinue::at($cursor);
    }

    public function addLine(string $line): void
    {
        $this->block->appendContent($line);
    }
}
