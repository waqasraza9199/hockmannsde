<?php declare(strict_types=1);

namespace Nimbits\NimbitsArticleQuestionsNext\ArticleQuestions;

use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ReferenceVersionField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;

use Shopware\Core\Framework\DataAbstractionLayer\Field\LongTextField;

use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;

use Shopware\Core\Framework\DataAbstractionLayer\Field\VersionField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;

use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\System\Language\LanguageDefinition;

class ArticleQuestionEntityDefinition extends EntityDefinition
{
    public function getEntityName(): string
    {
        return 'nimbits_articlequestions';
    }

    public function getCollectionClass(): string
    {
        return ArticleQuestionEntityCollection::class;
    }

    public function getEntityClass(): string
    {
        return ArticleQuestionEntity::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            (new VersionField())->addFlags(new ApiAware()),
            new StringField('salutation', 'salutation'),
            new StringField('firstname', 'firstname'),
            new StringField('surname', 'surname'),
            new StringField('mail', 'mail'),
            new StringField('company', 'company'),
            new LongTextField('question', 'question'),
            new LongTextField('answer', 'answer'),
            new BoolField('active', 'active'),
            new StringField('additional_info', 'additional_info'),
			new StringField('created_at', 'created_at'),

			(new FkField('language_id', 'language_id', LanguageDefinition::class)),
			 new ManyToOneAssociationField('language', 'language_id', LanguageDefinition::class, 'id', false),

			(new FkField('article_id', 'article_id', ProductDefinition::class))->addFlags(new Required()),
            (new ReferenceVersionField(ProductDefinition::class, 'product_version_id'))->addFlags(new Required()),
            new ManyToOneAssociationField('product', 'article_id', ProductDefinition::class, 'id', false)


        ]);
    }
}