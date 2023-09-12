<?php declare(strict_types=1);

namespace RHWeb\ThemeFeatures\Administration\Controller;

use RHWeb\ThemeFeatures\Core\Service\DataService;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SettingsController
 * @RouteScope(scopes={"api"})
 */
class SettingsController
{
    private DataService $dataService;

    public function __construct(DataService $dataService)
    {
        $this->dataService = $dataService;
    }

    /**
     * @Route("/api/rhweb-theme-features/settings/demo-data/options", name="api.rhweb-theme-features.settings.demo-data.options", methods={"GET"})
     */
    public function demoDataOptions(): JsonResponse
    {
        return new JsonResponse(
            $this->dataService->getOptions()
        );
    }

    /**
     * @Route("/api/rhweb-theme-features/settings/demo-data/install", name="api.rhweb-theme-features.settings.demo-data.install", methods={"POST"})
     */
    public function demoDataInstall(Request $request): JsonResponse
    {
        if ($request->get('salesChannelId') && !in_array($request->get('salesChannelId'), ['undefined','null'])) {
            $this->dataService->setSalesChannelId($request->get('salesChannelId'));
        }

        $this->dataService->remove($request->get('pluginName'), 'demo');
        $this->dataService->install($request->get('pluginName'), 'demo', $request->get('name'));

        return new JsonResponse([]);
    }

    /**
     * @Route("/api/rhweb-theme-features/settings/demo-data/remove", name="api.rhweb-theme-features.settings.demo-data.remove", methods={"POST"})
     */
    public function demoDataRemove(Request $request): JsonResponse
    {
        if ($request->get('salesChannelId') && !in_array($request->get('salesChannelId'), ['undefined','null'])) {
            $this->dataService->setSalesChannelId($request->get('salesChannelId'));
        }

        $this->dataService->remove($request->get('pluginName'), 'demo');

        return new JsonResponse([]);
    }
}
