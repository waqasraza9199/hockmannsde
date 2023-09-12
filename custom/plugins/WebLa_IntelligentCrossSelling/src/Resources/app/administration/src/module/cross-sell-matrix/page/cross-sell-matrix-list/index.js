import template from './cross-sell-matrix-list.html.twig';
import './cross-sell-matrix-list.scss';
const {
    Component,
    Context,
    Mixin
} = Shopware;

const {
    Criteria
} = Shopware.Data;

Component.register('cross-sell-matrix-list', {
    template,

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },

    inject: {
        repositoryFactory: 'repositoryFactory'
    },

    mixins: [
        Mixin.getByName('notification')
    ],

    data: function() {
        return {
            items: null,
            dataSource: undefined,
            isLoading: false,
            columns: [{
                    property: 'property_group.translated.name',
                    label: this.$tc('cross-sell-matrix.entity.propertyGroup'),
                },
                {
                    property: 'weight',
                    label: this.$tc('cross-sell-matrix.entity.weight'),
                    inlineEdit: 'number'
                }
            ],
            codeDeleteModal: null,
            settings: null
        }
    },
    computed: {
        crossSellingPropertyGroupRepository() {
            return this.repositoryFactory.create('webla_intelligent_cross_selling_property_group');
        },
        crossSellingPropertyGroupCriteria() {
            const criteria = new Criteria();
            criteria.addAssociation('property_group');
            return criteria;
        },
        crossSellingSettingsRepository() {
            return this.repositoryFactory.create('webla_intelligent_cross_selling_settings');
        },
        crossSellingSettingsCriteria() {
            const criteria = new Criteria();
            criteria.setLimit(1);
            return criteria;
        }
    },
    created() {
        this.isLoading = true;
    },
    mounted() {
        this.createdComponent();
    },
    methods: {
        async createdComponent() {
            this.isLoading = true;
            await this.getList();
            await this.getSettings();
            this.isLoading = false;
        },
        async getList() {
            try {
                const items = await this.crossSellingPropertyGroupRepository.search(this.crossSellingPropertyGroupCriteria, Context.api)
                this.dataSource = items;
                this.items = items;
            } catch {
                this.createNotificationError({
                    message: this.$tc('cross-sell-matrix.list.messageSaveErrorLoad')
                });
            }
        },
        async getSettings() {
            try {
                const items = await this.crossSellingSettingsRepository.search(this.crossSellingSettingsCriteria, Context.api)
                if (items.total === 0) {
                    this.settings = await this.crossSellingSettingsRepository.create(Context.api)
                    this.settings.title = "";
                    this.settings.maxProducts = 5;
                    this.settings.active = false;
                    this.settings.showTitle = true;
                    this.settings.onlyCategory = false;
                } else {
                    this.settings = items[0]
                }
            } catch {
                this.createNotificationError({
                    message: this.$tc('cross-sell-matrix.list.messageSaveErrorLoad')
                });
            };
        },
        async onSave() {
            try {
                const res = await this.crossSellingSettingsRepository.save(this.settings, Context.api);
                this.getSettings();
                this.createNotificationSuccess({
                    message: this.$tc('cross-sell-matrix.list.messageSaveSuccess')
                })
            } catch (e) {
                console.log(e);
                this.createNotificationError({
                    message: this.$tc('cross-sell-matrix.list.messageSaveErrorGeneral')
                });
            }
        },
        async saveItem(item) {
            try {
                await this.crossSellingPropertyGroupRepository.save(item, Context.api);
                this.getList();
            } catch (e) {
                this.createNotificationError({
                    message: this.$tc('cross-sell-matrix.list.messageSaveErrorGeneral')
                });
            }
        },
        async deleteRule() {
            await this.crossSellingPropertyGroupRepository.delete(this.codeDeleteModal.id, Context.api);
            this.onCloseDeleteModal();
            await this.getList();
        },
        onShowDeleteModal(item) {
            this.codeDeleteModal = item;
        },
        onCloseDeleteModal() {
            this.codeDeleteModal = null;
        },
    }
});