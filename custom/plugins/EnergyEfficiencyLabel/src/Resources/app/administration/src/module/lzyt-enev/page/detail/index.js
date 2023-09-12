import template from './index.html.twig';
import './index.scss';

const {Application, Component, Mixin, Context} = Shopware;

Component.register('lzyt-enev-detail', {
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
            mediaItem: null,
            datasheetItem: null,
            iconItem: null,
            isLoading: false,
            isSaveSuccessful: false,
            element: null,
            createPage: false,
            variation: []
        };
    },

    created() {
        this.repository = this.repositoryFactory.create('lzyt_enev');

        this.createdComponent();
    },

    watch: {
        '$route.params.id'() {
            this.createdComponent();
        }
    },

    computed: {
        productStore() {
            return this.repositoryFactory.create('product');
        },

        mediaStore() {
            return this.repositoryFactory.create('media');
        },

        bundleStore() {
            return this.repositoryFactory.create('lzyt_enev');
        },

        mediaUploadTag() {
            return `lzyt-enev-detail--${this.bundle.id}--media`;
        },

        datasheetUploadTag() {
            return `lzyt-enev-detail--${this.bundle.id}--datasheet`;
        },

        iconUploadTag() {
            return `lzyt-enev-detail--${this.bundle.id}--icon`;
        },

        productSelectContext() {
            return {
                ...Shopware.Context.api,
                inheritance: true
            };
        },
    },

    methods: {
        saveOnLanguageChange() {
            return this.onClickSave();
        },

        onChangeLanguage(languageId) {
            Shopware.Context.api.languageId = languageId;
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
                .get(this.bundleId, Shopware.Context.api)
                .then((entity) => {
                    this.bundle = this.prepareData(entity);
                });
        },

        prepareData(entity) {
            entity.color = entity.color.toLowerCase();

            return entity;
        },

        setMediaItem(selection) {
            this.setMediaIdFromSelection(selection, 'media');
        },

        setDatasheetItem(selection) {
            this.setMediaIdFromSelection(selection, 'datasheet');
        },

        setIconItem(selection) {
            this.setMediaIdFromSelection(selection, 'icon');
        },

        /**
         * helper to set mediaId and mediaitems
         *
         * @param selection
         * @param key
         */
        setMediaIdFromSelection(selection, key) {
            let mediaId = this.getMediaIdFromSelection(selection);

            if (!mediaId) {
                this.remove(key);

                return;
            }

            this.bundle[key + 'Id'] = mediaId;
        },

        /**
         * helper to get mediaId from selection
         * @param selection
         * @return {*}
         */
        getMediaIdFromSelection(selection) {
            let mediaId;
            if (selection.targetId !== undefined) {
                mediaId = selection.targetId;
            } else if (selection[0].id !== undefined) {
                mediaId = selection[0].id;
            }

            return mediaId;
        },

        onDropMedia(dragData) {
            this.setMediaItem({targetId: dragData.id});
        },

        remove(key) {
            this.bundle[key + 'Id'] = null;
        },

        onProductChange(productId) {
            this.$emit('element-update', this.element);
        },

        async onClickSave() {
            this.isLoading = true;

            this.repository
                .save(this.bundle, Shopware.Context.api)
                .then(() => {
                    this.createdComponent();
                    this.isLoading = false;
                    this.isSaveSuccessful = true;
                })
                .catch((exception) => {
                    this.isLoading = false;
                    this.createNotificationError({
                        title: this.$tc('lzyt-enev.notification.errorSave'),
                        message: exception
                    });
                });
        },

        onChangeClass(value){
            this.bundle.class = value;
        },

        onChangeColor(value){
            this.bundle.color = value;
        },

        onChangeSpectrumFrom(value){
            this.bundle.spectrumFrom = value;
        },

        onChangeSpectrumTo(value){
            this.bundle.spectrumTo = value;
        },

        saveFinish() {
            this.isSaveSuccessful = false;
        }
    }
});