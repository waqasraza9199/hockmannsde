import template from './sw-product-detail.html.twig';
import deDE from "./snippet/de-DE.json";
import enGB from "./snippet/en-GB.json";

Shopware.Component.override('sw-product-detail', {
    template,

    snippets: {
        'de-DE': deDE,
        'en-GB': enGB
    }
});

