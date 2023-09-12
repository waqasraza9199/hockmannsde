<?php declare(strict_types=1);

namespace Nimbits\NimbitsArticleQuestionsNext\ArticleQuestions;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
/**
 * @method void              add(CustomEntity $entity)
 * @method void              set(string $key, CustomEntity $entity)
 * @method CustomEntity[]    getIterator()
 * @method CustomEntity[]    getElements()
 * @method CustomEntity|null get(string $key)
 * @method CustomEntity|null first()
 * @method CustomEntity|null last()
 */
class ArticleQuestionEntityCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return ArticleQuestionEntity::class;
    }
}