import deDE from './snippet/de-DE.json';
import enGB from './snippet/en-GB.json';
import '../../app/component/nimbits-articlequestions-list';
import '../../app/component/nimbits-articlequestions-detail';

const {Module} = Shopware;

Shopware.Module.register('nb-articlequestions', {
    type: 'plugin',
    name: 'ArticleQuestions',
    title: 'Article Questions',
    description: 'This modules allows you to see all incoming article questions',
    color: '#62ff80',
    icon: 'default-arrow-simple-right',

    snippets: {
        'de-DE': deDE,
        'en-GB': enGB
    },

    routes: {
        overview: {
            component: 'nimbits-articlequestions-list',
            path: 'overview'
        },
        //create: {
        //    component: 'nimbits-articlequestions-detail',
        //    path: 'create'/*,
        //    redirect: {
        //       name: 'nb.articlequestions.create'
        //    }*/
        //},
        detail: {
            component: 'nimbits-articlequestions-detail',
            path: 'detail/:id?',
            props: {
                default: (route) => ({id: route.params.id})
            }/*,
            redirect: {
                name: 'nb.articlequestions.detail'
            }*/
        }
    },

    navigation: [{
        label: 'nb-articlequestions.menu.label',
        color: '#62ff80',
        path: 'nb.articlequestions.overview',
        parent: 'sw-catalogue',
        icon: 'default-arrow-simple-right',
        position: 60
    }]
});
