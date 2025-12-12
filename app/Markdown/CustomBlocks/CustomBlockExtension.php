<?php

namespace App\Markdown\CustomBlocks;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\ExtensionInterface;

/**
 * Custom Block Extension for Documentation Components
 *
 * Supports fenced blocks using ::: syntax:
 * - :::warning, :::tip, :::note, :::success, :::error (Callouts)
 * - :::steps (Step Timeline)
 * - :::checklist (Verification Checklist)
 * - :::featured (Featured Boxout)
 * - :::prerequisites (Prerequisites List)
 * - :::issues (Issues Accordion)
 */
class CustomBlockExtension implements ExtensionInterface
{
    public function register(EnvironmentBuilderInterface $environment): void
    {
        // Register block start parser
        $environment->addBlockStartParser(new CustomFencedBlockStartParser(), 70);

        // Register renderers for each block type
        $environment->addRenderer(CalloutBlock::class, new Renderers\CalloutRenderer());
        $environment->addRenderer(StepsBlock::class, new Renderers\StepsRenderer());
        $environment->addRenderer(ChecklistBlock::class, new Renderers\ChecklistRenderer());
        $environment->addRenderer(FeaturedBlock::class, new Renderers\FeaturedRenderer());
        $environment->addRenderer(PrerequisitesBlock::class, new Renderers\PrerequisitesRenderer());
        $environment->addRenderer(IssuesBlock::class, new Renderers\IssuesRenderer());
    }
}
