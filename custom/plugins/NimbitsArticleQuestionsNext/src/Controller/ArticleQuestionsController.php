<?php declare(strict_types=1);

namespace Nimbits\NimbitsArticleQuestionsNext\Controller;

use Nimbits\NimbitsArticleQuestionsNext\Setting\Service\SettingService;
use Shopware\Core\Content\Mail\Service\AbstractMailService;
use Shopware\Core\Content\Mail\Service\MailService;
use Shopware\Core\Content\MailTemplate\MailTemplateEntity;
use Shopware\Core\Framework\Adapter\Translation\Translator;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Storefront\Framework\Captcha\Annotation\Captcha;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"storefront"})
 */
class ArticleQuestionsController extends StorefrontController
{

    /**
     * @var SettingService
     */
    private $settingService;


    /**
     * @var MailService
     */
    private $mailService;

    /**
     * @var systemConfigService
     */
    private $systemConfigService;

    /**
     * @var EntityRepositoryInterface
     */
    private $productRepository;

    /**
     * @var EntityRepositoryInterface
     */
    private $articleQuestionsRepository;

    /**
     * @var \Twig\Environment $twig
     */
    private $twig;

    private $translation;

    /**
     * @var EntityRepositoryInterface
     */
    private $mailTemplateRepository;

    public function __construct(
        SettingService            $settingService,
        AbstractMailService       $mailService,
        SystemConfigService       $systemConfigService,
        EntityRepositoryInterface $productRepository,
        EntityRepositoryInterface $articleQuestionsRepository,
        \Twig\Environment         $twig,
        Translator                $translation,
        EntityRepositoryInterface $mailTemplateRepository
    )
    {
        $this->settingService = $settingService;
        $this->mailService = $mailService;
        $this->systemConfigService = $systemConfigService;
        $this->productRepository = $productRepository;
        $this->articleQuestionsRepository = $articleQuestionsRepository;
        $this->twig = $twig;
        $this->translation = $translation;
        $this->mailTemplateRepository = $mailTemplateRepository;
    }

    /**
     * @Route("/nimbits/articlequestions/request", name="frontend.action.nimbits.article-questions-request", options={"seo"="false"}, methods={"POST"}, defaults={"_captcha"=true})
     * @Captcha
     */
    public function request(Request $request, SalesChannelContext $salesChannelContext, Context $context): Response
    {
        $settings = $this->settingService->getSettingsAsArray($salesChannelContext->getSalesChannel()->getId());

        $questionData = [
            'product_id' => $request->get('nbaq_product_id'),
            'product_url' => $request->get('nbaq_product_url'),
            'salutation' => $request->get('nbaq_salutation'),
            'company' => $request->get('nbaq_company'),
            'firstname' => $request->get('nbaq_firstname'),
            'surname' => $request->get('nbaq_surname'),
            'mail' => $request->get('nbaq_mail'),
            'question' => $request->get('nbaq_question'),
            'answer' => "",
            'active' => 0,
            'languageid' => $request->get('nbaq_saleschannellanguageid')
        ];




        $this->insertQuestion($questionData);

        $this->sendRequestMail($salesChannelContext, $context, $settings, $questionData);


        $finishUrl = $this->generateUrl('frontend.detail.page', ['productId' => $request->get('nbaq_product_id'), 'nb_aq_success' => 1]);
        return new RedirectResponse($finishUrl);
    }

    private function insertQuestion(array $questionData)
    {

        $data = [
            'article_id' => $questionData["product_id"],
            'salutation' => $questionData["salutation"],
            'firstname' => $questionData["firstname"],
            'surname' => $questionData["surname"],
            'mail' => $questionData["mail"],
            'company' => $questionData["company"],
            'question' => $questionData["question"],
            'answer' => $questionData["answer"],
            'active' => false,
            'additional_info' => NULL,
            'language_id' => $questionData["languageid"]
        ];

        $retval = $this->articleQuestionsRepository->create(
            [
                $data,
            ],
            \Shopware\Core\Framework\Context::createDefaultContext()
        );
    }

    private function sendRequestMail(SalesChannelContext $salesChannelContext, Context $context, array $settings, array $questionData)
    {


        if (!isset($settings['nbArticlequestionsRequestmail'])
            || !filter_var($settings['nbArticlequestionsRequestmail'], FILTER_VALIDATE_EMAIL)) {


            //fallback

            $senderEmail = $this->systemConfigService->get('core.basicInformation.email', $salesChannelContext->getSalesChannel()->getId());

            $senderEmail = $senderEmail ?? $this->systemConfigService->get('core.mailerSettings.senderAddress');


            if ($senderEmail === null) {
                return;
            }
            $settings['nbArticlequestionsRequestmail'] = $senderEmail;
        }

        $criteria = new Criteria();
        $criteria->addFilter();


        $product = $this->productRepository->search(
            (new Criteria())
                ->addFilter(new EqualsFilter('product.id', $questionData["product_id"])),
            \Shopware\Core\Framework\Context::createDefaultContext()
        );
        $product = $product->getElements();


        $questionData["productname"] = $product[array_key_first($product)]->getName();
        $questionData["productordernumber"] = $product[array_key_first($product)]->getProductNumber();


        $data = [];
        $data['recipients'] = [$settings['nbArticlequestionsRequestmail'] => $settings['nbArticlequestionsRequestmail']];

        $customerMail =  $questionData["mail"];
        $sameEmailAsShop = $this->systemConfigService->get('NimbitsArticleQuestionsNext.config.senderSameAsShop');


        $mailtemplate = $this->getMailTemplate($salesChannelContext, "nimbits_aq_emailshopowner");

        $data['contentHtml'] = $mailtemplate->getContentHtml() ?? $mailtemplate->getTranslated()['contentHtml'];
        $data['contentPlain'] = $mailtemplate->getContentPlain() ?? $mailtemplate->getTranslated()['contentPlain'];
        $data['subject'] = $mailtemplate->getSubject() ?? $mailtemplate->getTranslated()['subject'];

        $data['salesChannelId'] = $salesChannelContext->getSalesChannel()->getId();
        $data['salesChannel'] = $salesChannelContext->getSalesChannel();
        $data['senderName'] = $this->translation->trans("nimbits-articlequestions.mail.sendername");
        if ($sameEmailAsShop === true) {
            $data["senderEmail"] = $customerMail;
        }



        $this->mailService->send(
            $data,
            $context,
            [
                'salesChannelId' => $salesChannelContext->getSalesChannel()->getId(),
                'questionData' => $questionData
            ]
        );


        //send customer mail

        $questionData["productname"] = $product[array_key_first($product)]->getName();
        $questionData["productordernumber"] = $product[array_key_first($product)]->getProductNumber();


        $data2 = [];
        $data2['recipients'] = [$questionData["mail"] => $questionData["mail"]];

        $mailtemplate = $this->getMailTemplate($salesChannelContext, "nimbits_aq_emailcustomer");

        $data2['contentHtml'] = $mailtemplate->getContentHtml() ?? $mailtemplate->getTranslated()['contentHtml'];
        $data2['contentPlain'] = $mailtemplate->getContentPlain() ?? $mailtemplate->getTranslated()['contentPlain'];
        $data2['subject'] = $mailtemplate->getSubject() ?? $mailtemplate->getTranslated()['subject'];

        $data2['salesChannelId'] = $salesChannelContext->getSalesChannel()->getId();
        $data2['salesChannel'] = $salesChannelContext->getSalesChannel();
        $data2['senderName'] = $this->translation->trans("nimbits-articlequestions.mail.sendernamecustomer");

        $this->mailService->send(
            $data2,
            $context,
            [
                'salesChannelId' => $salesChannelContext->getSalesChannel()->getId(),
                'questionData' => $questionData
            ]
        );
    }

    private function getMailTemplate(SalesChannelContext $salesChannelContext, string $technicalName)
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('mailTemplateType.technicalName', $technicalName));
        $criteria->setLimit(1);

        /** @var MailTemplateEntity|null $mailTemplate */
        $mailTemplate = $this->mailTemplateRepository->search($criteria, $salesChannelContext->getContext())->first();

        return $mailTemplate;
    }

}