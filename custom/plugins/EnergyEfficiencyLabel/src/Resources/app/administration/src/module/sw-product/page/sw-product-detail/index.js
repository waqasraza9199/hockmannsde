import template from './sw-product-detail.html.twig';

Shopware.Component.override('sw-product-detail', {
    template,

    computed:{
        lzytenevRoute() {
            return {
                name: 'lzyt.enev.create',
                params: { productId: Shopware.State.get('swProductDetail').product.id }
            };
        },
    }
});
