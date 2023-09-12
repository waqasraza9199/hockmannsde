import template from './sw-product-detail-custom.html.twig';

const { Component } = Shopware;
const { mapState, mapGetters } = Component.getComponentHelper();

Component.register('sw-product-detail-lenzgoogleshopping', {
    template,
    inject: ['repositoryFactory', 'feature'],

    data() {
        return {
            googleTaxonomyRepository: null,
            activeValue: false,
            parentActiveValue: false,
            googleTaxonomyValue: '',
            parentGoogleTaxonomyValue: '',
            taxonomyResultLimit: 5,
            conditionOptions: [
                {id: '', name: this.$tc('lenz-google-shopping.detail.unsetValueName')},
                {id: "new", name: this.$tc('lenz-google-shopping.detail.conditionNewName')},
                {id: "refurbished", name: this.$tc('lenz-google-shopping.detail.conditionRefurbishedName')},
                {id: "used", name: this.$tc('lenz-google-shopping.detail.conditionUsedName')},
            ],
            energyEfficiencyClassOptions: [
                {
                    id: '',
                    name: this.$tc('lenz-google-shopping.detail.unsetValueName')
                },
                {
                    id: "A+++",
                    name: "A+++"
                },
                {
                    id: "A++",
                    name: "A++"
                },
                {
                    id: "A+",
                    name: "A+"
                },
                {
                    id: "A",
                    name: "A"
                },
                {
                    id: "B",
                    name: "B"
                },
                {
                    id: "C",
                    name: "C"
                },
                {
                    id: "D",
                    name: "D"
                },
                {
                    id: "E",
                    name: "E"
                },
                {
                    id: "F",
                    name: "F"
                },
                {
                    id: "G",
                    name: "G"
                }
            ],
            minEnergyEfficiencyClassOptions: [
                {
                    id: '',
                    name: this.$tc('lenz-google-shopping.detail.unsetValueName')
                },
                {
                    id: "A+++",
                    name: "A+++"
                },
                {
                    id: "A++",
                    name: "A++"
                },
                {
                    id: "A+",
                    name: "A+"
                },
                {
                    id: "A",
                    name: "A"
                },
                {
                    id: "B",
                    name: "B"
                },
                {
                    id: "C",
                    name: "C"
                },
                {
                    id: "D",
                    name: "D"
                },
                {
                    id: "E",
                    name: "E"
                },
                {
                    id: "F",
                    name: "F"
                },
                {
                    id: "G",
                    name: "G"
                }
            ],
            maxEnergyEfficiencyClassOptions: [
                {
                    id: '',
                    name: this.$tc('lenz-google-shopping.detail.unsetValueName')
                },
                {
                    id: "A+++",
                    name: "A+++"
                },
                {
                    id: "A++",
                    name: "A++"
                },
                {
                    id: "A+",
                    name: "A+"
                },
                {
                    id: "A",
                    name: "A"
                },
                {
                    id: "B",
                    name: "B"
                },
                {
                    id: "C",
                    name: "C"
                },
                {
                    id: "D",
                    name: "D"
                },
                {
                    id: "E",
                    name: "E"
                },
                {
                    id: "F",
                    name: "F"
                },
                {
                    id: "G",
                    name: "G"
                }
            ],
            ageGroupOptions: [
                {
                    id: '',
                    name: this.$tc('lenz-google-shopping.detail.unsetValueName')
                },
                {
                    id: "newborn",
                    name: this.$tc('lenz-google-shopping.detail.ageGroupNewbornName')
                },
                {
                    id: "infant",
                    name: this.$tc('lenz-google-shopping.detail.ageGroupInfantName')
                },
                {
                    id: "toddler",
                    name: this.$tc('lenz-google-shopping.detail.ageGroupToddlerName')
                },
                {
                    id: "kids",
                    name: this.$tc('lenz-google-shopping.detail.ageGroupKidsName')
                },
                {
                    id: "adult",
                    name: this.$tc('lenz-google-shopping.detail.ageGroupAdultName')

                }
            ],
            genderOptions: [
                {
                    id: '',
                    name: this.$tc('lenz-google-shopping.detail.unsetValueName')
                },
                {
                    id: "male",
                    name: this.$tc('lenz-google-shopping.detail.genderMaleName')
                },
                {
                    id: "female",
                    name: this.$tc('lenz-google-shopping.detail.genderFameleName')
                },
                {
                    id: "unisex",
                    name: this.$tc('lenz-google-shopping.detail.genderUnisexName')
                }
            ],
            sizeTypeOptions: [
                {
                    id: '',
                    name: this.$tc('lenz-google-shopping.detail.unsetValueName')
                },
                {
                    id: "regular",
                    name: this.$tc('lenz-google-shopping.detail.sizeTypeRegularName')
                },
                {
                    id: "petite",
                    name: this.$tc('lenz-google-shopping.detail.sizeTypePetiteName')
                },
                {
                    id: "oversize",
                    name: this.$tc('lenz-google-shopping.detail.sizeTypeOversizeName')
                },
                {
                    id: "maternity",
                    name: this.$tc('lenz-google-shopping.detail.sizeTypeMaternityName')
                }
            ],
            sizeSystemOptions: [
                {
                    id: '',
                    name: this.$tc('lenz-google-shopping.detail.unsetValueName')
                },
                {
                    id: "US",
                    name: "US"
                },
                {
                    id: "UK",
                    name: "UK"
                },
                {
                    id: "EU",
                    name: "EU"
                },
                {
                    id: "DE",
                    name: "DE"
                },
                {
                    id: "FR",
                    name: "FR"
                },
                {
                    id: "JP",
                    name: "JP"
                },
                {
                    id: "CN (China)",
                    name: "CN (China)"
                },
                {
                    id: "IT",
                    name: "IT"
                },
                {
                    id: "BR",
                    name: "BR"
                },
                {
                    id: "MEX",
                    name: "MEX"
                },
                {
                    id: "AU",
                    name: "AU"
                }
            ],
            destinationOptions: [
                {
                    id: '',
                    name: this.$tc('lenz-google-shopping.detail.unsetValueName')
                },
                {
                    id: "Shopping ads",
                    name: this.$tc('lenz-google-shopping.detail.destinationShoppingAdvertName')
                },
                {
                    id: "Shopping Actions",
                    name: this.$tc('lenz-google-shopping.detail.destinationShoppingActionsName')
                },
                {
                    id: "Display ads",
                    name: this.$tc('lenz-google-shopping.detail.destinationDisplayAdsName')
                },
                {
                    id: "Local inventory ads",
                    name: this.$tc('lenz-google-shopping.detail.destinationAdsWithLocalInventoryName')
                },
                {
                    id: "Surfaces across Google",
                    name: this.$tc('lenz-google-shopping.detail.destinationGooglePlatformsName')
                },
                {
                    id: "Local surfaces across Google",
                    name: this.$tc('lenz-google-shopping.detail.destinationLocalGooglePlatformsName')
                },
            ]
        }
    },

    created() {
        this.googleTaxonomyRepository = this.repositoryFactory.create('lenz_google_shopping_taxonomy');
        this.initProduct();
        this.initParentProduct();
    },

    watch: {
        product(val) {
            this.initProduct();
        },
        parentProduct(val) {
            this.initParentProduct();
        },
        activeValue(val) {
            if(val === null || val === true) {
                this.product.customFields.lenz_google_shopping_active = val;
            } else {
                this.product.customFields.lenz_google_shopping_active = false;
            }
        },

        googleTaxonomyValue(val) {
            var me = this;
            this.product.customFields.lenz_google_shopping_category = val;

            this.googleTaxonomyRepository
                .get(val, Shopware.Context.api)
                .then((entity) => {
                    if(entity === null) {
                        me.product.customFields.lenz_google_shopping_category_number = null;
                    } else {
                        me.product.customFields.lenz_google_shopping_category_number = entity.catId;
                    }
                });
        }
    },

    methods: {
        initProduct() {
            if (this.product.length === undefined) {
                return;
            }
            this.initCustomFieldsForProduct();

            if(this.product.customFields.lenz_google_shopping_active === true || this.product.customFields.lenz_google_shopping_active === "1") {
                this.activeValue = true;
            } else if(this.product.customFields.lenz_google_shopping_active === null) {
                this.activeValue = null;
            } else {
                this.activeValue = false;
            }

            if(this.product.customFields.lenz_google_shopping_adult === true || this.product.customFields.lenz_google_shopping_adult === "1") {
                this.product.customFields.lenz_google_shopping_adult = true;
            } else if(this.product.customFields.lenz_google_shopping_adult === null) {
                this.product.customFields.lenz_google_shopping_adult = null
            } else {
                this.product.customFields.lenz_google_shopping_adult = false
            }

            if(this.product.customFields.lenz_google_shopping_is_bundle === true || this.product.customFields.lenz_google_shopping_is_bundle === "1") {
                this.product.customFields.lenz_google_shopping_is_bundle = true;
            } else if(this.product.customFields.lenz_google_shopping_is_bundle === null) {
                this.product.customFields.lenz_google_shopping_is_bundle = null
            } else {
                this.product.customFields.lenz_google_shopping_is_bundle = false
            }

            this.googleTaxonomyValue = this.product.customFields.lenz_google_shopping_category;
        },

        initParentProduct() {
            if (this.parentProduct.length === undefined) {
                return;
            }
            this.initCustomFieldsForParentProduct();

            if(this.parentProduct.customFields.lenz_google_shopping_active === true || this.parentProduct.customFields.lenz_google_shopping_active === "1") {
                this.parentActiveValue = true;
            } else {
                this.parentActiveValue = false;
            }

            if(this.parentProduct.customFields.lenz_google_shopping_adult === true || this.parentProduct.customFields.lenz_google_shopping_adult === "1") {
                this.parentProduct.customFields.lenz_google_shopping_adult = true;
            } else {
                this.parentProduct.customFields.lenz_google_shopping_adult = false
            }

            if(this.parentProduct.customFields.lenz_google_shopping_is_bundle === true || this.parentProduct.customFields.lenz_google_shopping_is_bundle === "1") {
                this.parentProduct.customFields.lenz_google_shopping_is_bundle = true;
            } else {
                this.parentProduct.customFields.lenz_google_shopping_is_bundle = false
            }

            this.parentGoogleTaxonomyValue = this.parentProduct.customFields.lenz_google_shopping_category;
        },

        initCustomFieldsForProduct() {
            if(this.product.customFields === null) {
                this.product.customFields = {};
            }

            if(this.product.customFields.lenz_google_shopping_active === undefined) {
                this.product.customFields.lenz_google_shopping_active = null;
            }

            if(this.product.customFields.lenz_google_shopping_category === undefined) {
                this.product.customFields.lenz_google_shopping_category = null;
            }
        },

        initCustomFieldsForParentProduct() {
            if(this.parentProduct.customFields === null) {
                this.parentProduct.customFields = {};
            }

            if(this.parentProduct.customFields.lenz_google_shopping_active === undefined) {
                this.parentProduct.customFields.lenz_google_shopping_active = null;
            }

            if(this.parentProduct.customFields.lenz_google_shopping_category === undefined) {
                this.parentProduct.customFields.lenz_google_shopping_category = null;
            }
        },

        stripHtml(string) {
            let regex = /(<([^>]+)>)/ig;

            return string.replace(regex, '');
        },

        onChangeActiveState() {
            this.product.customFields.lenz_google_shopping_active = arguments[0];
        },

        onChangeGoogleTaxonomy() {
            if(arguments[0] === null) {
                this.product.customFields.lenz_google_shopping_category_number = null;
                return;
            }

            if(arguments[1] !== undefined) {
                this.product.customFields.lenz_google_shopping_category_number = arguments[1].catId;
                return;
            }
        },

        itemGroupIdPlaceholder() {
            if(this.parentProduct.id !== undefined) {
                return this.stripHtml(this.parentProduct.productNumber)
            }

            return this.stripHtml(this.product.productNumber)
        }
    },

    computed: {
        ...mapState('swProductDetail', [
            'product',
            'parentProduct',
            'loading',
            'apiContext',
        ]),

        ...mapGetters('swProductDetail', [
            'isLoading'
        ])
    },

    metaInfo() {
        return {
            title: 'Produktdaten (Export)'
        };
    },
});
