<?php

namespace App\Markdown;

use League\CommonMark\Extension\ExtensionInterface;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\Extension\TaskList\TaskListExtension;
use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\Strikethrough\StrikethroughExtension;

final class GithubFlavoredMarkdownExtension implements ExtensionInterface
{
    public function register(EnvironmentBuilderInterface $environment): void
    {
        // Disabled AutolinkExtension to prevent automatic URL linking
        // $environment->addExtension(new AutolinkExtension());
        $environment->addExtension(new StrikethroughExtension());
        $environment->addExtension(new TableExtension());
        $environment->addExtension(new TaskListExtension());
    }
}
