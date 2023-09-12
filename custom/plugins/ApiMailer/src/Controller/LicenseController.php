<?php declare(strict_types=1);

namespace LZYT8\ApiMailer\Controller;

use DateTime;
use LZYT8\ApiMailer\Service\LicenseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;

/**
 * @RouteScope(scopes={"api"})
 */
class LicenseController extends AbstractController
{
    private LicenseService $licenseService;

    public function __construct(LicenseService $licenseService)
    {
        $this->licenseService = $licenseService;
    }

    /**
     * @Route("/api/lzyt-apimailer/license/sync", name="api.lzytapimailer.license.sync", methods={"GET"})
     */
    public function sync(): JsonResponse
    {
        $expiryDate = $this->licenseService->fetchValidUntil();
        if (is_null($expiryDate))
            $expiryDate = (new DateTime())->modify('-24 hours');

        return new JsonResponse(['validUntil' => $expiryDate]);
    }
}