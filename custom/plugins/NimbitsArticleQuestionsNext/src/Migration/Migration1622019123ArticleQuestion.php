<?php declare(strict_types=1);

namespace Nimbits\NimbitsArticleQuestionsNext\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Uuid\Uuid;

class Migration1622019123ArticleQuestion extends MigrationStep
{
    //26.05.2021
    public function getCreationTimestamp(): int
    {
        return 1622019123;
    }

    public function update(Connection $connection): void
    {

        $mailShopownerTemplateTypeId = $this->createShopownerMailTemplateType($connection);
        $this->createShopownerMailTemplate($connection, $mailShopownerTemplateTypeId);

        $mailCustomerTemplateTypeId = $this->createCustomerMailTemplateType($connection);
        $this->createCustomerMailTemplate($connection, $mailCustomerTemplateTypeId);

        $mailCustomerAnswerTemplateTypeId = $this->createCustomerAnswerMailTemplateType($connection);
        $this->createCustomerAnswerMailTemplate($connection, $mailCustomerAnswerTemplateTypeId);

    }

    private function createShopownerMailTemplateType(Connection $connection): string
    {
        $mailTemplateTypeId = Uuid::randomBytes();

        $defaultLangId = $this->getLanguageIdByLocale($connection, 'en-GB');
        $deLangId = $this->getLanguageIdByLocale($connection, 'de-DE');

        $englishName = 'Articlequestions shopowner e-mail';
        $germanName = 'Fragen zum Artikel Shopbetreiber E-Mail';

        $existantType = $this->hasExistent($connection, "mail_template_type", "nimbits_aq_emailshopowner");

        if ($existantType === false) {
            $connection->insert('mail_template_type', [
                'id' => $mailTemplateTypeId,
                'technical_name' => 'nimbits_aq_emailshopowner',
                'available_entities' => json_encode(['product' => 'product', 'nimbits_articlequestions' => 'nimbits_articlequestions']),
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
            ]);
        } else {
            $mailTemplateTypeId = $existantType;
        }


        if ($defaultLangId !== $deLangId || $defaultLangId !== Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM)) {
            if ($this->hasExistent($connection, "mail_template_type_translation", $englishName) === false) {
                $connection->insert('mail_template_type_translation', [
                    'mail_template_type_id' => $mailTemplateTypeId,
                    'language_id' => $defaultLangId,
                    'name' => $englishName,
                    'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                ]);
            }
        }

        if ($deLangId) {
            if ($this->hasExistent($connection, "mail_template_type_translation", $germanName) === false) {
                $connection->insert('mail_template_type_translation', [
                    'mail_template_type_id' => $mailTemplateTypeId,
                    'language_id' => $deLangId,
                    'name' => $germanName,
                    'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                ]);
            }
        }

        return $mailTemplateTypeId;
    }

    private function getLanguageIdByLocale(Connection $connection, string $locale): ?string
    {
        $sql = <<<SQL
SELECT `language`.`id`
FROM `language`
INNER JOIN `locale` ON `locale`.`id` = `language`.`locale_id`
WHERE `locale`.`code` = :code
SQL;

        $languageId = $connection->fetchAssoc($sql, ['code' => $locale]);
        if (!$languageId && $locale !== 'en-GB') {
            return null;
        }

        if (!$languageId) {
            return Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM);
        }

        return $languageId['id'];
    }

    private function hasExistent(Connection $connection, string $type, string $fieldtocheck)
    {


        $sql = <<<SQL
SELECT `mail_template_type`.`id`
FROM `mail_template_type`
WHERE `mail_template_type`.`technical_name` = :fieldtocheck
SQL;

        if ($type == "mail_template_type_translation") {

            $sql = <<<SQL
SELECT `mail_template_type_translation`.`mail_template_type_id`
FROM `mail_template_type_translation`
WHERE `mail_template_type_translation`.`name` = :fieldtocheck
SQL;

        }

        if ($type == "mail_template") {

            $sql = <<<SQL
SELECT `mail_template`.`id`
FROM `mail_template`
WHERE `mail_template`.`id` = :fieldtocheck
SQL;

        }

        if ($type == "mail_template_translation") {

            $sql = <<<SQL
SELECT `mail_template_translation`.`mail_template_id`
FROM `mail_template_translation`
WHERE `mail_template_translation`.`subject` = :fieldtocheck
SQL;

        }

        $id = $connection->fetchAssoc($sql, ['fieldtocheck' => $fieldtocheck]);

        if (!$id) {
            return false;
        }

        return $id['mail_template_id'];

    }

    private function createShopownerMailTemplate(Connection $connection, string $mailTemplateTypeId): void
    {
        $mailTemplateId = Uuid::randomBytes();

        $defaultLangId = $this->getLanguageIdByLocale($connection, 'en-GB');
        $deLangId = $this->getLanguageIdByLocale($connection, 'de-DE');

        $existant = $this->hasExistent($connection, "mail_template", $mailTemplateId);

        if ($existant === false) {
            $connection->insert('mail_template', [
                'id' => $mailTemplateId,
                'mail_template_type_id' => $mailTemplateTypeId,
                'system_default' => 0,
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
            ]);
        } else {
            $mailTemplateId = $existant;
        }

        if ($defaultLangId !== $deLangId || $defaultLangId !== Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM)) {
            if ($this->hasExistent($connection, "mail_template_translation", 'Question regarding article') === false) {
                $connection->insert('mail_template_translation', [
                    'mail_template_id' => $mailTemplateId,
                    'language_id' => $defaultLangId,
                    'sender_name' => '{{ salesChannel.name }}',
                    'subject' => 'Question regarding article',
                    'description' => '',
                    'content_html' => $this->getContentHtmlEnShopowner(),
                    'content_plain' => $this->getContentPlainEnShopowner(),
                    'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                ]);
            }
        }

        if ($deLangId) {
            if ($this->hasExistent($connection, "mail_template_translation", 'Frage zum Artikel') === false) {
                $connection->insert('mail_template_translation', [
                    'mail_template_id' => $mailTemplateId,
                    'language_id' => $deLangId,
                    'sender_name' => '{{ salesChannel.name }}',
                    'subject' => 'Frage zum Artikel',
                    'description' => '',
                    'content_html' => $this->getContentHtmlDeShopowner(),
                    'content_plain' => $this->getContentPlainDeShopowner(),
                    'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                ]);
            }
        }
    }

    private function getContentHtmlEnShopowner(): string
    {
        return <<<MAIL
Article question from {{ questionData.salutation }} {{ questionData.firstname }} {{ questionData.surname }}:
<br>
<b>Name:</b> {{ questionData.salutation }} {{ questionData.firstname }} {{ questionData.surname }}<br>
<b>Company:</b> {{ questionData.company }}<br>
<b>E-Mail:</b> {{ questionData.mail }}<br><br>

<b>Customers comment:</b> {{ questionData.question }}<br><br>

<b>Product name:</b> <a href="{{ questionData.product_url }}">{{ questionData.productname }}</a><br>
<b>Ordernumber:</b> {{ questionData.productordernumber }}<br>
MAIL;
    }

    private function getContentPlainEnShopowner(): string
    {
        return <<<MAIL
Article question from {{ questionData.salutation }} {{ questionData.firstname }} {{ questionData.surname }}:

Name: {{ questionData.salutation }} {{ questionData.firstname }} {{ questionData.surname }}
Company: {{ questionData.company }}
E-Mail: {{ questionData.mail }}

Customers comment: {{ questionData.question }}

Product name: {{ questionData.productname }}
Ordernumber: {{ questionData.productordernumber }}
MAIL;
    }

    private function getContentHtmlDeShopowner(): string
    {
        return <<<MAIL
Frage zum Artikel von {{ questionData.salutation }} {{ questionData.firstname }} {{ questionData.surname }}:
<br>
<b>Name:</b> {{ questionData.salutation }} {{ questionData.firstname }} {{ questionData.surname }}<br>
<b>Firma:</b> {{ questionData.company }}<br>
<b>E-Mail:</b> {{ questionData.mail }}<br><br>

<b>Kunden Kommentar:</b> {{ questionData.question }}<br><br>

<b>Produkt:</b> <a href="{{ questionData.product_url }}">{{ questionData.productname }}</a><br>
<b>Artikelnummer:</b> {{ questionData.productordernumber }}<br>
MAIL;
    }

    private function getContentPlainDeShopowner(): string
    {
        return <<<MAIL
Frage zum Artikel von {{ questionData.salutation }} {{ questionData.firstname }} {{ questionData.surname }}:

Name: {{ questionData.salutation }} {{ questionData.firstname }} {{ questionData.surname }}
Firma: {{ questionData.company }}
E-Mail: {{ questionData.mail }}

Kunden Kommentar: {{ questionData.question }}

Produkt: {{ questionData.productname }}
Artikelnummer: {{ questionData.productordernumber }}
MAIL;
    }

    private function createCustomerMailTemplateType(Connection $connection): string
    {
        $mailTemplateTypeId = Uuid::randomBytes();

        $defaultLangId = $this->getLanguageIdByLocale($connection, 'en-GB');
        $deLangId = $this->getLanguageIdByLocale($connection, 'de-DE');

        $englishName = 'Articlequestions customer e-mail';
        $germanName = 'Fragen zum Artikel Kunden E-Mail';

        $existant = $this->hasExistent($connection, "mail_template_type", "nimbits_aq_emailcustomer");

        if ($existant === false) {
            $connection->insert('mail_template_type', [
                'id' => $mailTemplateTypeId,
                'technical_name' => 'nimbits_aq_emailcustomer',
                'available_entities' => json_encode(['product' => 'product', 'nimbits_articlequestions' => 'nimbits_articlequestions']),
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
            ]);
        } else {
            $mailTemplateTypeId = $existant;
        }

        if ($defaultLangId !== $deLangId || $defaultLangId !== Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM)) {
            if ($this->hasExistent($connection, "mail_template_type_translation", $englishName) === false) {
                $connection->insert('mail_template_type_translation', [
                    'mail_template_type_id' => $mailTemplateTypeId,
                    'language_id' => $defaultLangId,
                    'name' => $englishName,
                    'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                ]);
            }
        }


        if ($deLangId) {
            if ($this->hasExistent($connection, "mail_template_type_translation", $germanName) === false) {
                $connection->insert('mail_template_type_translation', [
                    'mail_template_type_id' => $mailTemplateTypeId,
                    'language_id' => $deLangId,
                    'name' => $germanName,
                    'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                ]);
            }
        }

        return $mailTemplateTypeId;
    }

    private function createCustomerMailTemplate(Connection $connection, string $mailTemplateTypeId): void
    {
        $mailTemplateId = Uuid::randomBytes();

        $defaultLangId = $this->getLanguageIdByLocale($connection, 'en-GB');
        $deLangId = $this->getLanguageIdByLocale($connection, 'de-DE');


        $existant = $this->hasExistent($connection, "mail_template", $mailTemplateId);

        if ($existant === false) {
            $connection->insert('mail_template', [
                'id' => $mailTemplateId,
                'mail_template_type_id' => $mailTemplateTypeId,
                'system_default' => 0,
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
            ]);
        } else {
            $mailTemplateId = $existant;
        }

        if ($defaultLangId !== $deLangId || $defaultLangId !== Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM)) {
            if ($this->hasExistent($connection, "mail_template_translation", 'Your question regarding an article') === false) {
                $connection->insert('mail_template_translation', [
                    'mail_template_id' => $mailTemplateId,
                    'language_id' => $defaultLangId,
                    'sender_name' => '{{ salesChannel.name }}',
                    'subject' => 'Your question regarding an article',
                    'description' => '',
                    'content_html' => $this->getContentHtmlEnCustomer(),
                    'content_plain' => $this->getContentPlainEnCustomer(),
                    'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                ]);
            }
        }


        if ($deLangId) {
            if ($this->hasExistent($connection, "mail_template_translation", 'Ihre Frage zum Artikel') === false) {
                $connection->insert('mail_template_translation', [
                    'mail_template_id' => $mailTemplateId,
                    'language_id' => $deLangId,
                    'sender_name' => '{{ salesChannel.name }}',
                    'subject' => 'Ihre Frage zum Artikel',
                    'description' => '',
                    'content_html' => $this->getContentHtmlDeCustomer(),
                    'content_plain' => $this->getContentPlainDeCustomer(),
                    'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                ]);
            }
        }
    }

    private function getContentHtmlEnCustomer(): string
    {
        return <<<MAIL
Dear {{ questionData.salutation }} <b>{{ questionData.firstname }} {{ questionData.surname }}</b>, your question :<br><br>

<br>
<b>Name:</b> {{ questionData.salutation }} {{ questionData.firstname }} {{ questionData.surname }}<br>
<b>Company:</b> {{ questionData.company }}<br>
<b>E-Mail:</b> {{ questionData.mail }}<br><br>

<b>Your Question:</b> {{ questionData.question }}<br><br>

<b>Product name:</b> <a href="{{ questionData.product_url }}">{{ questionData.productname }}</a><br>
<b>Productnumber:</b> {{ questionData.productordernumber }}<br>
MAIL;
    }

//////////

    private function getContentPlainEnCustomer(): string
    {
        return <<<MAIL
Dear {{ questionData.salutation }} {{ questionData.firstname }} {{ questionData.surname }}, your question:

Name: {{ questionData.salutation }} {{ questionData.firstname }} {{ questionData.surname }}
Company: {{ questionData.company }}
E-Mail: {{ questionData.mail }}

Your Question: {{ questionData.question }}

Product name: {{ questionData.productname }}
Productnumber: {{ questionData.productordernumber }}
MAIL;
    }

    private function getContentHtmlDeCustomer(): string
    {
        return <<<MAIL
Hallo {{ questionData.salutation }} {{ questionData.firstname }} {{ questionData.surname }}, Ihre Frage:
<br>
<b>Name:</b> {{ questionData.salutation }} {{ questionData.firstname }} {{ questionData.surname }}<br>
<b>Firma:</b> {{ questionData.company }}<br>
<b>E-Mail:</b> {{ questionData.mail }}<br><br>

<b>Kunden Kommentar:</b> {{ questionData.question }}<br><br>

<b>Produkt:</b> <a href="{{ questionData.product_url }}">{{ questionData.productname }}</a><br>
<b>Artikelnummer:</b> {{ questionData.productordernumber }}<br>
MAIL;
    }

    private function getContentPlainDeCustomer(): string
    {
        return <<<MAIL
Hallo {{ questionData.salutation }} {{ questionData.firstname }} {{ questionData.surname }}, Ihre Frage:

Name: {{ questionData.salutation }} {{ questionData.firstname }} {{ questionData.surname }}
Firma: {{ questionData.company }}
E-Mail: {{ questionData.mail }}

Kunden Kommentar: {{ questionData.question }}

Produkt: {{ questionData.productname }}
Artikelnummer: {{ questionData.productordernumber }}
MAIL;
    }

    private function createCustomerAnswerMailTemplateType(Connection $connection): string
    {
        $mailTemplateTypeId = Uuid::randomBytes();

        $defaultLangId = $this->getLanguageIdByLocale($connection, 'en-GB');
        $deLangId = $this->getLanguageIdByLocale($connection, 'de-DE');

        $englishName = 'Articlequestions customer answer e-mail';
        $germanName = 'Fragen zum Artikel Kunden Antwort E-Mail';

        $existant = $this->hasExistent($connection, "mail_template_type", "nimbits_aq_emailcustomeranswer");

        if ($existant === false) {
            $connection->insert('mail_template_type', [
                'id' => $mailTemplateTypeId,
                'technical_name' => 'nimbits_aq_emailcustomeranswer',
                'available_entities' => json_encode(['product' => 'product', 'nimbits_articlequestions' => 'nimbits_articlequestions']),
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
            ]);
        } else {
            $mailTemplateTypeId = $existant;
        }

        if ($defaultLangId !== $deLangId || $defaultLangId !== Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM)) {
            if ($this->hasExistent($connection, "mail_template_type_translation", $englishName) === false) {
                $connection->insert('mail_template_type_translation', [
                    'mail_template_type_id' => $mailTemplateTypeId,
                    'language_id' => $defaultLangId,
                    'name' => $englishName,
                    'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                ]);
            }
        }

        if ($deLangId) {
            if ($this->hasExistent($connection, "mail_template_type_translation", $germanName) === false) {
                $connection->insert('mail_template_type_translation', [
                    'mail_template_type_id' => $mailTemplateTypeId,
                    'language_id' => $deLangId,
                    'name' => $germanName,
                    'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                ]);
            }
        }

        return $mailTemplateTypeId;
    }

    private function createCustomerAnswerMailTemplate(Connection $connection, string $mailTemplateTypeId): void
    {
        $mailTemplateId = Uuid::randomBytes();

        $defaultLangId = $this->getLanguageIdByLocale($connection, 'en-GB');
        $deLangId = $this->getLanguageIdByLocale($connection, 'de-DE');

        $existant = $this->hasExistent($connection, "mail_template", $mailTemplateId);

        if ($existant === false) {
            $connection->insert('mail_template', [
                'id' => $mailTemplateId,
                'mail_template_type_id' => $mailTemplateTypeId,
                'system_default' => 0,
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
            ]);
        } else {
            $mailTemplateId = $existant;
        }

        if ($defaultLangId !== $deLangId || $defaultLangId !== Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM)) {
            if ($this->hasExistent($connection, "mail_template_translation", 'Question regarding an article has been answered') === false) {
                $connection->insert('mail_template_translation', [
                    'mail_template_id' => $mailTemplateId,
                    'language_id' => $defaultLangId,
                    'sender_name' => '{{ salesChannel.name }}',
                    'subject' => 'Question regarding an article has been answered',
                    'description' => '',
                    'content_html' => $this->getContentHtmlEnCustomerAnswer(),
                    'content_plain' => $this->getContentPlainEnCustomerAnswer(),
                    'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                ]);
            }
        }

        if ($deLangId) {
            if ($this->hasExistent($connection, "mail_template_translation", 'Ihre Frage zum Artikel wurde beantwortet') === false) {
                $connection->insert('mail_template_translation', [
                    'mail_template_id' => $mailTemplateId,
                    'language_id' => $deLangId,
                    'sender_name' => '{{ salesChannel.name }}',
                    'subject' => 'Ihre Frage zum Artikel wurde beantwortet',
                    'description' => '',
                    'content_html' => $this->getContentHtmlDeCustomerAnswer(),
                    'content_plain' => $this->getContentPlainDeCustomerAnswer(),
                    'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                ]);
            }
        }
    }


//////

    private function getContentHtmlEnCustomerAnswer(): string
    {
        return <<<MAIL
Dear {{ data.salutation }} <b>{{ data.firstname }} {{ data.surname }}</b> . Your question has been answered:<br><br>

<b>Your question:</b> {{ data.question }}<br><br>

<b>Answer of the shopowner:</b> {{ data.answer }}<br><br>

Regarding the product:<br><br>

<b>Article:</b> {{ data.productname }}<br>
<b>Ordernumber:</b> {{ data.productordernumber }}<br><br>

Other informations:<br><br>

<b>Name:</b> {{ data.salutation }} {{ data.firstname }} {{ data.surname }}<br>
<b>E-Mail:</b> {{ data.mail }}<br><br>

With kind regards
MAIL;
    }

    private function getContentPlainEnCustomerAnswer(): string
    {
        return <<<MAIL
Dear {{ data.salutation }} {{ data.firstname }} {{ data.surname }}. Your question has been answered:

Your question: {{ data.question }}

Answer of the shopowner: {{ data.answer }}

Regarding the product:

Article: {{ data.productname }}
Ordernumber: {{ data.productordernumber }}

Other informations:

Name: {{ data.salutation }} {{ data.firstname }} {{ data.surname }}
E-Mail: {{ data.mail }}

With kind regards
MAIL;
    }

    private function getContentHtmlDeCustomerAnswer(): string
    {
        return <<<MAIL
Hallo {{ data.salutation }} <b>{{ data.firstname }} {{ data.surname }}</b> . Ihre Frage zu einem Artikel wurde beantwortet:<br><br>

<b>Ihre Frage:</b> {{ data.question }}<br><br>

<b>Antwort des Shopbetreibers:</b> {{ data.answer }}<br><br>

Bezüglich des Produktes:<br><br>

<b>Produkt:</b> {{ data.productname }}<br>
<b>Artikelnummer:</b> {{ data.productordernumber }}<br><br>

Weitere Informationen:<br><br>

<b>Name:</b> {{ data.salutation }} {{ data.firstname }} {{ data.surname }}<br>
<b>E-Mail:</b> {{ data.mail }}<br><br>

Mit freundlichen Grüßen
MAIL;
    }

    private function getContentPlainDeCustomerAnswer(): string
    {
        return <<<MAIL
Hallo {{ data.salutation }} {{ data.firstname }} {{ data.surname }} . Ihre Frage zu einem Artikel wurde beantwortet:

Ihre Frage: {{ data.question }}

Antwort des Shopbetreibers: {{ data.answer }}

Bezüglich des Produktes:

Produkt: {{ data.productname }}
Artikelnummer: {{ data.productordernumber }}

Weitere Informationen:

Name: {{ data.salutation }} {{ data.firstname }} {{ data.surname }}
E-Mail: {{ data.mail }}

Mit freundlichen Grüßen
MAIL;
    }

    public function updateDestructive(Connection $connection): void
    {

    }
}
