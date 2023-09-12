import './page/cogi-tags-list';
import './page/cogi-tags-detail';
import deDE from './snippet/de-DE.json';
import enGB from './snippet/en-GB.json';
import esES from './snippet/es-ES.json';
import itIT from './snippet/it-IT.json';
import nlNL from './snippet/nl-NL.json';
import frFR from './snippet/fr-FR.json';

const { Module } = Shopware;

Module.register('cogi-tags', {
    type: 'plugin',
    name: 'Tag',
    title: 'cogi-tags.general.mainMenuItemGeneral',
    description: 'cogi-tags.general.descriptionTextModule',
    color: '#f5be00',
    icon: 'default-action-tags',
    favicon: 'icon-module-settings.png',

    snippets: {
        'de-DE': deDE,
        'en-GB': enGB,
        'es-ES': esES,
        'it-IT': itIT,
        'nl-NL': nlNL,
        'fr-FR': frFR
    },

    routes: {
        list: {
            component: 'cogi-tags-list',
            path: 'list'
        },
        detail: {
            component: 'cogi-tags-detail',
            path: 'detail/:id',
            meta: {
                parentPath: 'cogi.tags.list'
            }
        }
    },

    navigation: [{
        label: 'cogi-tags.general.mainMenuItemGeneral',
        color: '#f5be00',
        path: 'cogi.tags.list',
        icon: 'default-action-tags',
        parent: 'sw-content'
    }]
});
