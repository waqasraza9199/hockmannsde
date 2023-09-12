<?php declare(strict_types=1);

namespace LZYT\Enev\Service;

use LZYT\Enev\Core\Content\Enev\EnevEntity;
use Shopware\Core\Content\Product\SalesChannel\Listing\ProductListingResult;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Search\AggregationResult\AggregationResult;

class Filter
{
    /**
     * workaround to set the correct filter list
     *
     * @param ProductListingResult $result
     * @param $aggregation
     * @param $field
     */
    public function setFilterList(ProductListingResult $result, $aggregation, $field): void
    {
        $getter = 'get' . ucfirst($field);
        $aggregations = $result->getAggregations();
        /** @var AggregationResult $filters */
        $aggregationsResults = $aggregations->get($aggregation);
        if($aggregationsResults === null){
            return;
        }
        /** @var EntityCollection $filters */
        $filters = $aggregationsResults->getEntities();
        $foundFilters = [];
        /** @var  EnevEntity $filter */
        foreach ($filters as $index => $filter) {
            if(is_callable([$filter, $getter])){
                if (isset($foundFilters[$filter->$getter()])) {
                    $filters->remove($index);
                    continue;
                }
                $foundFilters[$filter->$getter()] = true;
            }
        }
    }
}