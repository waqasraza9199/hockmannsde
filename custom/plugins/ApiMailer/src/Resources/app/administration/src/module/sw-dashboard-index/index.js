import template from './sw-dashboard-index.html.twig';
import './sw-dashboard-index.scss';

const { Component } = Shopware;

Component.override('sw-dashboard-index', {
    inject: ['apLicenseService'],

    template,

    data() {
        return {
            apLicenseDuration: '',
            apLicenseValidUntil: new Date(),
            apIsFetchingLicense: false,
        }
    },

    created() {
        this.syncAPLicense();
    },

    methods: {
        async syncAPLicense() {
            this.apIsFetchingLicense = true;
            const license = await this.apLicenseService.sync();

            this.apLicenseValidUntil = new Date(license.validUntil.date);

            let month = this.apLicenseValidUntil.getMonth() + 1;
            if (month < 10)
                month = '0' + month;

            let day = this.apLicenseValidUntil.getDate();
            if (day < 10)
                day = '0' + day;

            this.apLicenseDuration = `${day}.${month}.${this.apLicenseValidUntil.getFullYear()}`;
            this.apIsFetchingLicense = false;
        },
        async onSyncAPLicense(e) {
            this.syncAPLicense();
        }
    },

    computed: {
        isAPLicenseValid() {
            return this.apLicenseValidUntil > new Date();
        }
    }
});