import template from './cogi-tags-detail.html.twig';

const { Component, Mixin } = Shopware;

Component.register('cogi-tags-detail', {
    template,

    inject: [
        'repositoryFactory'
    ],

    mixins: [
        Mixin.getByName('notification')
    ],

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },

    data() {
        return {
            tag: null,
            isLoading: false,
            processSuccess: false,
            repository: null
        };
    },

    created() {
        this.repository = this.repositoryFactory.create('tag');
        this.getTag();
    },

    methods: {
        getTag() {
            this.repository
                .get(this.$route.params.id, Shopware.Context.api)
                .then((entity) => {
                    this.tag = entity;
                });
        },

        onClickSave() {
            this.isLoading = true;

            this.repository
                .save(this.tag, Shopware.Context.api)
                .then(() => {
                    this.getTag();
                    this.isLoading = false;
                    this.processSuccess = true;
                }).catch((exception) => {
                    this.isLoading = false;
                    this.createNotificationError({
                        title: this.$t('cogi-tags.detail.errorTitle'),
                        message: exception
                    });
                });
        },

        saveFinish() {
            this.processSuccess = false;
        }
    }
});
