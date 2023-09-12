import template from './cogi-tags-list.html.twig';

const { Component, Mixin } = Shopware;
const { Criteria } = Shopware.Data;

Component.register('cogi-tags-list', {
    template,

    inject: [
        'repositoryFactory'
    ],

    mixins: [
        Mixin.getByName('notification')
    ],

    data() {
        return {
            showTagDeleteModal: false,
            showTagAddModal: false,
            toDeleteTagId: null,
            newTag: '',
            tags: null
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },

    computed: {
        tagRepository() {
            return this.repositoryFactory.create('tag');
        },

        columns() {
            return [{
                property: 'name',
                dataIndex: 'name',
                label: this.$t('cogi-tags.list.labelName'),
                routerLink: 'cogi.tags.detail',
                inlineEdit: 'string',
                allowResize: true,
                primary: true
            }, {
                property: 'id',
                dataIndex: 'id',
                routerLink: 'cogi.tags.detail',
                label: this.$tc('cogi-tags.list.id'),
                allowResize: true
            }, {
                property: 'createdAt',
                dataIndex: 'createdAt',
                label: this.$tc('cogi-tags.list.labelCreatedAt'),
                allowResize: true
            }];
        }
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.getList();
        },

        getList() {
            this.tagRepository
                .search(new Criteria(), Shopware.Context.api)
                .then((result) => {
                    this.tags = result;
                });
        },

        onStartTagDelete(tag) {
            this.toDeleteTagId = tag.id;
            this.onShowTagDeleteModal();
        },

        onConfirmTagDelete() {
            this.onCloseTagDeleteModal();


            this.tagRepository.delete(this.toDeleteTagId, Shopware.Context.api).then(() => {
                this.createNotificationSuccess({
                    title: this.$tc('global.default.success'),
                    message: this.$tc('cogi-tags.list.messageDeleteSuccess')
                });
                this.tagRepository
                    .search(new Criteria(), Shopware.Context.api)
                    .then((result) => {
                        this.tags = result;
                    });
            }).catch((exception) => {
                this.createNotificationError({
                    title: this.$tc('global.default.error'),
                    message: this.$tc('cogi-tags.list.messageDeleteError')
                });
            });
        },

        onSaveNewTag() {
            const tagCriteria = new Criteria();
            tagCriteria.addFilter(Criteria.equals('name', this.newTag));

            this.tagRepository
                .search(tagCriteria, Shopware.Context.api)
                .then((result) => {
                    if (result.total > 0) {
                        this.createNotificationError({
                            title: this.$tc('global.default.error'),
                            message: this.$tc('cogi-tags.list.messageAlreadyExistsError')
                        });
                    }
                    else {
                        const item = this.tagRepository.create(Shopware.Context.api);
                        item.name = this.newTag;
                        this.tagRepository.save(item, Shopware.Context.api).then(() => {
                            this.getList();
                            this.onCloseTagAddModal();

                            this.createNotificationSuccess({
                                title: this.$tc('global.default.success'),
                                message: this.$tc('cogi-tags.list.messageAddedSuccess')
                            });
                        }).catch(() => {
                            this.createNotificationError({
                                title: this.$tc('global.default.error'),
                                message: this.$tc('global.default.error')
                            });
                        });
                    }
                });
        },

        onExportToCSV() {
            let tagList = [];
            this.tags.forEach(function (item) {
                tagList.push(item);
            });

            let csvContent = "data:text/csv;charset=utf-8,";

            let list = ["ID", "Name", "Creation"];
            let row = list.join(",");
            csvContent += row + "\r\n";

            tagList.forEach(function (rowArray) {
                let list = [
                    rowArray.id,
                    rowArray.name,
                    rowArray.createdAt
                ];
                let row = list.join(",");
                csvContent += row + "\r\n";
            });

            let encodedUri = encodeURI(csvContent);
            let link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "tags.csv");
            document.body.appendChild(link);

            link.click();
        },

        onShowTagAddModal() {
            this.showTagAddModal = true;
        },

        onCloseTagAddModal() {
            this.showTagAddModal = false;
            this.newTag = '';
        },

        onCancelTagDelete() {
            this.toDeleteTagId = null;
            this.onCloseTagDeleteModal();
        },

        onShowTagDeleteModal() {
            this.showTagDeleteModal = true;
        },

        onCloseTagDeleteModal() {
            this.showTagDeleteModal = false;
        }
    }
});
