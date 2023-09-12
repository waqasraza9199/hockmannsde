<?php declare(strict_types=1);

namespace LZYT8\BetterInvoice\Core\Content\CustomDrop;

use Shopware\Core\Content\Rule\RuleDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CreatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\UpdatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class CustomDropDefinition extends EntityDefinition
{
    /**
     * {@inheritDoc}
     */
    public function getEntityName(): string
    {
        return 'lzyt_custom_drop';
    }

    /**
     * {@inheritDoc}
     */
    public function getCollectionClass(): string
    {
        return CustomDropCollection::class;
    }

    /**
     * {@inheritDoc}
     */
    public function getEntityClass(): string
    {
        return CustomDropEntity::class;
    }

    /**
     * {@inheritDoc}
     */
    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            (new BoolField('enabled', 'enabled'))->addFlags(new Required()),
            (new IntField('priority', 'priority'))->addFlags(new Required()),
            (new StringField('bank', 'bank'))->addFlags(new Required()),
            (new StringField('iban', 'iban'))->addFlags(new Required()),
            (new StringField('bic', 'bic'))->addFlags(new Required()),

            (new FkField('rule_id', 'ruleId', RuleDefinition::class))->addFlags(new Required()),
            (new ManyToOneAssociationField('rule', 'rule_id', RuleDefinition::class, 'id', false)),

            new CreatedAtField(),
            new UpdatedAtField(),
        ]);
    }
}
