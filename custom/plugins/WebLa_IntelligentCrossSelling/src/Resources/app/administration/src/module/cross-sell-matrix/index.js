import './page/cross-sell-matrix-list';
import './page/cross-sell-matrix-create';

const { Module } = Shopware;

Module.register('cross-sell-matrix', {
    type: 'core',
    name: 'settings-cross-sell',
    title: 'cross-sell-matrix.general.mainMenuItemGeneral',
    description: '',
    color: '#9AA8B5',
    icon: 'default-action-settings',
    favicon: 'icon-module-settings.png',
    entity: 'crossSell',

    routes: {
        index: {
            component: 'cross-sell-matrix-list',
            path: 'index',
            meta: {
                parentPath: 'sw.settings.index',
            },
        },
        create: {
            component: 'cross-sell-matrix-create',
            path: 'create',
            meta: {
                parentPath: 'cross.sell.matrix.index'
            },
        },
    },

    settingsItem: {
        group: 'plugins',
        to: 'cross.sell.matrix.index',
        icon: 'default-chart-pie',
        name: 'cross-sell-matrix.general.mainMenuItemGeneral'
    },
});
