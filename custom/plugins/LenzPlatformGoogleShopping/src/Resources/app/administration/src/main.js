import './page/sw-product-detail';
import './view/sw-product-detail-lenz-google-shopping';

Shopware.Module.register('sw-product-lenz-google-shopping-tab', {
    routeMiddleware(next, currentRoute) {
        if (currentRoute.name === 'sw.product.detail') {
            currentRoute.children.push({
                name: 'sw.product.detail.lenzgoogleshopping',
                path: '/sw/product/detail/:id/lenzgoogleshopping',
                component: 'sw-product-detail-lenzgoogleshopping',
                meta: {
                    parentPath: "sw.product.index"
                }
            });
        }
        next(currentRoute);
    }
});
