<?php declare(strict_types=1);

namespace Nimbits\NimbitsArticleQuestionsNext\Resources\snippet\en_GB;

use Shopware\Core\System\Snippet\Files\SnippetFileInterface;

class SnippetFile_en_GB implements SnippetFileInterface
{
    public function getName(): string
    {
        return 'trans.en-GB';
    }

    public function getPath(): string
    {
        return __DIR__ . '/trans.en-GB.json';
    }

    public function getIso(): string
    {
        return 'en-GB';
    }

    public function getAuthor(): string
    {
        return 'Nimbits';
    }

    public function isBase(): bool
    {
        return true;
    }
}