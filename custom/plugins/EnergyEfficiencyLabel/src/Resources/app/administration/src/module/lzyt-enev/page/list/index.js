import template from './index.html.twig';
import './index.scss';

const {Component, Mixin} = Shopware;
const {Criteria} = Shopware.Data;

Component.register('lzyt-enev-list', {
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

    computed: {
        getRepository() {
            if (!this.repository) {
                this.repository = this.repositoryFactory.create('lzyt_enev');
            }

            return this.repository;
        },
        listingCriteria() {
            const criteria = new Criteria(this.page, this.limit);
            criteria.addAssociation('product')
                .addAssociation('media')
                .addAssociation('datasheet')
                .addAssociation('icon')
                .setTerm(this.term)
                .addFilter(Criteria.not('AND', [Criteria.equals('productId', null)]));

            if (this.sortBy) {
                criteria.addSorting(Criteria.sort(this.sortBy, this.sortDirection));
            }
            criteria.addSorting(Criteria.sort('position', 'ASC'));

            if (this.filterRootLanguages) {
                criteria.addFilter(Criteria.equals('parentId', null));
            }

            if (this.filterInheritedLanguages) {
                criteria.addFilter(Criteria.not('AND', [Criteria.equals('parentId', null)]));
            }

            return criteria;
        },
        columns() {
            return [{
                property: 'product.name',
                dataIndex: 'product.name',
                label: this.$tc('lzyt-enev.elements.product'),
                routerLink: 'lzyt.enev.detail',
                allowInlineEdit: false,
                allowResize: true,
                primary: true
            }, {
                property: 'productNumber',
                dataIndex: 'product.productNumber',
                label: this.$tc('sw-product.list.columnProductNumber'),
                inlineEdit: 'string',
                allowInlineEdit: true,
                allowResize: true,
                primary: false
            }, {
                property: 'color',
                dataIndex: 'color',
                label: this.$tc('lzyt-enev.elements.energyEfficiency'),
                allowInlineEdit: false,
                allowResize: true,
                primary: false
            }, {
                property: 'position',
                dataIndex: 'position',
                label: this.$tc('lzyt-enev.elements.position'),
                inlineEdit: 'number',
                allowInlineEdit: true,
                allowResize: true,
                primary: false
            }, {
                property: 'active',
                label: this.$tc('sw-product.list.columnActive'),
                inlineEdit: 'boolean',
                allowInlineEdit: true,
                allowResize: true,
                align: 'center'
            }];
        },
    },

    watch: {
        listingCriteria() {
            this.getList();
        }
    },

    created() {
    },

    methods: {
        onChangeLanguage() {
            this.getList();
        },

        getList() {
            this.isLoading = true;

            this.getRepository.search(this.listingCriteria, Shopware.Context.api).then((items) => {
                this.total = items.total;
                this.records = items;
                this.isLoading = false;

                return items;
            }).catch(() => {
                this.isLoading = false;
            });
        },
        onFocus(evt) {
            evt.preventDefault();
            evt.target.select();
        },
        onDuplicate(referenceRecord) {
            this.record = referenceRecord;
            this.cloning = true;
        },
        onDuplicateFinish(duplicate) {
            this.cloning = false;
            this.record = null;

            this.$nextTick(() => {
                this.$router.push({name: 'lzyt.enev.detail', params: {id: duplicate.id}});
            });
        },
        onDuplicateCancel() {
            this.cloning = false;
            this.record = null;
        },
        onProductLink(item) {
            if(item.product === undefined){
                return;
            }
            this.$nextTick(() => {
                this.$router.push(this.getProductLink(item));
            });
        },
        getProductLink(item) {
            return {name: 'sw.product.detail.base', params: {id: item.product.id}}
        }
    }
});