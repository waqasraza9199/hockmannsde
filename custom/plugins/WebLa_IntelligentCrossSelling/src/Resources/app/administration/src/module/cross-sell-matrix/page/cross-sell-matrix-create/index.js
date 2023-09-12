import template from './cross-sell-matrix-create.html.twig';
import './cross-sell-matrix-create.scss';

const { Component, Mixin } = Shopware;
const { mapPropertyErrors } = Shopware.Component.getComponentHelper();

Component.register('cross-sell-matrix-create', {
    template,

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },

    inject: [
        'repositoryFactory',
        'systemConfigApiService',
    ],

    mixins: [
        Mixin.getByName('notification'),
    ],

    data() {
        return {
            id: null,
            item: {
                propertyGroupId: null,
                weight: 1,
            },
            isLoading: false,
            isSaveSuccessful: false,
            config: {}
        };
    },

    computed: {
        crossSellingPropertyGroupRepository() {
            return this.repositoryFactory.create('webla_intelligent_cross_selling_property_group');
        },
    },

    watch: {

    },

    created() {
        this.createItem();
    },

    methods: {
        createItem() {
            this.isLoading = true;
            this.item = this.crossSellingPropertyGroupRepository.create(Shopware.Context.api);
            this.isLoading = false;
        },

        onSave() {

            this.isSaveSuccessful = false;
            this.isLoading = true;

            this.crossSellingPropertyGroupRepository.save(this.item, Shopware.Context.api)
                .then(() => {

                    this.isSaveSuccessful = true;
                    this.isLoading = false;
                    this.$router.push({ name: 'cross.sell.matrix.index' });
                }).catch((error) => {
                    console.log(error);
                    this.createNotificationError({
                        message: this.$tc('cross-sell-matrix.create.messageSaveError'),
                    });
                    this.isLoading = false;
                });
        },

        onCancel() {
            this.$router.push({ name: 'cross.sell.matrix.index' });
        },

    },
});
