import './page/list';
import './page/detail';
import './page/create';
import deDE from './snippet/de-DE.json';

const {Module} = Shopware;

Module.register('lzyt-enev', {
    type: 'plugin',
    name: 'LzytEnev',
    title: 'lzyt-enev.menu.main',
    description: 'lzyt-enev.menu.description',
    color: '#ff4e00',
    icon: 'default-shopping-paper-bag-product',
    entity: 'lzyt_enev',

    snippets: {
        'de-DE': deDE
    },

    routes: {
        list: {
            component: 'lzyt-enev-list',
            path: 'list'
        },
        detail: {
            component: 'lzyt-enev-detail',
            path: 'detail/:id',
            meta: {
                parentPath: 'lzyt.enev.list'
            }
        },
        create: {
            component: 'lzyt-enev-create',
            path: 'create/:productId?',
            meta: {
                parentPath: 'lzyt.enev.list'
            }
        },
    },

    navigation: [{
        label: 'lzyt-enev.menu.main',
        color: '#ff4e00',
        path: 'lzyt.enev.list',
        icon: 'default-shopping-paper-bag-product',
        position: 999,
        parent: 'sw-catalogue',
    }],
});