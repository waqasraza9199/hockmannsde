import APLicenseService from '../services/license.service';

Shopware.Service().register('apLicenseService', (container) => {
    const initContainer = Shopware.Application.getContainer('init');
    return new APLicenseService(initContainer.httpClient, container.loginService);
});