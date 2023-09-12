import template from './index.html.twig';
import './index.scss';

const {Component} = Shopware;

Component.register('lzyt-enev-arrow', {
    template,
    props: {
        arrow: {
            type: Object,
            required: true
        }
    },
    beforeCreate() {
        this.$options.filters.style = this.$options.filters.style.bind(this);
        this.$options.filters.prefix = this.$options.filters.prefix.bind(this);
    },
    created() {
        if(this.arrow.id === null){
            this.arrow = Object.assign(this.arrow, {
                id: 'e1e1e1',
                class: this.arrow.name,
                color: 'e1e1e1',
            });
        }
    },
    methods: {
        length(value) {
            return value.length;
        },
        prefix(value) {
            return '#' + value.replace('#', '');
        }
    },
    filters: {
        prefix(value) {
            return this.prefix(value);
        },
        style(value) {
            return 'background-color: ' + this.prefix(value) + '; color: ' + this.prefix(value);
        }
    }
});