const {Component} = Shopware;

Component.extend('lzyt-drop-create', 'lzyt-drop-detail', {
    data() {
        return {
            repository: null,
            bundle: null,
            isLoading: false,
            isSaveSuccessful: false,
            element: null,
            createPage: true
        };
    },
    methods: {
        createdComponent() {
            this.bundle = this.repository.create(Shopware.Context.api);
            this.bundle.enabled = false;
        },

        async onClickSave() {
            this.isLoading = true;

            this.repository
                .save(this.bundle, Shopware.Context.api)
                .then(() => {
                    this.isLoading = false;
                    this.$router.push({name: 'lzyt.drop.detail', params: {id: this.bundle.id}});
                }).catch((exception) => {
                this.isLoading = false;

                this.createNotificationError({
                    title: this.$tc('lzyt-drop.notification.errorSave'),
                    message: exception
                });
            });
        },
    }
});