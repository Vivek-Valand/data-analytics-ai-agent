<?php

namespace App\Services;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\MarkdownConverter;

class MarkdownRenderer
{
    public function toHtml(string $markdown): string
    {
        $environment = new Environment;
        $environment->addExtension(new CommonMarkCoreExtension);
        $environment->addExtension(new TableExtension);

        $converter = new MarkdownConverter($environment);

        return $converter->convert($markdown)->getContent();
    }
}
