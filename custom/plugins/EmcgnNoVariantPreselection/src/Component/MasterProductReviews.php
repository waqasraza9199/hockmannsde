<?php

namespace EmcgnNoVariantPreselection\Component;

use Shopware\Core\Content\Product\Aggregate\ProductReview\ProductReviewEntity;
use Shopware\Core\Content\Product\SalesChannel\Review\AbstractProductReviewRoute;
use Shopware\Core\Content\Product\SalesChannel\Review\ProductReviewResult;
use Shopware\Core\Content\Product\SalesChannel\Review\RatingMatrix;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Aggregation\Bucket\FilterAggregation;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Aggregation\Bucket\TermsAggregation;
use Shopware\Core\Framework\DataAbstractionLayer\Search\AggregationResult\Bucket\TermsResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;

class MasterProductReviews
{
    private const LIMIT = 10;
    private const DEFAULT_PAGE = 1;
    private const FILTER_LANGUAGE = 'filter-language';

    private AbstractProductReviewRoute $productReviewRoute;

    public function __construct(AbstractProductReviewRoute $productReviewRoute)
    {
        $this->productReviewRoute = $productReviewRoute;
    }

    public function loadProductReviews(SalesChannelProductEntity $masterProduct, Request $request, SalesChannelContext $context): ProductReviewResult
    {
        $reviewCriteria = $this->createReviewCriteria($request, $context);

        $reviews = $this->productReviewRoute
            ->load($masterProduct->getId(), $request, $context, $reviewCriteria)
            ->getResult();

        $matrix = $this->getReviewRatingMatrix($reviews);

        $reviewResult = ProductReviewResult::createFrom($reviews);
        $reviewResult->setMatrix($matrix);
        $reviewResult->setProductId($masterProduct->getId());
        $reviewResult->setCustomerReview($this->getCustomerReview($masterProduct->getId(), $context));
        $reviewResult->setTotalReviews($matrix->getTotalReviewCount());
        $reviewResult->setProductId($masterProduct->getId());
        $reviewResult->setParentId($masterProduct->getId());

        return $reviewResult;
    }

    private function createReviewCriteria(Request $request, SalesChannelContext $context): Criteria
    {
        $limit = $request->request->get('limit', self::LIMIT);
        $page = $request->request->get('p', self::DEFAULT_PAGE);
        $offset = $limit * ($page - 1);

        $criteria = new Criteria();
        $criteria->setLimit($limit);
        $criteria->setOffset($offset);

        $sorting = new FieldSorting('createdAt', 'DESC');
        if ($request->request->get('sort', 'points') === 'points') {
            $sorting = new FieldSorting('points', 'DESC');
        }

        $criteria->addSorting($sorting);

        if ($request->request->get('language') === self::FILTER_LANGUAGE) {
            $criteria->addPostFilter(
                new EqualsFilter('languageId', $context->getContext()->getLanguageId())
            );
        }

        $this->handlePointsAggregation($request, $criteria);

        return $criteria;
    }

    private function handlePointsAggregation(Request $request, Criteria $criteria): void
    {
        $points = $request->request->get('points', []);

        if (\is_array($points) && \count($points) > 0) {
            $pointFilter = [];
            foreach ($points as $point) {
                $pointFilter[] = new RangeFilter('points', [
                    'gte' => $point - 0.5,
                    'lt' => $point + 0.5,
                ]);
            }

            $criteria->addPostFilter(new MultiFilter(MultiFilter::CONNECTION_OR, $pointFilter));
        }

        $criteria->addAggregation(
            new FilterAggregation(
                'status-filter',
                new TermsAggregation('ratingMatrix', 'points'),
                [new EqualsFilter('status', 1)]
            )
        );
    }

    private function getReviewRatingMatrix(EntitySearchResult $reviews): RatingMatrix
    {
        $aggregation = $reviews->getAggregations()->get('ratingMatrix');

        if ($aggregation instanceof TermsResult) {
            return new RatingMatrix($aggregation->getBuckets());
        }

        return new RatingMatrix([]);
    }

    private function getCustomerReview(string $productId, SalesChannelContext $context): ?ProductReviewEntity
    {
        $customer = $context->getCustomer();

        if (!$customer) {
            return null;
        }

        $criteria = new Criteria();
        $criteria->setLimit(1);
        $criteria->setOffset(0);
        $criteria->addFilter(new EqualsFilter('customerId', $customer->getId()));

        $customerReviews = $this->productReviewRoute
            ->load($productId, new Request(), $context, $criteria)
            ->getResult();

        return $customerReviews->first();
    }
}
