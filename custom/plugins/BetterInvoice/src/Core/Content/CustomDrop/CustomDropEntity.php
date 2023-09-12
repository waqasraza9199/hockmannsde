<?php declare(strict_types=1);

namespace LZYT8\BetterInvoice\Core\Content\CustomDrop;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shopware\Core\Content\Rule\RuleEntity;

class CustomDropEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var bool
     */
    protected $enabled = false;

    /**
     * @var int
     */
    protected $priority = 0;

    /**
     * @var string
     */
    protected $bank;

    /**
     * @var string
     */
    protected $iban;

    /**
     * @var string
     */
    protected $bic;

    /**
     * @var RuleEntity
     */
    protected $rule;

    /**
     * @var string|null
     */
    protected $ruleId;

    /**
     * Getter method for the property.
     *
     * @return bool
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Setter method for the property.
     *
     * @param bool $enabled
     *
     * @return void
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * Getter method for the property.
     *
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Setter method for the property.
     *
     * @param int $priority
     *
     * @return void
     */
    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }

    /**
     * Getter method for the property.
     *
     * @return string
     */
    public function getBank()
    {
        return $this->bank;
    }

    /**
     * Setter method for the property.
     *
     * @param string $bank
     *
     * @return void
     */
    public function setBank(string $bank): void
    {
        $this->bank = $bank;
    }

    /**
     * Getter method for the property.
     *
     * @return string
     */
    public function getIban()
    {
        return $this->iban;
    }

    /**
     * Setter method for the property.
     *
     * @param string $iban
     *
     * @return void
     */
    public function setIban(string $iban): void
    {
        $this->iban = $iban;
    }

    /**
     * Getter method for the property.
     *
     * @return string
     */
    public function getBic()
    {
        return $this->bic;
    }

    /**
     * Setter method for the property.
     *
     * @param string $bic
     *
     * @return void
     */
    public function setBic(string $bic): void
    {
        $this->bic = $bic;
    }

    /**
     * Getter method for the property.
     *
     * @return RuleEntity
     */
    public function getRule()
    {
        return $this->rule;
    }

    /**
     * Setter method for the property.
     *
     * @param RuleEntity $rule
     *
     * @return void
     */
    public function setRule(RuleEntity $rule): void
    {
        $this->rule = $rule;
    }

    /**
     * Getter method for the property.
     *
     * @return string|null
     */
    public function getRuleId()
    {
        return $this->ruleId;
    }

    /**
     * Setter method for the property.
     *
     * @param string|null $ruleId
     *
     * @return void
     */
    public function setRuleId(?string $ruleId): void
    {
        $this->ruleId = $ruleId;
    }
}
