import template from './index.html.twig';
import './index.scss';
const { Component, StateDeprecated } = Shopware;

Component.register('lzyt-enev-clone-modal', {
    template,

    inject: ['repositoryFactory'],

    props: {
        record: {
            required: false
        }
    },

    data() {
        return {
            product: null,
            productId: null,
            enevOptions: null,
            isLoading: false
        };
    },

    computed: {
        productStore() {
            return this.repositoryFactory.create('product');
        },
        repository() {
            return this.repositoryFactory.create('lzyt_enev');
        },
        disabledSave(){
            return this.productId === null || this.isLoading;
        }
    },

    methods: {
        onProductChange(productId) {
            this.productId = productId;
            this.$emit('element-update', this.productId);
        },
        onCancel() {
            this.productId = null;
            this.product = null;
            this.enevOptions = null;
            this.$emit('clone-cancel');
        },
        async onSave() {
            this.isLoading = true;
            this.cloneenev().then((gbmedenev) => {
                this.isLoading = false;
                this.$emit('clone-finish', { id: gbmedenev.id });
            });
        },
        cloneenev() {
            const behavior = {
                overwrites: {
                    productId: this.productId,
                    active: false,
                    createdAt: null
                }
            };

            return this.repository
                .clone(this.record.id, Shopware.Context.api, behavior)
                .then((clone) => {
                    return clone;
                });
        }
    }
});
