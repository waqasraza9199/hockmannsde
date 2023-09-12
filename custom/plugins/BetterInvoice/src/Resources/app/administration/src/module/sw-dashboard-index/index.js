import template from './sw-dashboard-index.html.twig';
import './sw-dashboard-index.scss';

const { Component } = Shopware;

Component.override('sw-dashboard-index', {
    inject: ['biLicenseService'],

    template,

    data() {
        return {
            biLicenseDuration: '',
            biLicenseValidUntil: new Date(),
            biIsFetchingLicense: false,

        }
    },

    created() {
        this.syncBILicense();
    },

    methods: {
        async syncBILicense() {
            this.biIsFetchingLicense = true;
            const license = await this.biLicenseService.sync();

            this.biLicenseValidUntil = new Date(license.validUntil.date);

            let month = this.biLicenseValidUntil.getMonth() + 1;
            if (month < 10)
                month = '0' + month;

            let day = this.biLicenseValidUntil.getDate();
            if (day < 10)
                day = '0' + day;

            this.biLicenseDuration = `${day}.${month}.${this.biLicenseValidUntil.getFullYear()}`;
            this.biIsFetchingLicense = false;
        },
        async onSyncBILicense(e) {
            this.syncBILicense();
        }
    },

    computed: {
        isBILicenseValid() {
            return this.biLicenseValidUntil > new Date();
        }
    }
});