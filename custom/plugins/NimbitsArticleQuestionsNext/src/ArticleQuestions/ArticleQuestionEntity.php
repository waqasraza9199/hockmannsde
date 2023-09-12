<?php declare(strict_types=1);

namespace Nimbits\NimbitsArticleQuestionsNext\ArticleQuestions;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class ArticleQuestionEntity extends Entity
{
    use EntityIdTrait;
    /**
     * @var string
     */
    protected $salutation;
    protected $firstname;
    protected $surname;
    protected $mail;
    protected $company;
    protected $question;
	protected $answer;
	protected $active;
	protected $article_id;
    protected $additional_info;
    protected $language_id;

    public function getSalutation(): ?string
    {
        return $this->salutation;
    }

    public function setSalutation(string $salutation): void
    {
        $this->salutation = $salutation;
    }

	public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): void
    {
        $this->firstname = $firstname;
    }

	public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): void
    {
        $this->surname = $surname;
    }

	public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): void
    {
        $this->mail = $mail;
    }

	public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(string $company): void
    {
        $this->company = $company;
    }

	public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): void
    {
        $this->question = $question;
    }

	public function getAnswer(): ?string
    {
        return $this->answer;
    }

    public function setAnswer(string $answer): void
    {
        $this->answer = $answer;
    }

	public function getActive(): bool
    {
        if($this->active != 0){
			return true;
		}
		return false;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

	public function getAdditional_info(): ?string
    {
        return $this->additional_info;
    }

    public function setAdditional_info(string $additional_info): void
    {
        $this->additional_info = $additional_info;
    }

	public function getArticle_id()
    {
        return $this->article_id;
    }

    public function setArticle_id(string $article_id)
    {
        $this->article_id = $article_id;
    }

	public function getLanguage_id()
    {
        return $this->language_id;
    }

    public function setLanguage_id(string $language_id)
    {
        $this->language_id = $language_id;
    }




}