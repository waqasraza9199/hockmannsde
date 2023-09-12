<?php
namespace Lenz\GoogleShopping\Controller;

use Lenz\GoogleShopping\Service\GoogleShoppingTaxonomyService;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"storefront"})
 */
class GoogleShoppingTaxonomyApiController extends StorefrontController
{

    /**
     * @var GoogleShoppingTaxonomyService
     */
    private $googleShoppingTaxonomyService;

    public function __construct(GoogleShoppingTaxonomyService $googleShoppingTaxonomyService)
    {
        $this->googleShoppingTaxonomyService = $googleShoppingTaxonomyService;
    }

    /**
     * @RouteScope(scopes={"storefront"})
     * @Route("/lenz_google_shopping/update_taxonomy", name="frontend.lenzGoogleShopping.updateTaxonomy", options={"seo"=false}, defaults={"csrf_protected"=false}, methods={"GET"})
     */
    public function updateTaxonomy(Request $request, Context $context): Response
    {
        $time = microtime(true);
        try {
            if(ini_get('max_execution_time') < 60) {
                ini_set('max_execution_time', 60);
            }

            ini_set('memory_limit', '512M');
            $this->googleShoppingTaxonomyService->import();

            return new Response('Update successful. Took ' . ceil(microtime(true) - $time) . ' seconds');

        } catch(\Exception $e) {
            // Do nothing.
        }

        return new Response('Could not perform update. Please execute "<b style="color: red;">bin/console lenz:googleshopping:taxonomy:update</b>" in console.');
    }
}
