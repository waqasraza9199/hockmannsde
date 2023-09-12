import deDE from '../../../module/articlequestions/snippet/de-DE.json';
import enGB from '../../../module/articlequestions/snippet/en-GB.json';

import template from './nimbits-articlequestions-list.html.twig';
import './nimbits-articlequestions-list.scss';

const {Component} = Shopware;
const {Criteria} = Shopware.Data;

Component.register('nimbits-articlequestions-list', {
    template,

    snippets: {
        'de-DE': deDE,
        'en-GB': enGB
    },

    inject: ['repositoryFactory'],

    data() {
        return {
            isLoading: false,
            criteria: null,
            repository: null,
            items: null,
            term: this.$route.query ? this.$route.query.term : null
        };
    },

    computed: {
        columns() {
            return [
                {
                    property: 'created_at',
                    dataIndex: 'created_at',
                    label: this.$tc('nb-articlequestions.list.column_created_at'),
                },
                {
                    property: 'active',
                    dataIndex: 'active',
                    label: this.$tc('nb-articlequestions.list.column_active'),
                },
                {
                    property: 'salutation',
                    dataIndex: 'salutation',
                    label: this.$tc('nb-articlequestions.list.column_salutation'),
                },
                {
                    property: 'firstname',
                    dataIndex: 'firstname',
                    label: this.$tc('nb-articlequestions.list.column_firstname'),
                },
                {
                    property: 'surname',
                    dataIndex: 'surname',
                    label: this.$tc('nb-articlequestions.list.column_surname'),
                },
                {
                    property: 'mail',
                    dataIndex: 'mail',
                    label: this.$tc('nb-articlequestions.list.column_mail'),
                },
                {
                    property: 'company',
                    dataIndex: 'company',
                    label: this.$tc('nb-articlequestions.list.column_company'),
                },
                {
                    property: 'question',
                    dataIndex: 'question',
                    label: this.$tc('nb-articlequestions.list.column_question'),
                },
                {
                    property: 'answer',
                    dataIndex: 'answer',
                    label: this.$tc('nb-articlequestions.list.column_answer'),
                }
            ];
        }
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.repository = this.repositoryFactory.create('nimbits_articlequestions');

            this.criteria = new Criteria();
            this.criteria.addSorting(Criteria.sort('createdAt', 'DESC'));

            if (this.term) {
                this.criteria.setTerm(this.term);
            }

            this.isLoading = true;

            this.repository
                .search(this.criteria, Shopware.Context.api)
                .then((result) => {
                    this.total = result.total;
                    this.items = result;
                    this.isLoading = false;
                });
        }/*,
        onSearch(term) {
            this.criteria.setTerm(term);
            this.$route.query.term = term;
            this.$refs.listing.doSearch();
        }*/
    }
});
