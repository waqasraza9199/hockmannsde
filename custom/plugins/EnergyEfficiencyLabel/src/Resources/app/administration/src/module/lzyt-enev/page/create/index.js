const {Component} = Shopware;

Component.extend('lzyt-enev-create', 'lzyt-enev-detail', {
    data() {
        return {
            repository: null,
            bundle: null,
            bundleId: null,
            mediaItem: null,
            datasheetItem: null,
            iconItem: null,
            isLoading: false,
            isSaveSuccessful: false,
            element: null,
            createPage: true
        };
    },
    methods: {
        createdComponent() {
            this.bundle = this.repository.create(Shopware.Context.api);
            this.bundle.productId = this.$route.params.productId !== undefined ? this.$route.params.productId : null;
        },

        async onClickSave() {
            this.isLoading = true;

            this.repository
                .save(this.bundle, Shopware.Context.api)
                .then(() => {
                    this.isLoading = false;
                    this.$router.push({name: 'lzyt.enev.detail', params: {id: this.bundle.id}});
                }).catch((exception) => {
                this.isLoading = false;

                this.createNotificationError({
                    title: this.$tc('lzyt-enev.notification.errorSave'),
                    message: exception
                });
            });
        },
    }
});