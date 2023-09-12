import template from "./sw-product-cross-selling-assignment.html.twig";

const { Component } = Shopware;
const { Criteria } = Shopware.Data;

Component.override('sw-product-cross-selling-assignment', {
    template,

    computed: {
        searchCriteria() {
            const criteria = new Criteria();

            criteria.addFilter(Criteria.not('and', [Criteria.equals('id', this.product.id)]));
            criteria.addAssociation('options.group');

            return criteria;
        },
    },
});
