import BILicenseService from '../services/license.service';

Shopware.Service().register('biLicenseService', (container) => {
    const initContainer = Shopware.Application.getContainer('init');
    return new BILicenseService(initContainer.httpClient, container.loginService);
});