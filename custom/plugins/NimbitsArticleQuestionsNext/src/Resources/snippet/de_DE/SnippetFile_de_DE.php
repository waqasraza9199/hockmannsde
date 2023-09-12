<?php declare(strict_types=1);

namespace Nimbits\NimbitsArticleQuestionsNext\Resources\snippet\de_DE;

use Shopware\Core\System\Snippet\Files\SnippetFileInterface;

class SnippetFile_de_DE implements SnippetFileInterface
{
    public function getName(): string
    {
        return 'trans.de-DE';
    }

    public function getPath(): string
    {
        return __DIR__ . '/trans.de-DE.json';
    }

    public function getIso(): string
    {
        return 'de-DE';
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