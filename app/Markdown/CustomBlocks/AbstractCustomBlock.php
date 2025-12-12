<?php

namespace App\Markdown\CustomBlocks;

use League\CommonMark\Node\Block\AbstractBlock;

/**
 * Base class for all custom documentation blocks
 */
abstract class AbstractCustomBlock extends AbstractBlock
{
    protected string $type;
    protected string $content;

    public function __construct(string $type, string $content = '')
    {
        parent::__construct();
        $this->type = $type;
        $this->content = $content;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function appendContent(string $line): void
    {
        $this->content .= ($this->content ? "\n" : '') . $line;
    }
}
