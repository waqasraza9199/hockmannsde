<?php

namespace EmcgnNoVariantPreselection\Component;

class CheckId
{
    /**
     * Compare current id with master product id
     * if it is the same, then the master product should be loaded
     *
     * @param $request
     * @param $masterProduct
     * @return bool
     */
    public function compareId($request, $masterProduct)
    {
        $currentId = $request->attributes->get('productId');
        $masterProductId = $masterProduct->getId();

        if ($currentId == $masterProductId) {
            return true;
        } else {
            return false;
        }
    }
}
