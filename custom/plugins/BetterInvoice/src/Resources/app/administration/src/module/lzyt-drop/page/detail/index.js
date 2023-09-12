import template from './index.html.twig';

const {Component, Mixin, Context} = Shopware;

Component.register('lzyt-drop-detail', {
    template,

    inject: [
        'repositoryFactory'
    ],

    mixins: [
        Mixin.getByName('notification')
    ],

    data() {
        return {
            repository: null,
            bundle: null,
            bundleId: null,
            isLoading: false,
            isSaveSuccessful: false,
            element: null,
            createPage: false
        };
    },

    created() {
        this.repository = this.repositoryFactory.create('lzyt_custom_drop');
        this.createdComponent();
    },

    watch: {
        '$route.params.id'() {
            this.createdComponent();
        }
    },

    computed: {
        bundleStore() {
            return this.repositoryFactory.create('lzyt_custom_drop');
        },
    },

    methods: {
        onSaveRule(ruleId) {
            this.bundle.ruleId = ruleId;
        },

        saveOnLanguageChange() {
            return this.onClickSave();
        },

        onChangeLanguage(languageId) {
            Context.api.languageId = languageId;
            this.createdComponent();
        },

        createdComponent() {
            if (this.$route.params.id) {
                this.bundleId = this.$route.params.id;
                if (this.bundle && this.bundle.isLocal) {
                    return;
                }

                this.loadEntityData();
            }
        },

        loadEntityData() {
            this.repository
                .get(this.bundleId, Context.api)
                .then((entity) => {
                    this.bundle = entity;
                });
        },

        async onClickSave() {
            this.isLoading = true;

            this.repository
                .save(this.bundle, Context.api)
                .then(() => {
                    this.createdComponent();
                    this.isLoading = false;
                    this.isSaveSuccessful = true;
                })
                .catch((exception) => {
                    this.isLoading = false;
                    this.createNotificationError({
                        title: this.$tc('lzyt-drop.notification.errorSave'),
                        message: exception
                    });
                });
        },

        saveFinish() {
            this.isSaveSuccessful = false;
        }
    }
});