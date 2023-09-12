import './page/list';
import './page/detail';
import './page/create';

import deDE from './snippet/de-DE';
import enGB from './snippet/en-GB';

const { Module } = Shopware;

Module.register('lzyt-drop', {
    type: 'plugin',
    title: 'lzyt-drop.general.headline',
    icon: 'default-shopping-paper-bag-product',

    snippets: {
        'de-DE': deDE,
        'en-GB': enGB
    },

    settingsItem: [{
        group: 'shop',
        to: 'lzyt.drop.list',
        icon: 'default-object-rocket',
        name: 'lzyt-drop.general.headline'
    }],

    routes: {
        list: {
            component: 'lzyt-drop-list',
            path: 'list'
        },
        detail: {
            component: 'lzyt-drop-detail',
            path: 'detail/:id',
            meta: {
                parentPath: 'lzyt.drop.list'
            }
        },
        create: {
            component: 'lzyt-drop-create',
            path: 'create',
            meta: {
                parentPath: 'lzyt.drop.list'
            }
        }
    }
});