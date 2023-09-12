<?php

declare(strict_types=1);

namespace WebLa_IntelligentCrossSelling\Subscriber;

use Shopware\Core\Content\Product\Aggregate\ProductCrossSelling\ProductCrossSellingEntity;
use Shopware\Core\Content\Product\Aggregate\ProductVisibility\ProductVisibilityDefinition;
use Shopware\Core\Content\Product\ProductCollection;
use Shopware\Core\Content\Product\SalesChannel\AbstractProductCloseoutFilterFactory;
use Shopware\Core\Content\Product\SalesChannel\CrossSelling\CrossSellingElement;
use Shopware\Core\Content\Product\SalesChannel\CrossSelling\CrossSellingElementCollection;
use Shopware\Core\Content\Product\SalesChannel\CrossSelling\ProductCrossSellingRoute;
use Shopware\Core\Content\Product\SalesChannel\CrossSelling\ProductCrossSellingRouteResponse;
use Shopware\Core\Content\Product\SalesChannel\ProductAvailableFilter;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\AndFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\OrFilter;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SalesChannel\Entity\SalesChannelRepositoryInterface;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\HttpFoundation\Request;

class CrossSellingLoader extends ProductCrossSellingRoute
{

    private $originalProductCrossSellingRoute;

    private $crossSellingSettingsRepository;
    private $productRepository;
    private $crossSellingPropertyGroupRepository;
    private $systemConfigService;
    private $productCloseoutFilterFactory;


    public function __construct(
        SalesChannelRepositoryInterface $productRepository,
        EntityRepositoryInterface $crossSellingSettingsRepository,
        EntityRepositoryInterface $crossSellingPropertyGroupRepository,
        ProductCrossSellingRoute  $originalProductCrossSellingRoute,
        SystemConfigService $systemConfigService,
        AbstractProductCloseoutFilterFactory $productCloseoutFilterFactory
    ) {
        $this->crossSellingSettingsRepository = $crossSellingSettingsRepository;
        $this->crossSellingPropertyGroupRepository = $crossSellingPropertyGroupRepository;
        $this->productRepository = $productRepository;
        $this->originalProductCrossSellingRoute = $originalProductCrossSellingRoute;
        $this->systemConfigService = $systemConfigService;
        $this->productCloseoutFilterFactory = $productCloseoutFilterFactory;
    }

    public function getDecorated(): ProductCrossSellingRoute
    {
        return  $this->originalProductCrossSellingRoute;
    }

    /**
     * @Since("6.3.2.0")
     * @Entity("product")
     * @OA\Post(
     *      path="/product/{productId}/cross-selling",
     *      summary="Fetch cross-selling groups of a product",
     *      description="This route is used to load the cross sellings for a product. A product has several cross selling definitions in which several products are linked. The route returns the cross sellings together with the linked products",
     *      operationId="readProductCrossSellings",
     *      tags={"Store API","Product"},
     *      @OA\Parameter(
     *          name="productId",
     *          description="Product ID",
     *          @OA\Schema(type="string"),
     *          in="path",
     *          required=true
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Found cross sellings",
     *          @OA\JsonContent(ref="#/components/schemas/CrossSellingElementCollection")
     *     )
     * )
     * @Route("/store-api/product/{productId}/cross-selling", name="store-api.product.cross-selling", methods={"POST"})
     */
    public function load(string $productId, Request $request, SalesChannelContext $context, Criteria $criteria): ProductCrossSellingRouteResponse
    {
        $settingsCriteria = new Criteria();
        $settingsCriteria->addFilter(new EqualsFilter('active', true));

        $settings = $this->crossSellingSettingsRepository->search($settingsCriteria, $context->getContext());

        if ($settings->getTotal() < 1) {
            return
                $this->getDecorated()->load($productId, $request, $context, $criteria);
        }

        $crossSelling = new ProductCrossSellingEntity();
        if ($settings->first()->isShowTitle()) {
            $crossSelling->setName($settings->first()->getTitle());
            $crossSelling->setTranslated(["name" => $settings->first()->getTitle()]);
        }
        $crossSelling->setPosition(1);
        $crossSelling->setActive(true);
        $crossSelling->setId(Uuid::randomHex());

        $element = new CrossSellingElement();
        $element->setCrossSelling($crossSelling);
        $element->setProducts(new ProductCollection());
        $element->setTotal(0);

        $products = $this->loadSimilarProduct($productId, $context, $settings->first()->getMaxProducts(), $settings->first()->isOnlyCategory());
        $element->setProducts($products);
        $element->setTotal(count($products));
        $elements = new CrossSellingElementCollection();
        $elements->add($element);


        $origData = $this->getDecorated()->load($productId, $request, $context, $criteria)->getResult();
        $collection = new CrossSellingElementCollection($elements, $context);
        foreach ($origData as $other) {
            $collection->add($other);
        }
        return new ProductCrossSellingRouteResponse($collection);
    }

    private function loadSimilarProduct($productId, $context, $length, $isOnlyCategory)
    {
        // get product
        $productCriteria = new Criteria();
        $productCriteria->addFilter(new EqualsFilter('id', $productId));
        $productCriteria->addAssociation('properties.group');
        /** @var \Shopware\Core\Content\Product\ProductEntity */
        $product = $this->productRepository->search($productCriteria, $context)->first();
        $propertyGroups = $product->getProperties()->getElements();

        $categoryTree = $product->getCategoryTree();
        // get weights
        $weights = $this->crossSellingPropertyGroupRepository->search(new Criteria(), $context->getContext());
        $initialCriteria = new Criteria();

        if ($isOnlyCategory) {
            if ($categoryTree) {
                $filters[] = new ContainsFilter('categoryIds', end($categoryTree));
            }
        }
        $filters[] = new EqualsFilter('parentId', null);

        $initialCriteria->addFilter(new MultiFilter('AND', [
            new NotFilter('OR', [
                new EqualsFilter('id', $productId),
            ]),
            new NotFilter('OR', [
                new EqualsFilter('id', $product->getParentId()),
            ]),
            new NotFilter('OR', [
                new EqualsFilter('parentId', $productId),
            ])
        ]));

        $initialCriteria->addFilter(new MultiFilter('AND', $filters));

        $initialCriteria->addFilter(
            new ProductAvailableFilter($context->getSalesChannel()->getId(), ProductVisibilityDefinition::VISIBILITY_LINK)
        );

        $this->handleAvailableStock($initialCriteria, $context);

        $intialProductIds = $this->productRepository->search($initialCriteria, $context)->getIds();

        $parameters = [];
        $finalProducts = $this->mergeProductArrays(array(), $intialProductIds, 1);

        foreach ($propertyGroups as $propertyGroup) {
            $weightEntity = $this->findObjectById($propertyGroup->getGroupId(), $weights->getEntities());
            if ($weightEntity) {
                $weight = $weightEntity->getWeight();
                array_push($parameters, ["weight" => $weight, "propertyId" => $propertyGroup->getId()]);
            }
        }

        foreach ($parameters as $parameter) {
            $criteria = new Criteria();
            $criteria->addFilter(
                new MultiFilter(
                    'AND',
                    [
                        new NotFilter('AND', [new EqualsFilter('id', $productId)]),
                        new ContainsFilter('propertyIds', $parameter["propertyId"])
                    ]
                )
            );
            $criteria->addFilter(new MultiFilter('AND', [
                new NotFilter('OR', [
                    new EqualsFilter('id', $productId),
                ]),
                new NotFilter('OR', [
                    new EqualsFilter('id', $product->getParentId()),
                ]),
                new NotFilter('OR', [
                    new EqualsFilter('parentId', $product->getParentId()),
                ]),
                new NotFilter('OR', [
                    new EqualsFilter('parentId', $productId),
                ])
            ]));
            if ($isOnlyCategory) {
                if ($categoryTree) {
                    $filters[] = new ContainsFilter('categoryIds', end($categoryTree));
                }
                $filters[] = new EqualsFilter('parentId', null);
                $criteria->addFilter(new MultiFilter('AND', $filters));
            }
            $criteria->addFilter(
                new ProductAvailableFilter($context->getSalesChannel()->getId(), ProductVisibilityDefinition::VISIBILITY_LINK)
            );
            $this->handleAvailableStock($criteria, $context);
            $newIds = $this->productRepository->search($criteria, $context);
            $finalProducts = $this->mergeProductArrays($finalProducts, $newIds->getIds(), $parameter["weight"] / 100);
        }

        asort($finalProducts, SORT_NUMERIC);
        $finalProducts = array_reverse($finalProducts, true);

        $searchKeys = array_keys($finalProducts);

        $collection = new ProductCollection(array());

        foreach (array_slice($searchKeys, 0, $length)  as $key) {
            $criteria = (new Criteria())->addFilter(new EqualsFilter('id', $key))->setLimit(1);

            $res = $this->productRepository->search(
                $criteria,
                $context
            );
            $collection->add($res->getEntities()->first());
        }
        return $collection;
    }
    private function findObjectById($id, $array)
    {
        foreach ($array as $element) {
            if ($id === $element->getPropertyGroupId()) {
                return $element;
            }
        }
        return false;
    }
    private function mergeProductArrays($products, $newProducts, $weight)
    {
        $result = $products;
        foreach ($newProducts as $product) {
            if (array_key_exists($product, $products)) {
                $result[$product] = $products[$product] + $weight;
            } else {
                $result[$product] = $weight;
            }
        }
        return $result;
    }

    private function handleAvailableStock(Criteria $criteria, SalesChannelContext $context): void
    {
        $salesChannelId = $context->getSalesChannel()->getId();

        $hide = $this->systemConfigService->get('core.listing.hideCloseoutProductsWhenOutOfStock', $salesChannelId);

        if (!$hide) {
            return;
        }

        $closeoutFilter = $this->productCloseoutFilterFactory->create($context);
        $criteria->addFilter($closeoutFilter);
    }
}
