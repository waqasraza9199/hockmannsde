import template from './index.html.twig';

const { Component, Mixin } = Shopware;
const { debounce, get } = Shopware.Utils;

Component.register('lzyt-enev-select-class', {
    template,

    inject: [
        'systemConfigApiService'
    ],

    props: {
        value: {
            type: String,
            required: false,
            default: null
        },
        label: {
            type: String,
            required: true
        },
    },

    data() {
        return {
            options: [],
            itemSelected: null,
            isLoading: true
        };
    },

    computed: {
        currentValue: {
            get() {
                return this.value;
            },
            set(newValue) {
                this.$emit('input', newValue);
                this.$emit('change', newValue);
            }
        }
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.getOptions();
        },
        getOptions() {
            this.isLoading = true;
            this.systemConfigApiService.getValues('LzytEnev.config').then((response) => {
                this.options = this.prepareConfig(response['LzytEnev.config.class']);
                this.isLoading = false;
            });
        },
        prepareConfig(value) {
            let values = value.split(';'),
                data = [{
                    name: this.$tc('lzyt-enev.component.empty'),
                    id: null,
                }];

            values.forEach((item) => {
                data.push({
                    name: item.toUpperCase(),
                    id: item.toLowerCase(),
                });
            });

            return data;
        },
    }
});