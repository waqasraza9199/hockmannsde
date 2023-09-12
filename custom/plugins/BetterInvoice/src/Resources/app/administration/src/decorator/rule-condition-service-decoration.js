const { Application } = Shopware;

import '../core/component/lzyt-shop-sales';

Application.addServiceProviderDecorator('ruleConditionDataProviderService', (ruleConditionService) => {
    ruleConditionService.addCondition('lzyt_shop_sales', {
        component: 'lzyt-shop-sales',
        label: 'Gesamtumsatz im Shop',
        scopes: ['global']
    });

    return ruleConditionService;
});