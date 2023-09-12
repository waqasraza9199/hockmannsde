<?php declare(strict_types=1);

namespace LZYT\Enev\Service;

use LZYT\Enev\Core\Content\Enev\EnevCollection;
use LZYT\Enev\Core\Content\Enev\EnevEntity;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\Api\Response\Type\Api\JsonApiType;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Exception\InconsistentCriteriaIdsException;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\RequestCriteriaBuilder;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;

class Product
{
    /** @var EntityRepositoryInterface */
    private $lzytEnevRepository;

    /** @var EntityRepositoryInterface */
    private $productRepository;

    /** @var RequestCriteriaBuilder */
    private $searchCriteriaBuilder;

    private JsonApiType $responseFactory;

    /**
     * Product constructor.
     *
     * @param EntityRepositoryInterface $lzytEnevRepository
     * @param EntityRepositoryInterface $productRepository
     * @param RequestCriteriaBuilder $searchCriteriaBuilder
     * @param JsonApiType $responseFactory
     */
    public function __construct(
        EntityRepositoryInterface $lzytEnevRepository,
        EntityRepositoryInterface $productRepository,
        RequestCriteriaBuilder $searchCriteriaBuilder,
        JsonApiType $responseFactory
    ) {
        $this->lzytEnevRepository = $lzytEnevRepository;
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->responseFactory = $responseFactory;
    }

    /**
     * find products ex lzytEnev products
     *
     * @param Request $request
     * @param Context $context
     *
     * @return EntitySearchResult
     * @throws InconsistentCriteriaIdsException
     */
    public function products(Request $request, Context $context): EntitySearchResult
    {
        $products = $this->enevProducts($context);
        $criteria = new Criteria();
        $criteria = $this->searchCriteriaBuilder->handleRequest(
            $request,
            $criteria,
            $this->productRepository->getDefinition(),
            $context
        );
        if (count($products)) {
            $criteria->addFilter(new NotFilter(NotFilter::CONNECTION_AND,
                [new EqualsAnyFilter('product.id', $products)]));
        }

        return $this->productRepository->search($criteria, $context);
    }

    /**
     * get product IDs from EnEV
     *
     * @param Context $context
     *
     * @return array
     * @throws InconsistentCriteriaIdsException
     */
    public function enevProducts(Context $context): array
    {
        $enevs = $this->lzytEnevRepository->search(new Criteria(), $context);
        $products = [];

        /**
         * @var EnevCollection $enev
         */
        foreach ($enevs->getElements() as $index => $enev) {
            $products[] = $enev->get('product')->getId();
        }

        return $products;
    }

    /**
     * find enev by product
     *
     * @param $productId
     * @param Context $context
     * @param bool $activeOnly
     * @return EntitySearchResult
     * @throws InconsistentCriteriaIdsException
     */
    public function findEnevByProductId(
        $productId,
        Context $context,
        $activeOnly = true
    ): EntitySearchResult {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('lzyt_enev.productId', $productId))
            ->addSorting(new FieldSorting('position'))
            ->addAssociation('media')
            ->addAssociation('datasheet')
            ->addAssociation('icon');

        if ($activeOnly) {
            $criteria->addFilter(new EqualsFilter('lzyt_enev.active', true));
        }

        return $this->lzytEnevRepository->search($criteria, $context);
    }

    /**
     * @param $productId
     * @param Context $context
     * @return ProductEntity|null
     */
    public function findProductById(
        $productId,
        Context $context
    ) {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('id', $productId));

        return $this->productRepository->search($criteria, $context)->first();
    }

    /**
     * @param $productNumber
     * @param Context $context
     * @return ProductEntity|null
     */
    public function findProductByNumber(
        $productNumber,
        Context $context
    ) {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('productNumber', $productNumber));

        return $this->productRepository->search($criteria, $context)->first();
    }

    /**
     * @return EntityRepositoryInterface
     */
    public function getlzytEnevRepository(): EntityRepositoryInterface
    {
        return $this->lzytEnevRepository;
    }

    /**
     * @return EntityRepositoryInterface
     */
    public function getProductRepository(): EntityRepositoryInterface
    {
        return $this->productRepository;
    }

    /**
     * @return RequestCriteriaBuilder
     */
    public function getSearchCriteriaBuilder(): RequestCriteriaBuilder
    {
        return $this->searchCriteriaBuilder;
    }
}