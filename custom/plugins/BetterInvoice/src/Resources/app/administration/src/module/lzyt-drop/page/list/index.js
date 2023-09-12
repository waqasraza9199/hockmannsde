const {Component, Mixin, Context} = Shopware;
const {Criteria} = Shopware.Data;

import template from './lzyt-drop-list.html.twig';
import './lzyt-drop-list.scss';

Component.register('lzyt-drop-list', {
    template,

    inject: [
        'repositoryFactory'
    ],

    mixins: [
        Mixin.getByName('notification'),
        Mixin.getByName('listing')
    ],

    data() {
        return {
            repository: null,
            records: null,
            record: null,
            isLoading: false,
            term: '',
            sortBy: this.$route.params.sortBy || 'id',
            sortDirection: this.$route.params.sortDirection || 'DESC',
            naturalSorting: true,
            cloning: false
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },

    computed: {
        getRepository() {
            if (!this.repository)
                this.repository = this.repositoryFactory.create('lzyt_custom_drop');

            return this.repository;
        },
        listingCriteria() {
            const criteria = new Criteria(this.page, this.limit);
            criteria.addAssociation('rule').setTerm(this.term);

            if (this.sortBy)
                criteria.addSorting(Criteria.sort(this.sortBy, this.sortDirection));

            if (this.filterRootLanguages)
                criteria.addFilter(Criteria.equals('parentId', null));

            if (this.filterInheritedLanguages)
                criteria.addFilter(Criteria.not('AND', [Criteria.equals('parentId', null)]));

            return criteria;
        },

        columns() {
            return [{
                property: 'iban',
                label: this.$tc('lzyt-drop.list.columnIban'),
                dataIndex: 'iban',
                routerLink: 'lzyt.drop.detail',
                allowResize: false,
            }, {
                property: 'bic',
                label: this.$tc('lzyt-drop.list.columnBic'),
                dataIndex: 'bic',
                allowResize: false,
            }, {
                property: 'bank',
                label: this.$tc('lzyt-drop.list.columnBank'),
                dataIndex: 'bank',
                allowResize: false,
            }, {
                property: 'rule.name',
                label: this.$tc('lzyt-drop.list.columnRule'),
                dataIndex: 'rule.name',
                allowResize: false,
            }, {
                property: 'priority',
                label: this.$tc('lzyt-drop.list.columnPriority'),
                dataIndex: 'priority',
                allowResize: false,
            }, {
                property: 'enabled',
                label: this.$tc('lzyt-drop.list.columnEnabled'),
                dataIndex: 'enabled',
                inlineEdit: 'boolean',
                allowInlineEdit: true,
                allowResize: false,
                align: 'center'
            }]
        }
    },

    watch: {
        listingCriteria() {
            this.getList();
        }
    },

    methods: {
        getList() {
            this.isLoading = true;

            this.getRepository.search(this.listingCriteria, Context.api).then((items) => {
                this.total = items.total;
                this.records = items;
                this.isLoading = false;

                return items;
            }).catch(() => {
                this.isLoading = false;
            });
        }
    }

});