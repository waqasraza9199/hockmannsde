<?php declare(strict_types=1);

namespace Nimbits\NimbitsArticleQuestionsNext\Controller;

use Nimbits\NimbitsArticleQuestionsNext\Setting\Service\SettingService;
use Shopware\Administration\Snippet\SnippetFinderInterface;
use Shopware\Core\Checkout\Cart\Price\Struct\CartPrice;
use Shopware\Core\Content\Mail\Service\AbstractMailService;
use Shopware\Core\Content\MailTemplate\MailTemplateEntity;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Adapter\Translation\Translator;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\System\SalesChannel\Context\AbstractSalesChannelContextFactory;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @RouteScope(scopes={"api"})
 */
class ApiController extends AbstractController
{
    private $settingsService;

    private $mailService;

    private $salesChannelRepository;

    private $twig;

    private $snippetFinder;

    private $recaptchaverifyurl = "https://www.google.com/recaptcha/api/siteverify";

    private $translation;

    private $mailTemplateRepository;

    /**
     * @var systemConfigService
     */
    private $systemConfigService;

    private AbstractSalesChannelContextFactory $salesChannelContextFactory;

    public function __construct(
        SettingService                   $settingsService,
        EntityRepositoryInterface        $articleQuestionsRepository,
        AbstractMailService              $mailService,
        EntityRepositoryInterface        $salesChannelRepository,
        \Twig\Environment                $twig,
        EntityRepositoryInterface        $productRepository,
        SnippetFinderInterface           $snippetFinder,
        SystemConfigService              $systemConfigService,
        Translator                       $translation,
        EntityRepositoryInterface        $mailTemplateRepository,
        AbstractSalesChannelContextFactory $salesChannelContextFactory
    )
    {
        $this->settingsService = $settingsService;
        $this->articleQuestionsRepository = $articleQuestionsRepository;
        $this->mailService = $mailService;
        $this->salesChannelRepository = $salesChannelRepository;
        $this->twig = $twig;
        $this->productRepository = $productRepository;
        $this->snippetFinder = $snippetFinder;
        $this->systemConfigService = $systemConfigService;
        $this->translation = $translation;
        $this->mailTemplateRepository = $mailTemplateRepository;
        $this->salesChannelContextFactory = $salesChannelContextFactory;

    }

    /**
     * @Route("/api/nimbits/articlequestions/sendcustomermail", name="api.action.nimbits.articlequestions.sendcustomermail", methods={"POST"})
     */
    public function sendcustomermail(Request $request, Context $context): JsonResponse
    {
        if (!$request->get('isArticleQuestion')) {
            return new JsonResponse(['Error, not a article question request']);
        }

        $data = [];
        $data["questionid"] = $request->get('id');
        $data["subject"] = $this->translation->trans("nimbits-articlequestions.mail.subjectcustomeranswer");
        $data["salesChannelId"] = $request->get('salesChannelId');
        $data["senderName"] = $this->translation->trans("nimbits-articlequestions.mail.sendernamecustomeranswer");


        $languageId = $this->getLanguageId($data["questionid"]);
        $defcontext = $this->createContext($languageId);


        $salesChannel = $this->salesChannelRepository->search(
            (new Criteria())
                ->addFilter(new EqualsFilter('id', $data["salesChannelId"])),
            $defcontext
        );
        $salesChannel = $salesChannel->getElements();
        $salesChannel = $salesChannel[array_key_first($salesChannel)];

        $this->sendMail($salesChannel,
            $defcontext,
            $this->settingsService->getSettingsAsArray($data["salesChannelId"]),
            $data
        );

        return new JsonResponse(['Success']);
    }

    private function getLanguageId($questionid)
    {
        $aq = $this->articleQuestionsRepository->search(
            (new Criteria())
                ->addFilter(new EqualsFilter('id', $questionid)),
            \Shopware\Core\Framework\Context::createDefaultContext()
        );
        $aq = $aq->first();
        //$questionid = array_key_first($aq);

        $langid = $aq->language_id;

        if (empty($langid)) {
            //fallback no language
            return false;
        }

        return $langid;
    }

    private function createContext($langid)
    {
        if ($langid === false) {
            return \Shopware\Core\Framework\Context::createDefaultContext();
        }

        $src = \Shopware\Core\Framework\Context::createDefaultContext()->getSource();

        $cntxt = new \Shopware\Core\Framework\Context(
            $src,
            [],
            Defaults::CURRENCY,
            [$langid, Defaults::LANGUAGE_SYSTEM],
            Defaults::LIVE_VERSION,
            1.0,
            false,
            CartPrice::TAX_STATE_GROSS,
            null
        );

        return $cntxt;

    }

    private function sendMail(SalesChannelEntity $salesChannel, Context $context, array $settings, array $requestdata)
    {
        $twig = $this->twig;

        //load aq repository
        $aq = $this->articleQuestionsRepository->search(
            (new Criteria())
                ->addFilter(new EqualsFilter('id', $requestdata["questionid"])),
            $context
        );
        $aq = $aq->getElements();


        if (!isset($settings['nbArticlequestionsRequestmail'])
            || !filter_var($settings['nbArticlequestionsRequestmail'], FILTER_VALIDATE_EMAIL)) {
            //fallback

            $senderEmail = $this->systemConfigService->get('core.basicInformation.email', $requestdata["salesChannelId"]);

            $senderEmail = $senderEmail ?? $this->systemConfigService->get('core.mailerSettings.senderAddress');

            if ($senderEmail === null) {
                return;
            }
            $settings['nbArticlequestionsRequestmail'] = $senderEmail;
        }


        $product = $this->productRepository->search(
            (new Criteria())
                ->addFilter(new EqualsFilter('product.id', $aq[array_key_first($aq)]->getArticle_id())),
            $context
        );
        $product = $product->getElements();

        $templateData = [];
        $templateData["productname"] = $product[array_key_first($product)]->getName();
        $templateData["productordernumber"] = $product[array_key_first($product)]->getProductNumber();

        $templateData["salutation"] = $aq[array_key_first($aq)]->getSalutation();
        $templateData["firstname"] = $aq[array_key_first($aq)]->getFirstname();
        $templateData["surname"] = $aq[array_key_first($aq)]->getSurname();
        $templateData["mail"] = $aq[array_key_first($aq)]->getMail();
        $templateData["question"] = $aq[array_key_first($aq)]->getQuestion();
        $templateData["answer"] = $aq[array_key_first($aq)]->getAnswer();
        $templateData["active"] = $aq[array_key_first($aq)]->getActive();
        $templateData["additionalinfo"] = $aq[array_key_first($aq)]->getAdditional_info();


        $data = [];

        $recipients = [];
        $recipients[$aq[array_key_first($aq)]->getMail()] = $aq[array_key_first($aq)]->getMail();

        $data['recipients'] = $recipients;

        //$salesChannelContext = $this->salesChannelContextFactory->create(Uuid::randomHex(), Defaults::SALES_CHANNEL);


        $mailtemplate = $this->getMailTemplate($context, "nimbits_aq_emailcustomeranswer");

        $data['contentHtml'] = $mailtemplate->getContentHtml() ?? $mailtemplate->getTranslated()['contentHtml'];
        $data['contentPlain'] = $mailtemplate->getContentPlain() ?? $mailtemplate->getTranslated()['contentPlain'];
        $data['subject'] = $mailtemplate->getSubject() ?? $mailtemplate->getTranslated()['subject'];


        $data['salesChannelId'] = $requestdata["salesChannelId"];
        $data['salesChannel'] = $salesChannel;
        $data['senderName'] = $requestdata["senderName"];

        $this->mailService->send(
            $data,
            $context,
            [
                'salesChannelId' => $requestdata["salesChannelId"],
                'data' => $templateData
            ]
        );

    }

    private function getMailTemplate(Context $context, string $technicalName)
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('mailTemplateType.technicalName', $technicalName));
        $criteria->setLimit(1);

        /** @var MailTemplateEntity|null $mailTemplate */
        $mailTemplate = $this->mailTemplateRepository->search($criteria, $context)->first();

        return $mailTemplate;
    }

    /**
     * @Route("/api/v{version}/nimbits/articlequestion/validaterecaptcha", name="api.action.nimbits.articlequestion.validaterecaptcha", methods={"POST"})
     */
    public function validaterecaptcha(Request $request, Context $context): JsonResponse
    {
        $deftoken = $request->get('deftoken');
        $secret = $request->get('secret');


        if (empty($deftoken)) {
            return new JsonResponse(["success" => false, "msg" => 'The token is not filled.']);
        }
        if (empty($secret)) {
            return new JsonResponse(["success" => false, "msg" => 'The secret not filled.']);
        }

        if (preg_match('/\s/', $deftoken)) {
            return new JsonResponse(["success" => false, "msg" => 'The token contains a whitespace.']);
        }

        if (preg_match('/\s/', $secret)) {
            return new JsonResponse(["success" => false, "msg" => 'The secret contains a whitespace.']);
        }


        $tmp = file_get_contents("https://www.google.com/recaptcha/api.js?render=" . $deftoken);

        if (!strpos($tmp, "push('" . $deftoken . "')")) {
            return new JsonResponse(["success" => false, "msg" => 'The token is invalid.']);
        }


        $data = array('secret' => $secret, 'response' => "test");

        $url = $this->recaptchaverifyurl;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        $verifyResponse = curl_exec($ch);
        curl_close($ch);

        $responseData = json_decode($verifyResponse, true);

        foreach ($responseData["error-codes"] as $ec) {
            if ($ec == "invalid-input-secret") {
                return new JsonResponse(["success" => false, "msg" => 'The secret is invalid.']);
            }
        }


        return new JsonResponse(["success" => true]);


    }
}
